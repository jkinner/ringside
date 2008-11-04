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

require_once('ringside/api/db/RingsideApiDbDatabase.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RingsideSocialDbSession
{
	/**
	 * Records a session creation event in the session history table
	 *
	 * @param int $pid
	 * @param int $uid
	 * @param string $userName
	 * @param string $network
	 * @param string $sessionKey
	 */
	public static function logSessionHistory($pid, $uid, $userName, $network, $sessionKey, $trust_key)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$pid = mysql_real_escape_string($pid);
		$uid = mysql_real_escape_string($uid);
		$userName = mysql_real_escape_string($userName);
		$network = mysql_real_escape_string($network);
		$sessionKey = mysql_real_escape_string($sessionKey);
		$trust_key = mysql_real_escape_string($trust_key);
			
		$sql = "INSERT into rs_social_session_history (trust_key, social_session_key, principal_id, uid, user_name, network_key)
				values('$trust_key', '$sessionKey', $pid, $uid, '$userName', '$network')";
			
		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			error_log("Mysql Error: ".mysql_error()." error number: ".mysql_errno() );
		}
	}

	/**
	 * Checks to see if this session has been recorded in the history table.
	 *
	 * @param string $sessionKey
	 * @return bool
	 *
	public static function doesSessionExist($sessionKey)
	{
		$dbCon = RingsideSocialDbUtil::getDatabaseConnection();
		$sessionKey = mysql_real_escape_string($sessionKey);
		$sql = "SELECT social_session_key
			FROM rs_social_session_history WHERE social_session_key='$sessionKey'";

		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			error_log("Mysql Error: ".mysql_error()." error number: ".mysql_errno() );
		}

		$row_count = mysql_num_rows($result);
		if($row_count > 0)
		{
			return true;
		}
		return false;

	}
	*/

	/**
	 * Get's the auth token approval class for this trust
	 *
	 * @param int $trust_key
	 * @return string
	 */
	public static function getTrustAuthority($trust_key)
	{
		if(!isset($trust_key))
		{
			return null;
		}
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$trust_key = mysql_real_escape_string($trust_key);
		$sql = "SELECT trust_key, trust_name, trust_auth_class, trust_auth_url FROM rs_trust_authorities WHERE trust_key='$trust_key'";

		$result = mysql_query( $sql, $dbCon );
		if (mysql_errno($dbCon))
		{
			throw new Exception( mysql_error(), mysql_errno() );
		}

		$row = mysql_fetch_array( $result );
		if($row)
		{
			return $row;
		}
		return null;
	}
}
?>
