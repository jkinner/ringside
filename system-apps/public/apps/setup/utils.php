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

require_once( 'ringside/api/config/RingsideApiConfig.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/api/db/RingsideApiDbDatabase.php' );
require_once( 'ringside/api/dao/App.php' );

function writeLine( $string = '') {
   echo $string . " <br /> \n";
}

function writeRow( $cell1, $cell2, $cell3 , $row = 0) {

   if ( ($row % 2) == 0 ) { 
      echo "<tr width='100%' style='background-color: #EEE0E5;'> ";
   }  else { 
      echo "<tr style='background-color: #FFF0F5;'> ";
   }
   echo "<td width='33%'>$cell1</td>";
   echo "<td width='33%'>$cell2</td>";
   echo "<td width='33%'>$cell3</td>";
   echo "</tr>";
}


function writeError( $string = '' ) {
   echo '<div class="explanation_message">' . $string . '</div> <br />';
}

function writeSuccess( $string ) { 
   echo '<div class="success_message">' . $string . '</div> <br />';
}

function writeLink ( $href, $text ) {
   if ( !isset($_SERVER['PATH_INFO']) ) {
      $href = "setup.php/" . $href;
   }

   echo '<a href="'.$href.'">'. $text . '</a><br />';
}

function writeDatabaseInformation() {
   // Load database information
   writeLine( "<b>Is your database configuration available and correct? </b>" );
   writeLine(  "Database: " . RingsideApiConfig::$db_name );
   writeLine(  "Location: " . RingsideApiConfig::$db_server );
   writeLine(  "Username: " . RingsideApiConfig::$db_username );
   writeLine(  "Password: " . str_repeat( '*', strlen(RingsideApiConfig::$db_password) ) );
   writeLine(  "If your configuration appears wrong you must update api.config.RingisApiConfig" );
   if ( empty (RingsideApiConfig::$db_name) || empty (RingsideApiConfig::$db_username) || empty (RingsideApiConfig::$db_server) || empty (RingsideApiConfig::$db_password)  ) {
      writeError( "<font color='red'>Warning: Your configuration has empty parameters, please update before running setup.</font>" );
   }
   writeLine( );

}

/**
 * Create the schema for database, drop the table if schema fails to load.
 *
 * @return true/false
 */
function createSchema( ) {
    
   writeLine( "Your database is not yet configured, creating schema. " );
   $database = RingsideApiDbDatabase::getConnection();
   if ( $database === false ) {
      writeError( "Can not connect to the database server." );
      writeError( "Please check and configure your LocalSettings.php" );
      writeError( "Your current database information.");
      writeDatabaseInformation();
      return false;
   }

   if ( mysql_query('CREATE DATABASE ' . RingsideApiConfig::$db_name, $database ) ) {
      writeLine( "Database <b>" . RingsideApiConfig::$db_name . " </b> created successfully " );
   } else {
      writeError( 'Error creating database: ' . mysql_error() );
      return false;
   }

   RingsideApiDbDatabase::closeConnection( $database );
   $database = RingsideApiDbDatabase::getDatabaseConnection();
   if ( $database === false ) {
      writeError( 'Error continued failure creating and connecting to database. ' );
      return false;
   }

   $schema = readSqlFile( 'ringside-schema.sql' );
   if ( $schema === false ) {
      writeError( ' The schema could not be loaded from the application ' );

      $drop = mysql_query('DROP DATABASE ' . RingsideApiConfig::$db_name, $database ) ;
      if ( $drop === false ) {
         writeError( "Error dropping database {RingsideApiConfig::$db_name} : (".mysql_errno($database).") " . mysql_error( $database) );
      } else {
         writeLine( "Dropping Database : " . RingsideApiConfig::$db_name . " dropped successfully " );
      }
      return false;
   }

   $result = RingsideApiDbDatabase::queryMultiLine( $schema, $database);
   if ( $result === false ) {
      writeError( 'The schema did not fully load, please check the error log' );
      return false;
   }

   writeLine( 'The schema for the database was loaded.' );
   writeLine();
    
   return true;
}

/**
 * Create the schema for demo applications.
 * @return true/false
 */
function createDemoSchema() {
    
   writeLine('Setting up DEMO tables for Tutorials.');
   $database = RingsideApiDbDatabase::getConnection();
   if ($database === false) {
      writeError( 'Can not connect to the database server.' );
      writeError( 'Please check and configure your LocalSettings.php' );
      writeError( 'Your current database information.');
      writeDatabaseInformation();
      return false;
   }
   
   if (!mysql_select_db(RingsideApiConfig::$db_name, $database)) {
   	writeError("Can not select the database '{RingsideApiConfig::$db_name}'.");
   }

   $schema = readSqlFile('demo-schema.sql');
   if ($schema === false) {
      writeError('The schema \'demo-schema.sql\' could not be loaded.');      
      return false;
   }

   $result = RingsideApiDbDatabase::queryMultiLine($schema, $database);
   if ($result === false) {
      writeError( 'The schema \'demo-schema.sql\' did not fully load, please check the error log' );
      return false;
   }
	
   writeLine('The demo schema was loaded.');
   writeLine();
   
   RingsideApiDbDatabase::closeConnection($database);
    
   return true;
}



/**
 * Drop the schema.
 * @return true/false
 */
