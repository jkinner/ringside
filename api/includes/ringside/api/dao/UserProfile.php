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
require_once ('ringside/api/dao/records/RingsideUsersProfileEcontact.php');

class Api_Dao_UserProfile
{
	/**
	 * Gets all the contacts for the to and from email addresses
	 *
	 * @param unknown_type $to
	 * @param unknown_type $from
	 * @return unknown
	 */
	public static function getEContacts($to, $from)
	{
		$toString = implode(",", $to);
		$q = Doctrine_Query::create();
		
		$q->select('user_id, contact_value')->from('RingsideUsersProfileEcontact e')->where("contact_type='email' and user_id in ($toString,$from)");
		
		return $q->execute();
	}
	
	protected $m_userId;
	protected $m_userProfile;
	protected $m_paramDbMap;

	public function __construct()
	{
		$this->initProfile();
	}

	public function getUserId()
	{
		return $this->m_userId;
	}

	public function setUserId($uid)
	{
		$this->m_userId = $uid;
	}

	public function getUserProfile()
	{
		return $this->m_userProfile;
	}

	protected function initProfile()
	{
		
		$this->m_userProfile = array();
		$this->initParamDbMap();
		
		foreach(array_keys($this->m_paramDbMap) as $fbName)
		{
			$this->m_userProfile[$fbName] = null;
		}
	}

	public function initFromDbRow($row)
	{
		//TODO
	}

	public function insertIntoDb($dbCon)
	{
		
		$sqlFields = array();
		foreach($this->m_userProfile as $fbName=>$upVal)
		{
			
			$dbValue = $this->m_paramDbMap[$fbName];
			$methodName = "insert" . $dbValue;
			
			if(method_exists($this, $dbVal))
			{
				//call method for inserting complex data   			
				$this->$methodName($this->m_userId, $dbCon, $upVal);
			}else
			{
				//append simple data to SQL for execution later 
				list($tblName, $fieldName) = explode(".", $dbValue);
				if(! isset($sqlFields[$tblName]))
					$sqlFields[$tblName] = array();
				$sqlFields[$tblName][] = $fieldName;
			}
		}
	}

	public function deleteFromDb($dbCon)
	{
		//TODO
	}

