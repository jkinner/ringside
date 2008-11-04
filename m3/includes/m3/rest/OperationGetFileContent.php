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

/**
 * M3 API that allows a management tool to obtain file contents
 * of any arbitrary Ringside file.
 * 
 * This takes two parameters:
 * - pathname: the relative path to the file whose content is to be retrieved (required)
 * - useInclude: if specified, and set to "true", will look up the file in include path (optional)
 *
 * If useInclude is not true, the pathname is assumed relative to the
 * Ringside Server's install directory.
 * 
 * The API returns the content that is gzipped and base-64 encoded.
 *
 * @author John Mazzitelli
 */
class OperationGetFileContent extends M3_AbstractRest
{
    private $pathname;
    private $useIncludePath;

    public function validateRequest()
    {
        $this->pathname = $this->getRequiredApiParam("pathname");
        $this->useIncludePath = $this->getApiParam("useIncludePath");
        
        // not allowed to look for files in parent directories
        if (preg_match("/\.\./", $this->pathname))
        {
            throw new OpenFBAPIException("Illegal pathname has been rejected");
        }
        
        if (isset($this->useIncludePath) && ($this->useIncludePath == "true"))
        {
            $this->pathname = M3_Util_File::findFileInIncludePath($this->pathname);
        }
        else
        {
            $_installDir = M3_Util_Settings::getRingsideServerInstallDir();
            $this->pathname = M3_Util_File::buildPathName($_installDir, $this->pathname);
        }

        if (!file_exists($this->pathname))
        {            
            throw new OpenFBAPIException("File [{$this->pathname}] does not exist");
        }

        // don't process request for any content larger than 1MB
        $_size = filesize($this->pathname);
        if ( $_size >= M3_Util_Settings::getMaxSizeAllowedOperationGetFileContent())
        {
            throw new OpenFBAPIException("File [{$this->pathname}] is too large [$_size]");
        }
        
        return;
    }
    
    public function execute()
    {
        $_content = file_get_contents($this->pathname);
        $_content = gzencode($_content);
        $_content = base64_encode($_content);
        return $_content;            
    }
}

?>