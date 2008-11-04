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
 * Clean all the current data. 
 * 
 * @author Richard Friedman
 */


writeDatabaseInformation();

writeLine ( "<b>Cleaning up all the data.</b>" );
$database = RingsideApiDbDatabase::getDatabaseConnection();
if ( $database === false ) { 
   writeLine( "No such database is currently available" );
   RingsideApiDbDatabase::closeConnection( $database );
} else { 

   $schema = readSqlFile( 'RingsideDbCleanData.sql' );
   if ( $schema === false ) { 
      writeError( ' The SQL could not be loade from the application ' );
      exit();
   }
   
   $result = RingsideApiDbDatabase::queryMultiLine( $schema, $database);
   if ( $result === false ) { 
      writeError( 'The database was not cleaned properly, check the error log.' );
   } else {
      writeLine( "Database " . RingsideApiConfig::$db_name . " cleaned successfully " );
   }
}

writeLine();
writeLine( "<b>Other options</b>" );
writeLink( "index.php", "Main Page" );
writeLink( "clean.php", "Clean Database" );


?>
