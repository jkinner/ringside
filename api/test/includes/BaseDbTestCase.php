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

require_once( 'PHPUnit/Framework.php' );
require_once 'SuiteTestUtils.php';

class BaseDbTestCase extends PHPUnit_Framework_TestCase
{
   protected function setUp() {
      require_once 'sql/AllDBAPITests-teardown.sql';
      require_once 'sql/AllDBAPITests-setup.sql';
   }

   protected function tearDown() {

   }
   
   private static $m_dbCon;

   public static function getDbCon() {

      if ( self::$m_dbCon == null ) {

         self::$m_dbCon = mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password );
         if ( ! self::$m_dbCon ) {
            throw new Exception( 'The service is not available at this time : db connect failure ' . mysql_error() , mysql_errno() );
         }

         if ( ! mysql_select_db( RingsideApiConfig::$db_name, self::$m_dbCon) ) {
            echo "error selecting" . mysql_error();
            throw new Exception( 'The service is not available at this time : db catalog failure ' . mysql_error() , mysql_errno() );
         }

      }

      return self::$m_dbCon;
   }   
}


?>