	public function retrieveFromDb($fieldNames, $dbCon)
	{
		
		$sqlFields = array();
		$joinTables = array();
		foreach($fieldNames as $fbName)
		{
			//check validity
			if(! array_key_exists($fbName, $this->m_userProfile))
			{
				throw new Exception("[UserProfile.retreieveFromDb] " . "no such user profile field '$fbName'");
			}
			
			//if field value hasn't been set, set it - either by
			//calling a function which will retreive the values
			//from the DB (for complex data), or by including it
			//in a single SQL statement which will obtain all the simple
			//field values, to be executed after this loop.
			if($this->m_userProfile[$fbName] == null)
			{
				$dbValue = $this->m_paramDbMap[$fbName];
				$methodName = "get" . $dbValue;
				if(method_exists($this, $methodName))
				{
					//retreive complex value    		
					$this->m_userProfile[$fbName] = $this->$methodName($this->m_userId, $dbCon);
				}else
				{
					//add simple value to list for SQL
					list($tblName, $fieldName) = explode(".", $dbValue);
					$sqlFields[] = "$tblName.$fieldName AS $fbName";
					
					if(! in_array($tblName, $joinTables))
						$joinTables[] = $tblName;
				}
			}
		}
		
		//construct SQL to obtain remaining data
		$sql = "SELECT users.id AS uid";
		if(count($sqlFields) > 0)
		{
			$sql .= "," . implode(",", $sqlFields) . " FROM users";
			foreach($joinTables as $tbl)
			{
				$sql .= " LEFT JOIN $tbl ON $tbl.user_id=users.id";
			}
		}
		$sql .= " WHERE users.id=" . $this->getUserId();
		
		//add simple data to user profile    	
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				foreach($row as $fname=>$val)
				{
					$this->m_userProfile[$fname] = $val;
				}
			}
		}else
		{
			print"Error: " . mysql_error() . "\nsql='$sql'";
			throw new Exception("[UserProfile.retreiveFromDb] " . "Error: " . mysql_error() . "\nsql='$sql'");
		}
	
	}

	protected function insertAffiliations($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getAffiliations($uid, $dbCon)
	{
		
		$sql = "SELECT network_id, name FROM users_profile_networks " . "LEFT JOIN networks ON networks.id=users_profile_networks.network_id " . "WHERE user_id=$uid";
		
		$affs = array();
		if($result = mysql_query($sql, $dbCon))
		{
			while($row = mysql_fetch_assoc($result))
			{
				$aff = array();
				$aff["nid"] = $row["network_id"];
				$aff["name"] = $row["name"];
				$aff["type"] = "";
				$aff["year"] = "";
				
				$affs[] = $aff;
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getAffiliations] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $affs;
	}

	protected function insertCurrentLocation($uid, $dbCon, $profileValue, $isHometown = false)
	{
	
	}

	protected function getCurrentLocation($uid, $dbCon, $isHometown = false)
	{
		$sql = "SELECT city,state,country,zip FROM users_profile_contact " . "WHERE user_id=$uid";
		
		if($isHometown)
			$sql .= " AND is_hometown=TRUE";
		
		$loc = array();
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				foreach($row as $fname=>$val)
				{
					$loc[$fname] = $val;
				}
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getCurrentLocation] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $loc;
	}

	protected function insertEducationHistory($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getEducationHistory($uid, $dbCon, $schoolType = null)
	{
		$sql = "SELECT grad_year,concentrations,name,school_type FROM users_profile_school " . "LEFT JOIN schools ON schools.id=users_profile_school.school_id " . "WHERE user_id=$uid";
		if($schoolType != null)
		{
			$sql .= " AND school_type='$schoolType'";
		}
		
		$schools = array();
		if($result = mysql_query($sql, $dbCon))
		{
			while($row = mysql_fetch_assoc($result))
			{
				$school = array();
				$school["name"] = $row["name"];
				$school["year"] = $row["grad_year"];
				$school["concentrations"] = explode(",", $row["concentrations"]);
				
				$schools[] = $school;
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getEducationHistory] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $schools;
	}

	protected function insertAppUser($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getIsAppUser($uid, $dbCon)
	{
		//TODO
		return "";
	}

	protected function insertHasAddedApp($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getHasAddedApp($uid, $dbCon)
	{
		//TODO
		return "";
	}

	protected function insertHometownLocation($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getHometownLocation($uid, $dbCon)
	{
		return $this->getCurrentLocation($uid, $dbCon, true);
	}

	protected function insertHighSchoolInfo($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getHighSchoolInfo($uid, $dbCon)
	{
	}

	protected function insertMeetingFor($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getMeetingFor($uid, $dbCon)
	{
		
		$sql = "SELECT meeting_for FROM users_profile_rel " . "WHERE user_id=$uid";
		
		$mfor = array();
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				$mfor = explode(",", $row["meeting_for"]);
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getMeetingFor] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $mfor;
	}

	protected function insertMeetingSex($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getMeetingSex($uid, $dbCon)
	{
		$sql = "SELECT meeting_sex FROM users_profile_rel " . "WHERE user_id=$uid";
		
		$msex = array();
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				$msex = explode(",", $row["meeting_sex"]);
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getMeetingSex] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $msex;
	}

	protected function insertName($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getName($uid, $dbCon)
	{
		$sql = "SELECT concat(first_name, ' ', last_name) AS full_name FROM users_profile_basic " . "WHERE user_id=$uid";
		
		$fname = "";
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				$fname = $row["full_name"];
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getName] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		return $fname;
	}

	protected function insertNotesCount($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getNotesCount($uid, $dbCon)
	{
		//TODO: implement
		return "0";
	}

	protected function insertPicUrl($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getPicUrl($uid, $dbCon)
	{
		//TODO
		return "http://www.nopic.com";
	}

	protected function insertStatus($uid, $dbCon, $profileValue)
	{
	
	}

	protected function getStatus($uid, $dbCon)
	{
		$sql = "SELECT status_message,status_update_time FROM users_profile_basic " . " WHERE user_id=$uid";
		
		$status = array();
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				$status["message"] = $row["status_message"];
				$status["time"] = $row["status_update_time"];
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getStatus] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $status;
	}

	protected function getWallCount($uid, $dbCon)
	{
		//TODO
		return "0";
	}

	protected function getWorkHistory($uid, $dbCon)
	{
		
		$sql = "SELECT employer,position,description,city,state,country,current,start_date,end_date " . "FROM users_profile_work WHERE user_id=$uid";
		
		$whist = array();
		if($result = mysql_query($sql, $dbCon))
		{
			if($row = mysql_fetch_assoc($result))
			{
				$winfo = array();
				
				$winfo["location"] = array();
				$winfo["location"]["city"] = $row["city"];
				$winfo["location"]["state"] = $row["state"];
				$winfo["location"]["country"] = $row["country"];
				
				$winfo["company_name"] = $row["employer"];
				$winfo["position"] = $row["position"];
				$winfo["description"] = $row["description"];
				$winfo["start_date"] = $row["start_date"];
				$winfo["end_date"] = $row["end_date"];
				
				$whist[] = $winfo;
			}
		}else
		{
			throw new Exception("[UsersGetInfo.getWorkHistory] Error: " . mysql_error() . "\nsql='$sql'", FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $whist;
	}

	protected function initParamDbMap()
	{
		$this->m_paramDbMap = array();
		$this->m_paramDbMap["about_me"] = "users_profile_personal.about";
		$this->m_paramDbMap["activities"] = "users_profile_personal.activities";
		$this->m_paramDbMap["affiliations"] = "Affiliations";
		$this->m_paramDbMap["birthday"] = "users_profile_basic.dob";
		$this->m_paramDbMap["books"] = "users_profile_personal.books";
		$this->m_paramDbMap["current_location"] = "CurrentLocation";
		$this->m_paramDbMap["education_history"] = "EducationHistory";
		$this->m_paramDbMap["first_name"] = "users_profile_basic.first_name";
		$this->m_paramDbMap["is_app_user"] = "IsAppUser";
		$this->m_paramDbMap["has_added_app"] = "HasAddedApp";
		$this->m_paramDbMap["hometown_location"] = "HometownLocation";
		$this->m_paramDbMap["hs_info"] = "HighSchoolInfo";
		$this->m_paramDbMap["interests"] = "users_profile_personal.interests";
		$this->m_paramDbMap["last_name"] = "users_profile_basic.last_name";
		$this->m_paramDbMap["meeting_for"] = "MeetingFor";
		$this->m_paramDbMap["meeting_sex"] = "MeetingSex";
		$this->m_paramDbMap["movies"] = "users_profile_personal.movies";
		$this->m_paramDbMap["music"] = "users_profile_personal.music";
		$this->m_paramDbMap["name"] = "Name";
		$this->m_paramDbMap["notes_count"] = "NotesCount";
		$this->m_paramDbMap["pic"] = "PicUrl";
		//TODO: implement big, small, square pics
		$this->m_paramDbMap["pic_big"] = "PicUrl";
		$this->m_paramDbMap["pic_small"] = "PicUrl";
		$this->m_paramDbMap["pic_square"] = "PicUrl";
		$this->m_paramDbMap["political"] = "users_profile_basic.political";
		$this->m_paramDbMap["profile_update_time"] = "users_profile_basic.modified";
		$this->m_paramDbMap["quotes"] = "users_profile_personal.quotes";
		$this->m_paramDbMap["relationship_status"] = "users_profile_rel.status";
		$this->m_paramDbMap["religion"] = "users_profile_basic.religion";
		$this->m_paramDbMap["sex"] = "users_profile_basic.sex";
		$this->m_paramDbMap["significant_other_id"] = "users_profile_rel.significant_other";
		$this->m_paramDbMap["status"] = "Status";
		$this->m_paramDbMap["timezone"] = "users_profile_basic.timezone";
		$this->m_paramDbMap["tv"] = "users_profile_personal.tv";
		$this->m_paramDbMap["wall_count"] = "WallCount";
		$this->m_paramDbMap["work_history"] = "WorkHistory";
	}

}

?>
