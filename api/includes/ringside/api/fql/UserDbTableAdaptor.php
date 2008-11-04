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

require_once( "ringside/api/fql/DbTableAdaptor.php" );
require_once( "ringside/api/dao/Friends.php" );

class UserDbTableAdaptor extends DbTableAdaptor
{

	public function retrieveFields($engine, $parsedStatement, $vars = array())
	{
		$rootObj = parent::retrieveFields($engine, $parsedStatement, $vars);
		
		//get friend list
		$userId = $vars['USER_ID'];
		$friendList = Api_Dao_Friends::friendsGetFriends( $userId, $engine->getDbConnection() );
		  		
  		//filter out un-viewable parameters for non-friends
  		$allowedFields = array('first_name', 'last_name', 'pic_small', 'uid');
  		foreach ($rootObj['user'] as $indx => $userProps) {
  			$uid = $userProps['uid'];
  			if (!in_array($uid, $friendList) && ($uid != $userId)) {
  				foreach ($userProps as $fld => $val) {
  					if (!in_array($fld, $allowedFields)) {
  						$rootObj['user'][$indx][$fld] = null;
  					}
  				}
  			}  		
  		}
		
		return $rootObj;
	}
}

?>