function dropSchema() {
    
   $database = RingsideApiDbDatabase::getDatabaseConnection();
   if ( $database === false ) {
      writeLine( "No such database is currently available" );
      RingsideApiDbDatabase::closeConnection( $database );
   } else {
      //RingsideApiDbDatabase::closeConnection( $database );
      //$database = RingsideApiDbDatabase::getConnection();

      $drop = mysql_query('DROP DATABASE ' . RingsideApiConfig::$db_name, $database ) ;
      if ( $drop === false ) {
         writeError( "Error dropping database {RingsideApiConfig::$db_name} : (".mysql_errno($database).") " . mysql_error( $database) );
         return false;
      } else {
         writeLine( "Database " . RingsideApiConfig::$db_name . " dropped successfully " );
      }
   }

   return true;
}

/**
 * Load the basic data to get started out of box.
 *
 * @param string $defaultPassword
 * @param string $adminPassword
 * @param database_connection $database
 * @return true/false on setting up data.
 */
function loadBasicData( $defaultPassword, $adminPassword , $database = null ) {


	$fixtureInstallFailed = false;
	if ((include_once 'RingsideWebFixtures.php') === false) {
		writeError('Could not include web fixtures.');
	}
	try {
		RingsideWebFixtures::installLocalDomain();
		RingsideWebFixtures::installApps();
	} catch (Exception $e) {
		writeError('Could not install web fixtures: ' . $e->getMessage());
		$fixtureInstallFailed = true;
	}
	if (!$fixtureInstallFailed) writeLine('Successfully installed web fixtures.');

   $schema = readSqlFile( 'RingsideDbBasicData.sql' );
   if ( $schema === false ) {
      writeError( ' The SQL could not be loaded from the application ' );
      return false;
   }

   // Some replacements need to happen for this script.
   $schema = str_replace( '$everyPassword', sha1($defaultPassword), $schema );
   $schema = str_replace( '$adminPassword', sha1($adminPassword), $schema );
   $schema = str_replace( '$socialApiKey', RingsideSocialConfig::$apiKey, $schema );
   $schema = str_replace( '$socialSecretKey', RingsideSocialConfig::$secretKey, $schema );
   $schema = str_replace( '$webNetworkKey', RingsideWebConfig::$networkKey, $schema);
   $schema = str_replace( '$webUrl', RingsideApiClientsConfig::$webUrl, $schema );
   $schema = str_replace( '$socialUrl', RingsideApiClientsConfig::$socialUrl, $schema );
   $schema = str_replace( '$serverUrl', RingsideApiClientsConfig::$serverUrl, $schema );
    
   global $demoUrl;
    
   if ( isset ( $demoUrl ) && !empty( $demoUrl ) ) {
      $schema = str_replace( '$demoUrl', $demoUrl, $schema );
   } else {
      $schema = str_replace( '$demoUrl', RingsideApiClientsConfig::$webUrl, $schema );
   }
    
   $database = RingsideApiDbDatabase::getDatabaseConnection();
   $result = RingsideApiDbDatabase::queryMultiLine( $schema, $database);
   if ( $result === false ) {
      writeError( 'The database was not setup properly, check the error log. <br /> ' . mysql_error()  );
      return false;
   } else {
      writeLine( "Database <b>" . RingsideApiConfig::$db_name . "</b> setup successfully " );
      return true;
   }

}

function checkHasData() {
   try {
      $dbCon = RingsideApiDbDatabase::getDatabaseConnection();
      if ( $dbCon === false ) {
         return false;
      }
      $data = Api_Dao_App::getApplicationInfoByApiKey( RingsideWebConfig::$networkKey, RingsideSocialConfig::$apiKey, $dbCon );
      if ( $data !== false )  {
         return true;
      } else {
         return false;
      }
   } catch ( Exception $exception ) {
      return false;
   }
}

function createRandomPassword() {

   $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
   $lChars = strlen( $chars );
   srand((double)microtime()*1000000);
   $i = 0;
   $pass = '' ;

   while ($i <= 7) {
      $num = rand() % $lChars;
      $tmp = substr($chars, $num, 1);
      $pass = $pass . $tmp;
      $i++;
   }

   return $pass;
}

function readSqlFile( $file ) {

   $result = false;

   $result = file_get_contents( $file , true );

   if ( $result === false ) {
      $incFile = "ringside" . DIRECTORY_SEPARATOR. "sql" . DIRECTORY_SEPARATOR .$file;
      error_log( "Could not find file, looking in $incFile");
      $result = file_get_contents( $incFile , true );
      if ( $result === false ) {
         writeLine( "Could not locate the file ringside/config/$file , looking in PEAR" );
         error_log( "Could not find file, looking in PEAR");
         $hasPear = include_once 'PEAR/Config.php';
         if ( $hasPear !== false ) {
            $pear = new PEAR_Config();
            $pearDataDir = $pear->get( "data_dir" );
            if ( $pearDataDir !== false ) {
               $pearDataDir .= DIRECTORY_SEPARATOR . "ringside" . DIRECTORY_SEPARATOR . "config";
               $result = file_get_contents ( $pearDataDir . DIRECTORY_SEPARATOR . $file );
               if ( $result === false ) {
                  writeLine( "Check your pear setting for data_dir : "  .  $pear->get( "data_dir" ));
                  writeLine( "From the command line 'pear config-set data_dir [data dir used during installation]'");
                  writeLine( "To See your configuration settings 'pear config-show user" );
                  error_log( $package . '/' . $file . " not read into memory from include_path or " . $pearDataDir . DIRECTORY_SEPARATOR . $file );
               }
            } else {
               error_log( $package . '/' . $file . " not read into memory and pear Data dir is empty" );
            }
         } else {
            error_log( $package . '/' . $file . " not read into memory and pear not available " );
         }
      }
   }

   return $result;

}

?>
