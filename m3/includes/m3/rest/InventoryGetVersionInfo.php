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

require_once 'ringside/m3/AbstractRest.php';
require_once 'ringside/m3/util/File.php';
require_once 'ringside/m3/util/Settings.php';
require_once('Doctrine/lib/Doctrine.php');

/**
 * M3 API that returns a version information about the Ringside software.
 *
 * @author John Mazzitelli
 */
class InventoryGetVersionInfo extends M3_AbstractRest
{
    // just a place to cache the ringside build info text file content
    private $ringsideBuildInfoContent;

    /**
     * Returns an associative, multi-dimensional array containing version
     * information about different software components in the server.
     *
     * @return array all version information
     */
    public function execute()
    {
        $_ringside = array(
            'version' => $this->getRingsideServerVersion(),
            'build_number' => $this->getRingsideServerBuildNumber(),
            'build_date' => $this->getRingsideServerBuildDate(),
            'svn_revision' => $this->getRingsideServerSvnRevision(),
            'install_dir' => M3_Util_Settings::getRingsideServerInstallDir());

        $_php = array(
            'version' => $this->getPhpVersionReal(),
            'doctrine_version' => Doctrine::VERSION,
            'php_ini_file' => M3_Util_Settings::getPhpIniFileLocation());

        // get all loaded extension versions
        $_extensions = array();
        $_loadedExtensions = get_loaded_extensions();
        foreach ($_loadedExtensions as $_loadedExtension)
        {
            $_extensions[strtolower($_loadedExtension)] = $this->getPhpVersionForExtension($_loadedExtension);
        }
        
        // There are some extensions we know we need, so force ourselves to get
        // their information. This is so we can show unloaded extensions in the list
        // to let the caller know something is missing that we expect to be loaded.
        $_extensions['tidy'] = $this->getPhpVersionForExtension('tidy');
        $_extensions['curl'] = $this->getPhpVersionForExtension('curl');
        $_extensions['xsl'] = $this->getPhpVersionForExtension('xsl');
        $_extensions['mcrypt'] = $this->getPhpVersionForExtension('mcrypt');
        $_extensions['pdo'] = $this->getPhpVersionForExtension('pdo');
        
        ksort($_extensions);

        $_extList = array();
        foreach ($_extensions as $_name => $_version)
        {
        	$_extList[] = array('name' => $_name, 'version' => $_version);
        }
        
        return array('ringside' => $_ringside,
                     'php' => $_php,
                     'extensions' => array('extension' => $_extList));
    }

    private function getRingsideServerVersion()
    {
        $_buildInfo = $this->getRingsideBuildInfoContent();
        preg_match("/Release:([^\n]*)/", $_buildInfo, $_matches);
        return @trim($_matches[1]);
    }

    private function getRingsideServerBuildNumber()
    {
        $_buildInfo = $this->getRingsideBuildInfoContent();
        preg_match("/Build Number:([^\n]*)/", $_buildInfo, $_matches);
        return @trim($_matches[1]);
    }

    private function getRingsideServerBuildDate()
    {
        $_buildInfo = $this->getRingsideBuildInfoContent();
        preg_match("/Date:([^\n]*)/", $_buildInfo, $_matches);
        return @trim($_matches[1]);
    }

    private function getRingsideServerSvnRevision()
    {
        $_buildInfo = $this->getRingsideBuildInfoContent();
        preg_match("/Subversion Revision Number:([^\n]*)/", $_buildInfo, $_matches);
        return @trim($_matches[1]);
    }

    private function getRingsideBuildInfoContent()
    {
        if (!$this->ringsideBuildInfoContent)
        {
            $this->ringsideBuildInfoContent = @file_get_contents('build.info', FILE_USE_INCLUDE_PATH);

            // if we can't read it (i.e. it doesn't exist, like on a developer's box), just set it to something
            if (!$this->ringsideBuildInfoContent)
            {
                $_date = date(DATE_RFC822);
                $this->ringsideBuildInfoContent = <<<EOF1
Release: Developer Version
Date: $_date
Build Number: Developer Build
Subversion Revision Number: Developer SVN
EOF1;
            }
        }

        return $this->ringsideBuildInfoContent;
    }

    /**
     * On some machines phpversion() will not return a string in this format:
     *    major.minor.version
     * but rather
     *    major.minor.version-[os manufacturer]
     *
     * This returns only the major.minor.version formatted version string
     * regardless of what phpversion() returns.
     *
     * @return the PHP version in the format of 'major.minor.version'
     */
    private function getPhpVersionReal()
    {
        $version = array();

        foreach(explode('.', phpversion()) as $bit)
        {
            if(is_numeric($bit))
            {
                $version[] = $bit;
            }
        }

        return(implode('.', $version));
    }

    /**
     * Returns the version string for the given extension, or "loaded"
     * if the extension has no version information but is loaded or
     * "disabled" if the extension is not loaded.
     *
     * @param $extension the extension whose version is to be returned
     *
     * @return version string
     */
    private function getPhpVersionForExtension($extension)
    {
        if (extension_loaded($extension))
        {
            $_version = phpversion($extension);
            if (!$_version)
            {
                $_version = "loaded";
            }
            return $_version;

        }
        else
        {
            return "disabled";
        }
    }
}
?>