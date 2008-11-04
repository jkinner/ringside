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
// Just need the constants for the table name; maybe refactor out of the class definition file?
require_once ('ringside/api/dao/UsersApp.php');
require_once ('ringside/api/OpenFBAPIException.php');
require_once ("ringside/api/fql/FQLException.php");
require_once ("ringside/api/fql/FQLEngine.php");

require_once 'ringside/api/dao/records/RingsideUser.php';
require_once 'ringside/api/dao/records/RingsideStatus.php';
require_once 'ringside/api/dao/records/RingsideStatusHistory.php';
require_once 'ringside/api/dao/records/RingsideUsersProfileBasic.php';

require_once 'ringside/api/db/RingsideApiDbDatabase.php';
/**
 * Represents a row in the OpenFB users table.
 */
class Api_Dao_User
{
	const RS_FBDB_USERS_BAD_PASSWORD = 1;
	const RS_FBDB_USERS_NO_USER = 2;
	
	private $m_id;
	private $m_username;
	private $m_password;

	public function __construct()
	{
	}

	/**
	 * Initialize this object from a row in the database.
	 *
	 * @param unknown_type $row The database row from which to initialize.
	 */
	public function initFromDbRow($row)
	{
		$this->m_id = $row[id];
		$this->m_username = $row[username];
		$this->m_password = $row[password];
	}

	/**
	 * loads the user if it exists, returns false if the user doesn't exist
	 *
	 * @param string $name
	 * @param resource $dbCon
	 * @return bool
	 */
	public function initByUserName($name, $dbCon)
	{
		$sql = "SELECT username, id FROM users WHERE username = '$name'";
		$result = mysql_query($sql, $dbCon);
		if(! $result || mysql_num_rows($result) == 0)
		{
			return false;
		}
		
		$row = mysql_fetch_assoc($result);
		$this->setId($row[id]);
		$this->setUsername($row[username]);
		
		return true;
	}

	public static function getInfoByUids($app_id, $uids)
	{
		$uids_list = implode(",", $uids);
		
		$q = Doctrine_Query::create();
		$q->from('RingsideUser')->where("id IN ($uids_list)");
		return $q->execute();
	}

	public static function getInfo($apiParams, $app_id, $uid)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$fqlEngine = FQLEngine::getInstance($dbCon);
		
		$fieldNames = explode(",", $apiParams["fields"]);
		if(array_search("uid", $fieldNames) === false)
		{
			$fieldNames[] = "uid";
		}
		$uids = explode(",", $apiParams["uids"]);
		
		//list of user hierarchies
		$result = null;
		try
		{
			//construct base FQL for queries
			$fql = "SELECT " . implode(",", $fieldNames) . " FROM user WHERE uid IN (" . implode(",", $uids) . ")";
			$result = $fqlEngine->query($app_id, $uid, $fql);
		}catch(FQLException $exception)
		{
			throw new OpenFBAPIException($exception->getMessage(), FB_ERROR_CODE_DATABASE_ERROR);
		}
		return $result;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function isUserAdmin($uid)
	{
		$q = Doctrine_Query::create();
		$q->select('username')->from('RingsideUser')->where("id = $uid");
		$users = $q->execute();
		
		if(count($users) == 0)
		{
			throw new Exception("[isUserAdmin] no user with uid $uid found.");
		}else
		{
			$uname = $users[0]->username;
			return ('admin' == $uname);
		}
	}

	/**
	 * Attempts to login a user
	 *
	 * @param string $name
	 * @param string $password
	 * @param resource $dbCon
	 * @return boolean
	 */
	public function login($name, $password, $dbCon)
	{
		// this checks to see if the username exists
		$statement = "SELECT * FROM users WHERE username='" . addslashes($name) . "'";
		$sql = mysql_query($statement, $dbCon);
		$result = mysql_fetch_array($sql); // puts the database information into an array
		

		if(! $result || mysql_errno($dbCon) > 0)
		{
			throw new Exception("No Such User!", self::RS_FBDB_USERS_NO_USER);
		}else if($result['password'] == sha1($password))
		{
			return $result['id'];
		}
		
		throw new Exception("Bad Password", self::RS_FBDB_USERS_BAD_PASSWORD);
	}

