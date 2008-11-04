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

class Social_Dsl_TagMetaInfo
{
	protected $m_handlerClassName;
	
	protected $m_handlerSourceFile;
	
	protected $m_tagNamespace;
	
	protected $m_tagName;
	
	protected $m_isEmpty;
	
	protected $m_tagType;
	
	protected $m_lastModified;
		
	public static function createMetaInfo($className, $sourceFilePath)
	{
		include_once $sourceFilePath;
	
		if (class_exists($className)) {
			$cObj = new $className();
			$meths = get_class_methods($cObj);
			
			if (in_array('doStartTag', $meths)) {			
				
				$tmi = new Social_Dsl_TagMetaInfo($className, $sourceFilePath);
				unset($cObj);
				
				return $tmi;
			}
			unset($cObj);
		}
		return NULL;
	}
	
	protected static function tagNameToClassName($name)
	{
		$cname = '';
		$dash = false;
		for ($k = 0; $k < strlen($name); $k++) {
			if ($name[$k] != '-') {
				if ($dash) {
					$cname .= strtoupper($name[$k]);
				} else {
					$cname .= strtolower($name[$k]);
				}
				$dash = false;
			} else {
				$dash = true;
			}
		}
		return ucfirst($cname);
	}
	
	/**
	 * Get the names of potential files for a given tag name.
	 * $fullName is in the form <namespace>:<tag name>.
	 */
	public static function getFilenamesFromTagName($fullName)
	{
		$narr = explode(':', $fullName);
		if (count($narr) > 0) {
			$ns = $narr[0];
			$name = $narr[1];
			$fname = strtolower($ns) . self::tagNameToClassName($name);
			return array("$fname.php", "{$fname}Handler.php");
		}
		return array();
	}
	
	public static function getClassnameFromFileName($filePath)
	{
		$farr = explode(DIRECTORY_SEPARATOR, $filePath);
		$fName = $farr[count($farr)-1];
		
		$parr = explode('.', $fName);
		return $parr[0];
	}
	
	public static function getNamespaceFromClassName($cname)
	{
		//HTML tags don't have a namespace
		if (is_upper($cname[0])) return NULL;
		
		//get namespace for non-HTML tags
		$ns = '';
		for ($k = 0; $k < strlen($cname); $k++) {
			$c = substr($cname, $k, 1);
			if (!is_upper($c)) {
				$ns .= $c;
			} else {
				break;
			}
		}
		return $ns;
	}
	
	public static function getNameFromClassName($cname)
	{
		//strip out namespace from class name
		$n = '';
		$inname = false;
		for ($k = 0; $k < strlen($cname); $k++) {
			$c = substr($cname, $k, 1);
			if (!$inname && is_upper($c)) $inname = true;			
			if ($inname) {
				$n .= $c;
			}
		}
		
		//strip out "Handler" at end if it exists
		$hlen = strlen('Handler');
		if (strlen($n) > $hlen) {
			$estr = substr($n, strlen($n)-$hlen, $hlen);			
			if ($estr == 'Handler') {
				$n = substr($n, 0, strlen($n)-$hlen);
			}
		}
		
		//convert upper to lower, put dashes in between lower and upper
		$tagName = '';
		$firstUpper = true;
		$lastWasUpper = false;
		for ($k = 0; $k < strlen($n); $k++) {
			$c = substr($n, $k, 1);
			if (is_upper($c)) {
				if (!$firstUpper && !$lastWasUpper) {
					$tagName .= '-';
				} else {
					$firstUpper = false;
				} 		
				$tagName .= strtolower($c);
				$lastWasUpper = true;
			} else {
				$lastWasUpper = false;
				$tagName .= $c;
			}			
		}
		
		return $tagName;
	}
	
