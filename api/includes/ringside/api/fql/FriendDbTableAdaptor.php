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

require_once("DbTableAdaptor.php");
require_once('ringside/api/dao/Friends.php');

class FriendDbTableAdaptor extends DbTableAdaptor
{	
	public function retrieveFields($engine, $parsedStatement, $vars = array())
   {
   	$fieldNames = $parsedStatement->getSelectFields();
   	$whereTokens = $parsedStatement->getWhereFields();
   	
   	//verify SELECT
   	if ((count($fieldNames) != 1) || 
				!(in_array("uid1", $fieldNames) xor in_array("uid2", $fieldNames)))
   	{
   		throw new FQLException("Only 'uid1' or 'uid2' can be selected from the friend table.");
   	}   	
   	$uidFieldName = $fieldNames[0];
   	   	
   	//verify WHERE
   	$uidStr = $this->getUidStringFromWhere($whereTokens);   	
   	
   	//restrict to friends of $uid
   	$uid = $vars["USER_ID"];
   	
   	// RXF - Commented out and using DAO layer.
//  	$fg = new FriendsGet($uid, array("uid" => $uid));
//  	$resp = $fg->execute();
//  	$ulist = $resp["uid"];
   // RXF - Using the DAO to get list of friends. 
   $ulist = Api_Dao_Friends::friendsGetFriends( $uid );
  		
  		$esqlFrom = "";
  		$esqlTo = "";
  		if (is_array($ulist) && (count($ulist) > 0)) {  		  		
  			$esqlFrom .= " AND from_id IN ($uid," . implode(",", $ulist) . ")";
  			$esqlTo .= " AND to_id IN ($uid," . implode(",", $ulist) . ")";
  		}  		
   	
   	$sql = "SELECT to_id AS uid FROM friends WHERE from_id $uidStr $esqlFrom" .
   			 "UNION SELECT from_id AS uid FROM friends WHERE to_id $uidStr $esqlTo";
   	
   	//print "\nfriendSQL='$sql'\n";
   	
   	$ds = mysql_query($sql, $engine->getDbConnection());
   	if (!$ds) {
      	throw new FQLException("Could not execute mapped FQL->SQL query: " .
      								  mysql_error() . "\nSQL='$sql'");
      }

      //construct response
      $obj = array();
      $obj["friend_info"] = array();
      while ($row = mysql_fetch_assoc($ds)) {
      	$obj["friend_info"][] = array($uidFieldName => $row["uid"]);
      }
   
      return $obj;
   }
   
   protected function getUidStringFromWhere($whereTokens)
   {	
   	$uidStr = "";
   	$record = false;
   	foreach ($whereTokens as $wtok) {
   		if ($record) $uidStr .= " $wtok ";
   		if (($wtok == "uid1") || ($wtok == "uid2")) {
   			if (!$record)  $record = true;
   		}   		
   	}
   	
   	return $uidStr;
   }


}

?>
