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

class GroupMemberDbTableAdaptor extends DbTableAdaptor
{	
	public function retrieveFields($engine, $parsedStatement, $vars = array())
   {
   	$whereTokens = $parsedStatement->getWhereFields();
   	
   	$allowedFields = array("gid","uid","positions");
   	   	
   	//check field validity
   	$fieldNames = array();
   	foreach ($parsedStatement->getSelectFields() as $fname) {
   		if (!in_array($fname, $allowedFields)) {
   			throw new FQLException("Invalid field '$fname' specified for group_member table.");
   		}
   		if ($fname != "positions") $fieldNames[] = $fname;
   	}
   	
   	//verify SELECT
   	if (!in_array("gid", $fieldNames) && !in_array("uid", $fieldNames)) {
   		throw new FQLException("'uid' or 'gid' must be specified in SELECT for group table.");
   	}   	
   	
   	$sql = "SELECT " . implode(",", $fieldNames) . " FROM groups_member WHERE " . implode(" ", $whereTokens);
   	
   	//print "\ngroupMemberSQL='$sql'\n";
   	
   	$ds = mysql_query($sql, $engine->getDbConnection());
   	if (!$ds) {
      	throw new FQLException("Could not execute mapped FQL->SQL query: " .
      								  mysql_error() . "\nSQL='$sql'");
      }

      $obj = array();
      $obj["group_member"] = array();
      while ($row = mysql_fetch_assoc($ds)) {
      	$vals = array();
      	foreach ($fieldNames as $fname) {
      		 $vals[$fname] = $row[$fname];
      	}
      	//TODO: implement positions
      	$vals["positions"] = array();
      	$obj["group_member"][] = $vals;
      }
   
      return $obj;
   }
   

}

?>
