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

// Trying to include RingsideSocialUtils will cause deadlock
require_once('ringside/api/db/RingsideApiDbDatabase.php');
include_once("ringside/api/config/RingsideApiConfig.php");

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RingsideSocialDbPrincipal
{
		/**
	 * Creates a principal, or returns the id of the principal if it already exist for this user name
	 *
	 * @param string $user_name (format: string@string)
	 */
	public static function createPrincipalForSubject($app_id, $network_id, $uid)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$user_name = mysql_real_escape_string($user_name);
		$sql = "SELECT * FROM principal WHERE user_name='$user_name'";
		$result = mysql_query( $sql, $dbCon );

		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}
		$count = mysql_num_rows($result);
		if($count == 0)
		{
			$sql = "INSERT INTO principal (user_name) VALUES ('$user_name')";
			$result = mysql_query( $sql, $dbCon );

			if (mysql_errno($dbCon))
			{
				throw new Exception( mysql_error(), mysql_errno() );
			}
			return mysql_insert_id ($dbCon);
		}else{
			$row = mysql_fetch_array( $result );
			if($row)
			{
				return $row['principal_id'];
			}
		}
		return null;
	}
	
	/**
	 * Creates a principal
	 */
	public static function createPrincipal( $app_id )
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$app_id = mysql_real_escape_string($app_id);
		$sql = "INSERT INTO principal (app_id) VALUES ($app_id)";
		$result = mysql_query( $sql, $dbCon );

		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}
		return mysql_insert_id ($dbCon);
	}

	/**
	 * Creates a principal, or returns the id of the principal if it already exist for this user
	 *
	 * @param string $uid the ID of the user
	 */
	public static function createPrincipalForUid($app_id, $uid)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$uid = mysql_real_escape_strign($uid);
		$app_id = mysql_real_escape_string($app_id);
		$sql = "SELECT principal.principal_id as principal_id FROM principal WHERE principal.uid = ".$uid." and principal.app_id = $app_id";
		$result = mysql_query( $sql, $dbCon );

		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}
		$count = mysql_num_rows($result);
		if($count == 0)
		{
			$sql = "INSERT INTO rs_principal (app_id) values($app_id)";
			$result = mysql_query( $sql, $dbCon );

			if (mysql_errno($dbCon))
			{
				throw new Exception( mysql_error(), mysql_errno() );
			}
			return mysql_insert_id ($dbCon);
		}else{
			$row = mysql_fetch_array( $result );
			if($row)
			{
				return $row['principal_id'];
			}
		}
		return null;
	}
	
	/**
	 * Constructs a principal object
	 *
	 * @param int $uid
	 * @param string $network_key
	 * @param string $user_name
	 * @return unknown
	 */
	public static function getPrincipalForSubject($uids, $network_key, $app_id, $user_name, $trust_key)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();

		if(!isset($uids))
		{
			error_log("getPrincipalForSubject: Unable to get principal id, no uid provided");
			return null;
		}

		if(!isset($app_id))
		{
			error_log("getPrincipalForSubject: Unable to get principal id, no app_id provided");
			return NULL;
		}
		
		if ( ! is_array($uids) ) {
			$uids = array($uids);
		}
		
		$db_uids = array();
		foreach ( $uids as $uid ) {
			$db_uids[] = mysql_real_escape_string($uid);
		}
		
		if(!isset($network_key))
		{
			$network_key = 'Ringside_Network';
		}

		if(!isset($user_name))
		{
			$user_name = $uid.'@'.$trust_key;
		}

		$uid_list = implode(',', $db_uids);
		$network_key = mysql_real_escape_string($network_key);
		$user_name = mysql_real_escape_string($user_name);
		$app_id = mysql_real_escape_string($app_id);
		
//		$sql = "SELECT id, principal_id, uid, network_key, user_name FROM rs_principal_map
//			WHERE uid=$uid AND network_key='$network_key' AND user_name='$user_name'";
		// TODO: Figure out if it is safe to ignore user_name
		$sql = "SELECT id, principal_id, uid, network_key, user_name FROM principal_map
			WHERE uid in ($uid_list) AND network_key='$network_key' AND app_id = $app_id";
		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}

		$results = array();
		if(mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array( $result );
			while($row)
			{
				$results[$row['uid']] = $row['principal_id'];
				$row = mysql_fetch_array( $result );
			}
		}
		
		// Make sure we return the same number of output entries as we received
		$final_results = array();
		foreach ( $uids as $uid ) {
			$final_result = array_key_exists($uid, $results)?$results[$uid]:null;
			$final_results[] = $final_result;
		}
		
		return $final_results;
	}

	/**
	 * Sets a subject on a principal
	 *
	 * @param int $pid
	 * @param int $uid
	 * @param string $network_key
	 * @param string $user_name
	 */
	public static function setSubject($pid, $uid, $network_key, $app_id, $user_name, $trust_key)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$pid = mysql_real_escape_string($pid);
		$uid = mysql_real_escape_string($uid);
		$network_key = mysql_real_escape_string($network_key);
		$app_id = mysql_real_escape_string($app_id);
		$user_name = mysql_real_escape_string($user_name);

		
		$sql = "INSERT INTO principal_map (principal_id, uid, network_key, app_id, user_name, trust_key)
			VALUES($pid, $uid, '$network_key', $app_id, '$user_name', '$trust_key')";
		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}
	}
	
	/**
	 * Constructs a subject from one or more principals
	 *
	 * @param array $pids the principal IDs
	 * @param string $network_key the network key, typically the Social Key from another Ringside installation
	 * @param string $trust_key the trust key
	 * 
	 * @return array the set of subjects in the network identified by the $network_key
	 */
	public static function getSubjectForPrincipal($pids, $network_key, $app_id, $trust_key)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();

		if(!isset($pids))
		{
			error_log("getSubjectForPrincipal: Unable to get subject id, no principal id provided");
			return null;
		}

		if(!isset($app_id))
		{
			error_log("getSubjectForPrincipal: Unable to get subject id, no app_id provided");
			return NULL;
		}
		
		if ( ! is_array($pids) ) {
			$pids = array($pids);
		}
		
		$db_pids = array();
		foreach ( $pids as $pid ) {
			$db_pids[] = mysql_real_escape_string($pid);
		}
		
		if(!isset($network_key))
		{
			$network_key = 'Ringside_Network';
		}

		$pid_list = implode(',', $db_pids);
		$network_key = mysql_real_escape_string($network_key);
		$app_id = mysql_real_escape_string($app_id);
		
		// TODO: Figure out if it is safe to ignore trust_key
		$sql = "SELECT id, principal_id, uid, network_key, user_name FROM principal_map
			WHERE principal_id in ($pid_list) AND network_key='$network_key' AND app_id = $app_id";
			
		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}

		$results = array();
		if(mysql_num_rows($result) != 0)
		{
			$row = mysql_fetch_array( $result );
			while($row)
			{
				$results[$row['principal_id']] = $row['uid'];
				$row = mysql_fetch_array( $result );
			}
		}
		
		// Make sure we return the same number of output entries as we received
		$final_results = array();
		foreach ( $pids as $pid ) {
			$final_results[] = array_key_exists($pid, $results)?$results[$pid]:null;
		}
		
		return $final_results;
	}
	
}
?>
