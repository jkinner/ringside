<?php

/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 *
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attribution
 * statements applied by the authors.  All third-party contributions are
 * distributed under license by Ringside Networks, Inc.
 *
 * This is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of
 * the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free
 * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
 ******************************************************************************/

include_once 'ringside/m3/util/Settings.php';
include_once 'ringside/social/dsl/TagMetaInfo.php';

class Social_Dsl_TagRegistry
{
	/**
	 * Singleton instance.
	 */
	protected static $m_instance;
	
	/**
	 * A list of paths (relative to the base of
	 * an entry in include_path) which will always
	 * contain tag handlers.
	 */
	protected static $m_fixedSearchPaths;
	
	/**
	 * Loaded flavor configuration.
	 */
	public static $flavors;
	
	/**
	 * List of directories already visited by the
	 * tag registry that potentially contain tag handlers.
	 */
	protected $m_visitedDirs;
	
	/**
	 * List of classes already visited by the
	 * tag registry.
	 */
	protected $m_visitedClasses;
	
	/**
	 * Array of array, key is namespace, value is
	 * an array of TagMetaInfo objects with the tag
	 * name as keys.
	 */
	protected $m_tagMetaInfo;

	/**
	 * Array of dynamically created tidy configuration options.
	 */
	protected $m_tidyConfig;
	
	/**
	 * Whether or not to do caching.
	 */
	protected $m_doCache;
	
	/**
	 * Whether or not to scan for new tags.
	 */
	protected $m_doScan;
	
	
	public function __construct($doCache = false, $doScan = true)
	{
		$this->clearData();
		$this->m_doCache = $doCache;
		$this->m_doScan = $doScan;
		$this->initialize();
	}
	
	protected function initialize()
	{	
		if ($this->m_doCache) {			 
			$fname = self::getCacheFileName();
			if (!file_exists($fname)) {
				//$type = 'scan-cache';
				//$t1 = microtime(true);
				$this->scanIncludePath();
				//write to cache file
				$this->toCacheFile($fname);
			} else {
				//$type = 'load-cache';
				//$t1 = microtime(true);
				//try to load from cache file
				if (!$this->loadCacheFile($fname)) {
					error_log('[TagRegistry] warning: failed to load from cache, deleting cache and scanning...');
					self::removeCacheFile();
					$this->scanIncludePath();
				}
			}
		} else {
			//$type = 'scan';
			//$t1 = microtime(true);
			//re-initialize registry
			$this->scanIncludePath();
		}
		//$t2 = microtime(true);
		//$diff = round(($t2 - $t1)*1e6);		
		//error_log("$type: {$diff}us");
		//initialize flavor savers
		self::initFlavors();
	}
	
	protected function clearData()
	{
		$this->m_visitedDirs = array();
		$this->m_visitedClasses = array();		
		$this->m_tagMetaInfo = array();
		$this->m_tidyConfig = NULL;
	}
	
	/**
     * Loads a cache file into a given instance.
     */
	public function loadCacheFile($fname)
	{
		$this->clearData();
		
		$str = file_get_contents($fname);
		if ($str === false) {
			error_log("[TagRegistry] could not read cache file '$fname'");
			return false;
		}
		try {
			$tarr = explode("\n", $str);
			foreach ($tarr as $tstr) {			
				if (strlen($tstr) > 0) {			
					//tag meta info, parse accordingly
					$tmi = Social_Dsl_TagMetaInfo::fromString($tstr);
						
					//check timestamp					
					$fpath = $tmi->getHandlerSourceFile();
					$modTime = filemtime($fpath);
					
					//error_log("$fpath: modTime=$modTime, last=" . $tmi->getLastModified());
					
					if ($modTime != $tmi->getLastModified()) {
						error_log("[TagRegistry] file contents changed for '$fpath', deleting cache..");
						$this->loadTagHandlerMetaInfo($path, true);		
						self::removeCacheFile();
					}
					
					$tns = $tmi->getTagNamespace();						
					if (!isset($this->m_tagMetaInfo[$tns])) $this->m_tagMetaInfo[$tns] = array();
					
					$this->m_tagMetaInfo[$tns][$tmi->getTagName()] = $tmi;
				}
			}
		} catch (Exception $e) {
			error_log("[TagRegistry] problem reading cache file '$fname': " . $e->getMessage());
			return false;
		}
		return true;
	}
		
	/**
 	 * Write the tag meta info to a cache file.
 	 */
	public function toCacheFile($fname)
	{
		$str = '';
		foreach ($this->m_tagMetaInfo as $tns => $tarr) {	
			foreach ($tarr as $tname => $tmi) {
				$str .= $tmi->toString() . "\n";
			}
		}
		if (file_put_contents($fname, $str) === FALSE) {
			throw new Exception("[TagRegistry] could not write to file '$fname'");
		}
		error_log("[TagRegistry] wrote cache to '$fname'");
	}
	
