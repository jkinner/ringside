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

class Pear_DbSetup
{

   public static function dbSetup()
   {
      if ((include 'LocalSettings.php') === false) 
      {
         die('Cannot locate LocalSettings.php, exiting...');
      }
      
      print "DB Info\n";
      print "Username: $db_username\n";
      print "Server: $db_server\n";
      print "Database: $db_name\n\n";
       
      $db = mysql_connect($db_server, $db_username, $db_password);
      if (!$db) {
         die('Could not connect to DB: ' . mysql_error() . "\n");
      }
       
      if (mysql_select_db($db_name, $db)) {
         $sql = "DROP DATABASE $db_name";
         if (!mysql_query($sql, $db)) {
            print "Couldn't drop database '$db_name' (non-fatal): " . mysql_error() . "\n";
         }
      }
       
      $sql = "CREATE DATABASE $db_name";
      if (!mysql_query($sql, $db)) {
         die("Couldn't create database '$db_name': " . mysql_error() . "\n");
      }
       
      if (!mysql_select_db($db_name, $db)) {
         die("Could not select database '$db_name' after creation: " . mysql_error());
      }
       
      $fname = 'api/config/ringside-schema.sql';
      $sql = file_get_contents($fname);
      if (!$sql) {
         die("Could not read file '$fname'.");
      }
       
      $queries = explode(';', $sql );
      foreach($queries as $query) {
         $query = trim($query);
         if(!empty($query)) {
            if(!mysql_query($query.';', $db)) {
               die("DB error: " . mysql_error($dbCon) . "\nSQL='$query'");
            }
         }
      }
       
      print "Database setup completed.\n";
   }
}

if ( isset( $_SERVER['argv'][1] ) )  {
   $action = $_SERVER['argv'][1];
   if ( $action == 'start' ) { 
      Pear_DbSetup::dbSetup();
   }
}

?>
