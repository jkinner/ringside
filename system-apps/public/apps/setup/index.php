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

include_once 'utils.php';

/**
 * The SETUP application is for getting the platform off the ground.
 * It is an administrator level application and after configuration
 * will only be available for specific users you configure.
 *
 * Database Configuration
 * Schema Configuration
 * Default Settings
 * Define Administrator
 * Add core applications
 * Add mock data for applications
 *
 * There are three compents to the platform
 * WEB, SOCIAL, API
 *
 * @author Richard Friedman
 */

// Step 0. Pre-flight - do you have a localsettings.php?
$errorReporting = error_reporting( E_ERROR );
$hasLocalSettings = include( "LocalSettings.php" );
error_reporting( $errorReporting );
if ( $hasLocalSettings === false ) { 
   writeLine( "<h1>To Start this process</h1>" );
   writeLine( "In the directory  you unzipped/untarred this file please " );
   writeLine( "Copy LocalSettings.php.example to LocalSettings.php and modify appropriately" );
   return;
}

// Step 0. Pre-flight - are you already configured?
$hasData = checkHasData();
if ( $hasData == true )  {
   writeLine( "<h1>You are already configured</h1>" );
   writeLine( "" );
   $href = "login.php";
   if ( isset($_SERVER['PATH_INFO']) ) {
      $href = "../" . $href;
   }
   writeLine( '<a href="'.$href.'">Go to Login Page.</a><br />' );

   writeLine( "<b>Other options</b>" );
   writeLink( "drop.php", "Drop Schema, Start Again" );
   return;
}

// step 1 DB setup.
writeLine( "<h1>Step 1: Do you have the required modules</h1>");
$tidy = extension_loaded( "tidy");
$curl = extension_loaded('curl');

if ( $tidy == true ) {
   writeLine( " Found Tidy version : " . phpversion('tidy') ) ;
} else {
   writeError( ' <b>Missing Tidy</b> :Please install tidy into your PHP distribution <a href="http://ringsidenetworks.org/">Learn more</a>' );
}

if ( $curl == true ) {
   $version =  phpversion('curl');
   if ( empty ($version) ) { 
      $version = "yes";
   }
   writeLine( " Found Curl version : " . $version ) ;
} else {
   writeError( ' <b>Missing CURL</b> :Please install CURL into your PHP distribution <a href="http://ringsidenetworks.org/">Learn more</a>' );
}

if ( $tidy != true || $curl != true ) {
   writeError( "Current system is using PHP.ini from "  . get_cfg_var('cfg_file_path') );
   return;
}

echo "<hr />";
writeLine ( "<h1>Step 2: Checking API and SECRET Keys.</h1>" );
require_once( 'ringside/social/config/RingsideSocialConfig.php' );
$api_key = RingsideSocialConfig::$apiKey;
$secret_key = RingsideSocialConfig::$secretKey;
if ( $api_key == "RingsideSocial"  || $secret_key == "RingsideSocial" || strlen($secret_key) < 4 ) {
   writeError( ' You have not configured the SOCIAL to API layer appropriately, API KEY and SECRET KEY should be set.' );
   writeLink( "drop.php", "Drop Schema, Start Again" );
   return;
}
writeLine( " The keys are configured properly." );

echo "<hr />";
writeLine ( "<h1>Step 3: Let's check your database.</h1>" );
$database = RingsideApiDbDatabase::getDatabaseConnection();
if ($database === false) {

   if (createSchema() === false) {
      writeLine("Could not create core schema, exiting due to error above.");
      writeLink( "drop.php", "Drop Schema, Start Again" );
      return;
   }   
   if (createDemoSchema() === false) {
      writeLine("Could not create demo schema, exiting due to error above.");
      writeLink( "drop.php", "Drop Schema, Start Again" );
      return;
   }
}
writeLine( "We were able to verify the database and schema exists." );

echo "<hr />";
writeLine ( "<h1>Step 4: Check connection to API server.</h1>" );
require_once( 'ringside/api/clients/RingsideApiClientsConfig.php' );
$serverUrl = RingsideApiClientsConfig::$serverUrl;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $serverUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Ringside API PHP5 Client 1.1 (curl) ' . phpversion());
$result = curl_exec($ch);
$errno = curl_errno($ch);
curl_close($ch);

if ( $errno == 0 ) {
   writeLine( " API server was located and responds. $serverUrl ");
} else {
   writeError( " API server failed to respond (Error $errno).  Validate the server url : $serverUrl ");
   writeLink( "drop.php", "Drop Schema, Start Again" );
   return;
}

echo "<hr />";
writeLine ( "<h1>Step 5: Setting up initial data.</h1>" );
$adminPassword = createRandomPassword();
$result = loadBasicData( 'ringside', $adminPassword, $database );
if ( $result == true ) {
   writeLine( " Basic setup information and some sample data was written to the database." );
//   writeLine( " Admin user name is <b>admin</b> and password is <b>$adminPassword</b>" );
   writeLine( " The following example users were created as well, all with password <b>ringside</b>" );
   echo "<table cellpadding='4' width='80%'> ";
   writeRow( 'joe@goringside.net', 'jack@goringside.net', 'jeff@goringside.net' , 1);
   writeRow( 'joel@goringside.net', 'jane@goringside.net',  'jill@goringside.net', 2 );
   writeRow( 'john@goringside.net', 'jon@goringside.net' , 'jared@goringside.net', 3  );
   echo "</table>";
   
} else {
   writeError( " While everything seems to be setup we could not add the data to the database. <br />" . 
   			   "<b>Maybe do some clean up of an old install/version first?</b>" );
   writeLink( "drop.php", "Drop Schema, Start Again" );
      
   return;
}

$href = "";
if ( isset($_SERVER['PATH_INFO']) ) {
   $href = "../" . $href;
}
echo "<hr />";
$successMessage = "You are configured <br />";
$successMessage .= "Login as administrator: <b>username</b> : admin <b>password</b>: $adminPassword <br />";
$successMessage .= "OR<br />";
$successMessage .= "Login with user <b>username</b> : joe@goringside.net <b>password</b>: ringside <br />";
$successMessage .= '<a href="'.$href.'login.php">Go to Login Page </a> ';
$successMessage .= ' or <a href="'.$href.'register.php">Register a new user!</a>';
writeSuccess( $successMessage );

writeLink( "drop.php", "Or Start again and Drop Schema" );

?>