	/**
     * Initialize flavors and the rules associated with them.
     */
	public function initFlavors()
	{
		if (!isset(self::$flavors)) {
			self::$flavors = array();
            
            $default_flavor_config = array('order' => 'allow,deny',
            							   'deny' => 'fb:profile-action,fb:mobile');
            
            self::$flavors['sidebar'] = &$default_flavor_config;
            self::$flavors['menu'] = &$default_flavor_config;
            self::$flavors['canvas'] = &$default_flavor_config;
            self::$flavors['wide'] = &$default_flavor_config;
            self::$flavors['narrow'] = &$default_flavor_config;
            self::$flavors['mobile-content'] = &$default_flavor_config;
            self::$flavors['profile-action'] = &$default_flavor_config;
            
            // Set the state of the output buffer when the flavor starts
            self::$flavors['profile-actions']['start'] = false;
            self::$flavors['profile-actions']['order'] = 'deny,allow';
            // fb:profile-action acts as a flavor, allowing other tags to be rendered once it is encountered in a profile-actions flavor
            self::$flavors['profile-actions']['allow'] = 'fb:profile-action';
            
            self::$flavors['mobile']['start'] = false;
            self::$flavors['mobile']['order'] = 'deny,allow';
            self::$flavors['mobile']['allow'] = 'fb:mobile';
            
            // This is a flavor used when processing disallowed tags in other flavors
            self::$flavors['hidden']['start'] = false;
            self::$flavors['hidden']['order'] = 'deny,allow';
            self::$flavors['hidden']['deny'] = array_merge($this->getAllTagNames('fb'), array('#text'));
		}
	}
	
	public function getAllTagMetaInfo()
	{
		$marr = array();
		foreach ($this->m_tagMetaInfo as $tns => $ttags) {
			foreach ($ttags as $tname => $tmi)  $marr[] = $tmi;
		}
		return $marr;
	}
	
	/**
 	 * Retrieve the tag meta info for a given tag name and
 	 * namespace, or NULL if one does not exist.
 	 * 
 	 * @return Social_Dsl_TagMetaInfo
 	 */
	public function getTagMetaInfo($namespace, $tagName)
	{
		if ($namespace == NULL) $namespace = 'NULL';
		if (array_key_exists($namespace, $this->m_tagMetaInfo)) {
			if (array_key_exists($tagName, $this->m_tagMetaInfo[$namespace])) {
				return $this->m_tagMetaInfo[$namespace][$tagName];
			}
		}		
		return NULL;
	}
	
	/**
     * Get an array of all tag namespaces.
     */
	public function getTagNamespaces()
	{
		return array_keys($this->m_tagMetaInfo);	
	}
	
	/**
     * Check whether the given tag has a tag handler loaded.
     */
	public function hasHandler($fullTagName)
	{
		$narr = explode(':', $fullTagName);
		$tns = NULL;
		$tname = NULL;
		if (count($narr) > 1) {
			$tns = $narr[0];
			$tname = $narr[1];
		} else if (count($narr) > 0) {			
			$tname = $narr[0];
		}
		$tmi = $this->getTagMetaInfo($tns, $tname);
		return ($tmi != NULL);
	}
	
	
	public function scanIncludePath()
	{
		if ($this->m_doCache) self::removeCacheFile();
	
		//go through search paths and load tag handlers		
		$fixedPath = 'ringside/social/dsl/handlers/fbml';
		$sFixedPath = str_replace('/', DIRECTORY_SEPARATOR, $fixedPath);
		
		$this->clearData();
		
		//load from all potential tag directories in include path
		$pathArray = explode(PATH_SEPARATOR, get_include_path());
		foreach ($pathArray as $p) {
			$searchDir = $p . DIRECTORY_SEPARATOR . $sFixedPath;
			if (file_exists($searchDir)) {
				$this->loadDirectory($searchDir);
			}
		}
	}
	
	
	/**
	 * Given a directory $dirName, search each file in the
	 * path with a .php extension, attempt to load tag meta
	 * info from that file.
	 */
	public function loadDirectory($dirName)
	{
		//error_log("[TagRegistry] Loading from '$dirName'");
		if (!in_array($dirName, $this->m_visitedDirs)) {
			$this->m_visitedDirs[] = $dirName;			
			if ($handle = opendir($dirName)) {
				while (false !== ($cname = readdir($handle))) {		
					$isPhp = (strlen($cname) > 4) && (strpos($cname, '.php', strlen($cname)-5) !== false);		
					if ($isPhp) {
						$fullName = $dirName . DIRECTORY_SEPARATOR . $cname;
						$this->loadTagHandlerMetaInfo($fullName);
					}
				}
			}
		}
	}
	
