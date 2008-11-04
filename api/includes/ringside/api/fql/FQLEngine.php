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

require_once( "ringside/api/fql/FQLException.php" );
require_once( "ringside/api/fql/DbTableAdaptor.php" );
require_once( "ringside/api/fql/UserDbTableAdaptor.php" );
require_once( "ringside/api/fql/FriendDbTableAdaptor.php" );
require_once( "ringside/api/fql/GroupMemberDbTableAdaptor.php" );
require_once( "ringside/api/fql/FQLParsedStatement.php" );


class FQLEngine 
{
	/**
	 * Singleton instance
	 */
	protected static $m_instance;

   /**
    * FQL Engine holds on to db connection, but probably
    * gets it from somewhere else.
    */
   protected $m_dbConnection;

   /**
    * The registered adapaters.
    */
   protected $m_dbTableAdaptors;

   public function __construct($dbCon)
   {  
   	$this->m_dbConnection = $dbCon;
      $this->m_dbTableAdaptors = array();      
   }

   public static function getInstance($dbCon)
   {
   	if (self::$m_instance == null) {
   		self::$m_instance = new FQLEngine($dbCon);
   		$fpath = dirname(__FILE__);
   		self::$m_instance->addDbTableHandler("user", "$fpath/users-table-config.json", "UserDbTableAdaptor");
   		self::$m_instance->addDbTableHandler("friend", "", "FriendDbTableAdaptor");   		
   		self::$m_instance->addDbTableHandler("group", "$fpath/group-table-config.json");
   		self::$m_instance->addDbTableHandler("group_member", "", "GroupMemberDbTableAdaptor");
   	}
   	return self::$m_instance;
   }
   
   public function addDbTableHandler($tableName, $configFilePath, $className = null)
   {
   	if ($className == null) {
   		$dba = new DbTableAdaptor();
   	} else {
   		$dba = new $className();
   	}
   	if (strlen($configFilePath) > 0) {
   		$dba->setConfigFile($configFilePath);
   	}
   	$this->m_dbTableAdaptors[$tableName] = $dba;
   }
    
   /**
    * Return the database connection.
    *
    * @return db connections
    */
   public function getDbConnection()
   {
      return $this->m_dbConnection;
   }
   
   public function query($appId, $userId, $fql)
   {  
   	return $this->queryParsedStatement($appId, $userId, new FQLParsedStatement($fql));   	
   }
   
   public function queryParsedStatement($appId, $userId, $parsedStatement)
   {
   	$tname = $parsedStatement->getFromTable();
   	//pre-defined variables to pass to DbTableAdaptor
   	$vars = array("USER_ID" => $userId, "APP_ID" => $appId);
   	if (array_key_exists($tname, $this->m_dbTableAdaptors)) {
      	$handler = $this->m_dbTableAdaptors[$tname];
      	return $handler->retrieveFields($this, $parsedStatement, $vars);
   	} else {
   		throw new FQLException("No DbHandler class specified for FQL table '$tname'");      
   	}
   }
}

?>
