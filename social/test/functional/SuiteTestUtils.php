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

   include_once 'ringside/api/config/RingsideApiConfig.php';

  function getDbCon() {

     static $m_dbCon = null;
     
      if ( $m_dbCon == null ) {

         $m_dbCon = mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password );
         if ( ! $m_dbCon ) {
            throw new Exception( 'The service is not available at this time : db connect failure ' . mysql_error() , mysql_errno() );
         }

         if ( ! mysql_select_db( RingsideApiConfig::$db_name, $m_dbCon) ) {
            echo "error selecting" . mysql_error();
            throw new Exception( 'The service is not available at this time : db catalog failure ' . mysql_error() , mysql_errno() );
         }

      }

      return $m_dbCon;
   }   
   
/**
 * Utility to load and run a sql file.
 *
 * @param unknown_type $filename
 * @param unknown_type $errmsg
 * @return unknown
 */
function mysql_import_file($filename, &$errmsg)
{
   /* Read the file */
   $lines = file($filename);

   if(!$lines)
   {
      $errmsg = "cannot open file $filename";
      return false;
   }

   $scriptfile = false;

   /* Get rid of the comments and form one jumbo line */
   foreach($lines as $line)
   {
      $line = trim($line);

      if(!ereg('^--', $line))
      {
         $scriptfile.=" ".$line;
      }
   }

   if(!$scriptfile)
   {
      $errmsg = "no text found in $filename";
      return false;
   }

   /* Split the jumbo line into smaller lines */

   $queries = explode(';', $scriptfile);

   /* Run each line as a query */

   mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password ) or die ( "Connect : " . mysql_error());
   mysql_select_db( RingsideApiConfig::$db_name ) or die ( "Select : " . mysql_error());

   foreach($queries as $query)
   {
      $query = trim($query);
      if($query == "") { continue; }
      if(!mysql_query($query.';'))
      {
         $errmsg = "query ".$query." failed";
         return false;
      }
   }

   /* All is well */
   return true;
}

function mysql_upload_string( $sql ) {

   $queries = explode(';', $sql );
   
   mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password ) or die ( "Connect : " . mysql_error());
   mysql_select_db( RingsideApiConfig::$db_name ) or die ( "Select : " . mysql_error());
   
   foreach($queries as $query)
   {
      $query = trim($query);
      if( !empty($query) ) {
         if(!mysql_query($query.';'))
         {
            $errmsg = "query ".$query." failed";
            echo "Failed ($query) " . mysql_errno();
            return false;
         }
      }
   }
   
   return true;
   
}

?>