	/**
	 * Given a .php file $filePath, parse the file and obtain
	 * all the class names defined in it. Attempt to create
	 * meta info for that class if it is a potential tag handler.
	 * 
	 * Returns true if a tag handler was found and it's meta info
	 * was loaded, false otherwise.
	 */
	public function loadTagHandlerMetaInfo($filePath, $force = false)
	{	
		$c = Social_Dsl_TagMetaInfo::getClassnameFromFileName($filePath);
		if ($force || !in_array($c, $this->m_visitedClasses)) {
			$this->m_visitedClasses[] = $c;
			$tmi = Social_Dsl_TagMetaInfo::createMetaInfo($c, $filePath);
			if ($tmi != NULL) {
				$tn = $tmi->getTagName();
				$tns = $tmi->getTagNamespace();
				if ($tns == NULL) $tns = 'NULL';						
				if (!array_key_exists($tns, $this->m_tagMetaInfo)) {
					$this->m_tagMetaInfo[$tns] = array();
				}					
				$this->m_tagMetaInfo[$tns][$tn] = $tmi;
				return true;			
			}
		}
		
		return false;
	}
	
	/**
	 * Get an array of all tag names in the form <namespace>:<tagname>.
	 */
	public function getAllTagNames($namespace = NULL)
	{
		$tnames = array();
		foreach ($this->m_tagMetaInfo as $tns => $ttags) {
			if (($namespace == NULL) || 
				(($namespace != NULL) && ($tns == $namespace))) {
		
				foreach ($ttags as $tname => $tmi)  $tnames[] = "$tns:$tname";
			}
		}
		return $tnames;
	}
	
	/**
	 * Make sure the tag is valid for the given $flavor.
	 */
	public function isValidForFlavor($name, $flavor)
	{
		if ($flavor === null || $name == '' || 
      		($name[0] != '#' && strncmp($name, 'fb:', 3) != 0 && strncmp($name, 'rs:', 3) != 0 ) ||
      		self::isTidyEscapeTag($name)) {
      		
      		return true;
      	}
		
		$is_allowed = true;
      	$order = array('allow', 'deny');
      	$taggroups = array( 'allow' => array(), 'deny' => array() );
      	
      	// By default, process the tags unless rules are specified
      	$flavorProps = null;      	
      	if ( array_key_exists($flavor, self::$flavors) ) {
      		$flavorProps = self::$flavors[$flavor];
      		$cfg_order = array();
      		if ( array_key_exists('order', $flavorProps) ) {
      			$cfg_order = split(',', $flavorProps['order']);
      		}
      		
      		if ( sizeof($cfg_order) < 2 ) {
      			if ( sizeof($cfg_order) == 1 ) {
      				$order = array($cfg_order, $cfg_order=='allow'?'deny':'allow');
      			} else {
      				// Don't do anything; nothing was in the list
      				$cfg_order = $order;
      			}
      		}
      		
      		$order = $cfg_order;
      	}
      	
      	$fbTagNames = $this->getAllTagNames();
      	if ( ($flavorProps != null) && array_key_exists('allow', $flavorProps) ) {
      		$taggroups['allow'] = split(',', $flavorProps['allow']);
      	} else if ( $order[0] == 'allow' ) {
      		$taggroups['allow'] = $fbTagNames;
      	}

         if ( ($flavorProps != null) && array_key_exists('deny', $flavorProps) ) {
      		$taggroups['deny'] = split(',', $flavorProps['deny']);
      	} else if ( $order[0] == 'deny' ) {
      		$taggroups['deny'] = $fbTagNames;
      	}

      	if ( ! array_search('#text', $taggroups['allow']) && ! array_search('#text', $taggroups['deny']) ) {
      		// Unless it is specified, #text is allowed.
      		$taggroups['allow'][] = '#text';
      	}
      	
      	if ( $order[0] == 'allow' /* then deny... */ ) {
      		// In this case, we take the allows and subtract the denies. If the element is in the resulting list, it passes.
      		$check_ary = array_diff($taggroups['allow'], $taggroups['deny']);
      		$is_allowed = (array_search($name, $check_ary)===false)?false:true;
      	} else {
      		// In this case, we take the denies and subtract the allows. If the element is NOT in the resulting list, it passes.
      		$check_ary = array_diff($taggroups['deny'], $taggroups['allow']);
      		$is_allowed = (array_search($name, $check_ary)===false)?true:false;
      	}

      	return $is_allowed;
	}
	
