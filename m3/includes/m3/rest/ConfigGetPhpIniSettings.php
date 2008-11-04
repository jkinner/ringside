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
require_once 'ringside/m3/util/PhpSettings.php';

/**
 * M3 API that returns the configuration settings found in php.ini.
 *
 * @author John Mazzitelli
 */
class ConfigGetPhpIniSettings extends M3_AbstractRest
{
    /**
     * Returns an associative array of all php.ini configuration settings.
     * The list returned is an array of name/value pair arrays.
     * 
     * @return array php.ini configuration settings, or false on error
     */
    public function execute()
    {
        $_settings = M3_Util_Settings::getPhpIniSettings();
        if ($_settings)
        {
            $_nameValues = array();
            foreach ($_settings as $_name => $_value)
            {
                $_nameValues[] = array('name' => $_name, 'value' => $_value);
            }
            
            return array('php_ini_setting' => $_nameValues);
        }
        else
        {
            return false;
        }
    }
}
?>