	/**
	 * Insert this object into the database.
	 * @param unknown_type $dbCon The database connection to use to do the insert.
	 * @throws Exception if an error occurs inserting the object into the database.
	 */
	public function insertIntoDb($dbCon)
	{
		$stmt = "INSERT INTO users ( ";
		
		if(isset($this->m_id))
		{
			$stmt = $stmt . "id, ";
		}
		
		$stmt = $stmt . "username, password ) VALUES ( ";
		
		if(isset($this->m_id))
		{
			$stmt = $stmt . $this->m_id . ", ";
		}
		$stmt = $stmt . "'" . mysql_real_escape_string($this->m_username) . "', '" . mysql_real_escape_string($this->m_password) . "' )";
		
		$msg = "";
		$code = 0;
		$result = mysql_query($stmt, $dbCon);
		if(! $result)
		{
			$msg = mysql_error();
			$code = mysql_errno();
		}
		
		if(! isset($this->m_id))
		{
			$this->m_id = mysql_insert_id();
			
			// Now also insert the default apps for this user
			$sql = 'INSERT INTO users_app (users_app.user_id,
			users_app.allows_create_listing,
			users_app.app_id,
			users_app.allows_photo_upload,
			users_app.allows_status_update,
			users_app.auth_information,
			users_app.auth_leftnav,
			users_app.auth_newsfeeds,
			users_app.auth_profile,
			users_app.enabled)
				SELECT ' . $this->m_id . ', 
			default_app.allows_create_listing,
			default_app.app_id,
			default_app.allows_photo_upload,
			default_app.allows_status_update,
			default_app.auth_information,
			default_app.auth_leftnav,
			default_app.auth_newsfeeds,
			default_app.auth_profile,
			default_app.enabled
				FROM default_app';
			$res = mysql_query($sql, $dbCon);
			if(! $res)
			{
				// Log as a warning. Default apps will not be added, but let the user register anyway.
				error_log("Warning: When registering default application(s): " . mysql_error());
			}
		}
		
		if(! $result)
		{
			throw new Exception("Error inserting a user.  Error: " . $msg, $code);
		}
	}

	/**
	 * Delete this object from the database.
	 * @param unknown_type $dbCon The database connection to use to do the delete.
	 *
	 * @throws Exception if an error occurs deleting the object from the database.
	 */
	public function deleteFromDb($dbCon)
	{
		$stmt = "DELETE FROM users WHERE id = " . $this->m_id;
		$result = mysql_query($stmt, $dbCon);
		if(! $result)
		{
			throw new Exception("Error deleting a user.  Error: " . mysql_error(), FB_ERROR_CODE_DATABASE_ERROR);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $lastName
	 * @param unknown_type $firstLike
	 * @return unknown
	 */
	public static function searchUsers($lastName, $firstLike)
	{
		$q = Doctrine_Query::create();
		$q->select('user_id, first_name, last_name, pic_small_url')
			->from('RingsideUsersProfileBasic')
			->where("last_name = '$last' OR first_name $first_like");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $appId
	 * @param unknown_type $status
	 * @param unknown_type $cleared
	 * @return unknown
	 */
	public static function createStatusHistory($userId, $appId, $statusMessage, $cleared)
	{
		$status = new RingsideStatusHistory();
		$status->uid = $userId;
		$status->aid = $appId;
		$status->status = $statusMessage;
		$status->cleared = $cleared;
		$ret = $status->trySave();
		
		if($ret)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $appId
	 * @return unknown
	 */
	public static function isStatus($userId, $appId)
	{
		$q = Doctrine_Query::create();
		$q->select('count(uid) as uid_count')->from('RingsideStatus')->where("uid = $userId AND aid = $appId");
		$ret = $q->execute();
		if($ret[0]['uid_count'] > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $appId
	 * @param unknown_type $status
	 * @return unknown
	 */
	public static function createStatus($userId, $appId, $status)
	{
		$status = new RingsideStatus();
		$status->uid = $userId;
		$status->aid = $appId;
		$status->status = $status;
		$ret = $status->trySave();
		
		if($ret)
		{
			return $status->getIncremented();
		}
		
		return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $appId
	 * @param unknown_type $status
	 * @return unknown
	 */
	public static function updateStatus($userId, $appId, $status)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideStatus')->set('status', '?', $status)->where("uid = $userId AND aid = $appId");
		return $q->execute();
	}

	public function getId()
	{
		return $this->m_id;
	}

	public function setId($id)
	{
		$this->m_id = $id;
	}

	public function getUsername()
	{
		return $this->m_username;
	}

	public function setUsername($username)
	{
		$this->m_username = $username;
	}

	public function getPassword()
	{
		return $this->m_password;
	}

	public function setPassword($password)
	{
		$this->m_password = $password;
	}
}

?>