	public static function fromString($serializedString)
	{
		$iarr = explode(',', $serializedString);
		if (count($iarr) == 7) {
			$tmi = new Social_Dsl_TagMetaInfo();
			$tns = ($iarr[0] == 'NULL') ? NULL : $iarr[0];
			$fname = $iarr[2];
			$tmi->setTagNamespace($tns);
			$tmi->setTagName($iarr[1]);
			$tmi->setHandlerSourceFile($fname);
			$tmi->setHandlerClassName($iarr[3]);
			$tmi->setTagType($iarr[4]);
			$tmi->setIsEmpty(($iarr[5] == 'true') ? true : false);			
			$tmi->setLastModified(intval($iarr[6]));
			
			return $tmi;
		} else {
			throw new Exception("[TagMetaInfo] incorrectly-serialized string: $serializedString");
		}
		return NULL;
	}
	
	public function __construct($className = NULL, $sourceFilePath = NULL)
	{
		$this->m_lastModified = -1;
		if (($className != NULL) && ($sourceFilePath != NULL)) {
			$this->m_handlerClassName = $className;
			$this->m_handlerSourceFile = $sourceFilePath;
			$this->introspect();		
		}
	}	
	
	public function setLastModified($mtime)
	{
		$this->m_lastModified = $mtime;
	}
	
	public function setTagName($n)
	{
		$this->m_tagName = $n;
	}
	
	public function setTagNamespace($n)
	{
		$this->m_tagNamespace = $n;
	}
	
	public function setHandlerSourceFile($n)
	{
		$this->m_handlerSourceFile = $n;
	}
	
	public function setHandlerClassName($n)
	{
		$this->m_handlerClassName = $n;
	}
	
	public function setTagType($n)
	{
		$this->m_tagType = $n;
	}
	
	public function setIsEmpty($n)
	{
		$this->m_isEmpty = $n;
	}
	
	public function getTagNamespace()
	{
		return $this->m_tagNamespace;
	}
	
	public function getTagName()
	{
		return $this->m_tagName;
	}
	
	public function getIsEmpty()
	{
		return $this->m_isEmpty;
	}
	
	public function getTagType()
	{
		return $this->m_tagType;
	}
	
	/**
	 *	@return int # of seconds since the epoch in the 70's (see filemtime)
	 */
	public function getLastModified()
	{
		return $this->m_lastModified;
	}
	
	public function getHandlerSourceFile()
	{
		return $this->m_handlerSourceFile;
	}
	
	public function getHandlerClassName()
	{
		return $this->m_handlerClassName;
	}
	
	protected function introspect()
	{
		include_once $this->m_handlerSourceFile;
		
		$cObj = new $this->m_handlerClassName();
		$meths = get_class_methods($cObj);
		
		if (in_array('getNamespace', $meths)) {
			$this->m_tagNamespace = $cObj->getNamespace();
		} else {
			$this->m_tagNamespace = self::getNamespaceFromClassName($this->m_handlerClassName);
		}
		
		if (in_array('getName', $meths)) {
			$this->m_tagName = $cObj->getName();
		} else {
			$this->m_tagName = self::getNameFromClassName($this->m_handlerClassName);
		}
		
		if (in_array('isEmpty', $meths)) {
			$this->m_isEmpty = $cObj->isEmpty();
		} else {
			$this->m_isEmpty = false;
		}
		
		if (in_array('getType', $meths)) {
			$this->m_tagType = $cObj->getType();
		} else {
			$this->m_tagType = 'inline';
		}
		
		unset($cObj);
	}
		
	public function createTagHandlerInstance()
	{
		include_once $this->m_handlerSourceFile;		
		$cObj = new $this->m_handlerClassName();
		return $cObj;
	}
	
	public function toString()
	{
		$lm = ($this->m_lastModified == -1) ? filemtime($this->m_handlerSourceFile) : $this->m_lastModified; 
	
		$tns = ($this->m_tagNamespace == NULL) ? 'NULL' : $this->m_tagNamespace; 
		$vals = array($tns,$this->m_tagName,$this->m_handlerSourceFile,
				      $this->m_handlerClassName,$this->m_tagType,($this->m_isEmpty ? 'true' : 'false'), $lm);
		return implode(',', $vals);
	}
}

function is_upper($c)
{
	$aval = ord($c);	
	if (($aval > 64) && ($aval < 91)) return true;
	return false;
}


?>