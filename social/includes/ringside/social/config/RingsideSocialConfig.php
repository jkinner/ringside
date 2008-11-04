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

class RingsideSocialConfig
{
	/*
	 * These should awlays get overridden in configuration settings. 
	 */
	public static $apiKey = 'RingsideSocial';
	public static $secretKey = 'RingsideSocial';

	/*
	 * Many components of the social layer, especially social dsl rendering 
	 * will need to point back to the WEB layer, as many end points exist there.
	 * Note: These end points might need more intelligence or we will need to 
	 * split end points between social layer and web layer. 
	 */
	public static $webRoot = '/web';
	
	public static $socialRoot = '/social';
	
	/*
	 This is the UID for the RingsideSocial user
	 */
	public static $uid = 1;
	 
}

class RingsideSocialServer {
    private static $LOAD_PATH = array('ringside/social', 'ringside');

    static function autoload($className) {
        if ( class_exists($className, false) || interface_exists($className, false)) {
            return false;
        }

        $parts = split('_', $className);

        $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';

        if ( count($parts) > 1 ) {
            $pkg_parts = array_slice($parts, 0, count($parts) - 1, true);
            // Ringside uses lower-case for package directory names and camel case for class names.
            $pkg_parts = strtolower(join(DIRECTORY_SEPARATOR, $pkg_parts));
            $fileName = $pkg_parts.DIRECTORY_SEPARATOR.$parts[count($parts)-1].'.php';
        }

        foreach ( self::$LOAD_PATH as $loadDirectory ) {
            $tryFile = ($loadDirectory?$loadDirectory.DIRECTORY_SEPARATOR:'').$fileName;
            if ( @include($tryFile) ) {
                if ( class_exists($className) ) {
//                    error_log("Successfully loaded $className from $tryFile");
                    return true;
                }

                error_log("Warning: Loaded file $fileName, but no class $className was defined.");
            }
        }

        return false;
    }
}

// Static block to load the LocalSettings.php.
{
	if ( ! include( 'LocalSettings.php' ) ) {
		error_log("Warning: No LocalSettings.php in include_path; default settings will be used configuration defaults");
	} else {
      
		RingsideSocialConfig::$webRoot = $webRoot;
		RingsideSocialConfig::$socialRoot = $socialRoot;
		
		if ( isset ( $socialApiKey ) && isset( $socialSecretKey )) {
			RingsideSocialConfig::$apiKey = $socialApiKey;
			RingsideSocialConfig::$secretKey = $socialSecretKey;
		}
	}

	spl_autoload_register(array('RingsideSocialServer', 'autoload'));
}

?>
