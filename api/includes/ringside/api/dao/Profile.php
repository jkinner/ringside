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

require_once ('ringside/api/dao/records/RingsideUser.php');
require_once ('ringside/api/dao/records/RingsideUsersProfile.php');
require_once ('ringside/api/dao/records/RingsideUsersProContact.php');
require_once ('ringside/api/dao/records/RingsideUsersProEcontact.php');
require_once ('ringside/api/dao/records/RingsideUsersProSchool.php');
require_once ('ringside/api/dao/records/RingsideUsersProWork.php');

/**
 * A DAO object which can manipulate an entire user profile.
 */
class Api_Dao_Profile
{
	protected static $simpleProperties = array('first_name','last_name','dob','sex','political',
										  'religion','last_updated','timezone','status_message',
										  'status_update_time','pic_url','pic_big_url','pic_small_url',
										  'pic_square_url','activities','interests','music','tv','movies',
										  'books','quotes','about','relationship_status','alternate_name',
										  'significant_other','meeting_for','meeting_sex','layout');
	
	protected static $complexProperties = array('contact' => 'RingsideUsersProContact',
												'econtact' => 'RingsideUsersProEcontact',
												'schools' => 'RingsideUsersProSchool',
												'work' => 'RingsideUsersProWork');
	
	
	public static function hasProperty($propertyName)
	{
		return (in_array($propertyName, self::$simpleProperties) ||
				array_key_exists($propertyName, self::$complexProperties));	
	}
	
	public static function createProfile($userId, $domainId, $props = array())
	{
		$p = new RingsideUsersProfile();
		
		$p->user_id = $userId;
		$p->domain_id = $domainId;
		
		//handle simple properties first
		foreach ($props as $pname => $pval) {
			if (in_array($pname, self::$simpleProperties)) {
				$p->$pname = $pval;	
			}
		}		
		
		//save the main profile DAO
		if (!$p->trySave()) {
			throw new Exception("Could not save profile DAO.");
		}
		$pid = $p->id;
		
		//handle complex properties, child DAOs of profile
		foreach ($props as $pname => $pval) {
			if (array_key_exists($pname, self::$complexProperties)) {
				$daoClass = self::$complexProperties[$pname];
				foreach ($pval as $complexObj) {								
					$daoInstance = new $daoClass();
					$daoInstance->profile_id = $pid;
					foreach ($complexObj as $cpname => $cpval) {
						if (!is_array($cpval)) {
							//error_log("daoClass=$daoClass, cpname=$cpname, cpval=$cpval");
							$daoInstance->$cpname = $cpval;						
						}
					}
					if (!$daoInstance->trySave()) {
						throw new Exception("Could not save profile DAO for $daoClass.");
					}			
				}
			}
		}
		
		return true;
	}
	
	public static function updateProfile($userId, $domainId, $props)
	{
		$p = self::getDoctrineProfile($userId, $domainId);
		if ($p == NULL) return false;
		
		//do simple properties first
		foreach ($props as $pname => $pval) {
			if (in_array($pname, self::$simpleProperties)) {
				$p->$pname = $pval;	
			}
		}
		
		if (!$p->trySave()) {
			throw new Exception("Could not update profile for uid=$userId and did=$domainId");
		}
		
		//handle complex properties, child DAOs of profile
		foreach ($props as $pname => $pval) {
			if (array_key_exists($pname, self::$complexProperties)) {		
				$daoClass = self::$complexProperties[$pname];
				//get Doctrine_Collection of complex values
				$coll = $p->$daoClass;				
				
				foreach ($pval as $complexObj) {	
					if (!array_key_exists('id', $complexObj)) {							
						throw new Exception("Profile sub-DAO must contain id, uid=$userId, did=$domainId, property=$pname");
					}
					//find the record given the id
					$rec = NULL;
					foreach ($coll->getKeys() as $k) {
						$r = $coll->get($k);
						if ($r->id == intval($complexObj['id'])) {
							$rec = $r;
							break;
						}
					}					
					if ($rec != NULL){	
						if (!array_key_exists('delete', $complexObj)) {							
							foreach ($complexObj as $elname => $elval) {
								$rec->$elname = $elval;
							}						
							if (!$rec->trySave()) {
								throw new Exception("Failed to update profile property $pname with id={$complexObj['id']}," .
												    ", uid=$userId, did=$domainId");
							}
						} else {
							//delete the record							
							if (!$rec->delete()) {
								throw new Exception("Failed to delete profile property $pname with id={$complexObj['id']}," .
												    ", uid=$userId, did=$domainId");
							}
						}
					}
				}
			}
		}
		
		return true;
	}
	
	protected static function getDoctrineProfile($userId, $domainId, $props = NULL)
	{
		if ($props == NULL) {
			$props = array_merge(self::$simpleProperties, array_keys(self::$complexProperties));
		}
	
		$q = Doctrine_Query::create();
				
		$dqlSelect = array('profile.user_id');		
		$joins = array();
		
		foreach ($props as $pname) {
			if (in_array($pname, self::$simpleProperties)) {
				$dqlSelect[] = "profile.$pname";	
			} else if (array_key_exists($pname, self::$complexProperties)) {				
				$daoClass = self::$complexProperties[$pname];
				$dqlSelect[] = "$pname.*";								
				$joins[] = "profile.$daoClass $pname";
			} else {
				throw new Exception("Unknown user profile property: '$pname'");
			}
		}
		
		//set up Doctrine query		
		$q->select(implode(',', $dqlSelect));
		$q->from('RingsideUsersProfile profile');
		
		foreach ($joins as $j) $q->leftJoin($j);		
		
		$q->where("profile.user_id=$userId AND profile.domain_id='$domainId'");

		/*
		error_log("dql='" . $q->getDql() . "'");
		error_log("");
		error_log("sql='" . $q->getSqlQuery() . "'");
		*/
				
		$p = $q->execute();
		if (count($p) > 0) {
			return $p[0];
		}
		return NULL;
	}
	
	public static function getProfile($userId, $domainId, $props = NULL)
	{
		$p = self::getDoctrineProfile($userId, $domainId, $props);
						
		if ($p != NULL) {
			$parr = $p->toArray();
			//rename complex property keys
			foreach (self::$complexProperties as $cname => $cTableName) {
				if (array_key_exists($cTableName, $parr)) {
					//rename the table to the mapped name
					$parr[$cname] = $parr[$cTableName];
					unset($parr[$cTableName]);
					//remove profile_id attribute
					foreach ($parr[$cname] as $indx => $earr) {						
						unset($parr[$cname][$indx]['profile_id']);
					}	
				}
			}			
			return $parr;
		}
		return NULL;
	}
	
	public static function deleteProfile($userId, $domainId)
	{
		$p = self::getDoctrineProfile($userId, $domainId);
		if ($p != NULL) {
			//delete sub-DAOs
			$subTables = array_values(self::$complexProperties);
			foreach ($subTables as $tbl) {
				if (!$p->$tbl->delete()) {
					throw new Exception("Couldn't' delete user profile field '$tbl', uid=$userId, did=$domainId");
				}
			}
			return $p->delete();
		}
		return false;
	}
}

?>