	/**
	 * Retrieve a tidy configuration which is dynamically
	 * created according to the loaded tags.
	 */
	public function getTidyConfiguration()
	{
		if (!isset($this->m_tidyConfig)) {
			$tidyConfig = array();
			$tidyConfig['wrap'] = 200;
			$tidyConfig['output-xhtml'] = true;
			$tidyConfig['doctype'] = 'omit';
//			$tidyConfig['preserve-entities'] = true;
			
			$inlineTags = array();
			$blockTags = array();
			$emptyTags = array();
			
			foreach ($this->m_tagMetaInfo as $tns => $ttags) {
				foreach ($ttags as $tname => $tmi) {
					if (($tns != NULL) && ($tns != 'NULL')) {
						$fullName = "$tns:$tname";
						if ($tmi->getIsEmpty()) $emptyTags[] = $fullName;
		
						$ttype = $tmi->getTagType();
						if (($ttype == 'inline') || ($ttype == 'both')) $inlineTags[] = $fullName;
						if (($ttype == 'block') || ($ttype == 'both')) 	$blockTags[] = $fullName;
					}
				}
			}
			
			$blockTags[] = 'rs:script';
			$blockTags[] = 'rs:style';
					
			$tidyConfig['new-empty-tags'] = implode(',', $emptyTags);
			$tidyConfig['new-blocklevel-tags'] = implode(',', $blockTags);
			$tidyConfig['new-inline-tags'] = implode(',', $inlineTags);
			$this->m_tidyConfig = $tidyConfig;
		}
		
		return $this->m_tidyConfig;
	}
	
	/**
 	 * Scans the given $txt for tag elements (namespace:tag)
 	 * and if found, tries to load a tag handler for that new
 	 * tag.
 	 */
	public function scanForNewTags($txt)
	{
		if ($this->m_doScan) {		
			//$t1 = microtime(true);
			$pattern = '/<[a-zA-z0-9\-]*:[a-zA-z0-9\-]*/';		
			preg_match_all($pattern, $txt, $matches);
			$mtags = $matches[0];		
			
			//check for new tags
			$newTags = array();
			foreach ($mtags as $tagTxt) {
				$fullName = substr($tagTxt, 1, strlen($tagTxt)-1);
				if (($fullName != 'rs:style') && ($fullName != 'rs:script') && !$this->hasHandler($fullName)) {
					if (!in_array($fullName, $newTags)) {
						$newTags[] = $fullName;
					}
				}
			}
			//try to load new tags 
			if (count($newTags) > 0) {
				error_log('[TagRegistry] re-scanning, unknown tags used: ' . implode(',', $newTags));
				$this->scanIncludePath();
			}
			//$t2 = microtime(true);
			//$diff = round(($t2 - $t1)*1e6);
			//error_log("scanForNewTags: {$diff}us");
		}
	}
	
	/**
	 * Get the singleton instance of the tag registry.
	 * 
	 * @return Social_Dsl_TagRegistry
	 */	
	public static function getInstance()
	{	
		if (!isset(self::$m_instance)) {
			self::initInstance();			
		}
		return self::$m_instance;
	}

	
	/**
	 * Initialize the singleton instance
	 */
	protected static function initInstance()
	{
		include 'LocalSettings.php';
        if (!isset($cacheTags) || empty($cacheTags)) {
        	$cacheTags = false;
        }
		if (!isset($scanTags) || empty($scanTags)) {
        	$scanTags = false;
        }
		//construct object instance	
		self::$m_instance = new Social_Dsl_TagRegistry($cacheTags, $scanTags);
	}
	
	protected static function getCacheFileName()
	{
		$dataDir = M3_Util_Settings::getDataDirectory();
		$fname = $dataDir . DIRECTORY_SEPARATOR . 'Social_Dsl_TagRegistry.config';
		return $fname;	
	}
		
	public static function removeCacheFile()
	{
		@unlink(self::getCacheFileName());
	}
	
	protected static function isTidyEscapeTag($name)
	{	
    	return ($name == 'rs:social-dsl'
      			|| $name == 'rs:script'
      		 	|| $name == 'rs:style');
	}
	
	
}

/**
 * Retrieves the names of classes defined in a PHP file
 * by parsing through the source and looking for T_CLASS
 * tokens.
 */
function get_class_names($filePath)
{	
    if (!is_file($filePath))
    {
        return array();
    }

	$source = file_get_contents($filePath);
	$oldRep = error_reporting(E_ERROR);
	try {
				
		$toks = token_get_all($source);
	} catch (Exception $e) {
		error_log("Error parsing '$filePath': " . $e->getMessage);
		return array();
	}
	$cnames = array();
	error_reporting($oldRep);
		
	$incdef = false;	//within class definition
	foreach ($toks as $tok) {
		if (is_long($tok[0])) {
			$tokName = token_name($tok[0]);
			$tokVal = $tok[1];
			//error_log("token: name='$tokName', value='$tokVal'");
		
			if (!$incdef && ($tokName == 'T_CLASS'))  $incdef = true;
			if ($incdef && ($tokName == 'T_STRING')) {
				//found class name, push onto stack
				$cnames[] = $tokVal;
				$incdef = false;
			}
		}
	}
	return $cnames;
}

?>