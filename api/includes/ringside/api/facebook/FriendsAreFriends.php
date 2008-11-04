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

require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Friends.php");

/**
 * The Friends.areFriends API
 */
class FriendsAreFriends extends Api_DefaultRest
{
	/** Array of user id 1 */
	private $uid1;
	
	/** Array of user id 2 */
	private $uid2;
	
	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		$this->uid1 = $this->getRequiredApiParam('uids1');
		$this->uid2 = $this->getRequiredApiParam('uids2');
	}
	
	/**
	 * Execute the Friends.areFriends method
	 *
	 * @return An array of values as xml.  There is one element in the outer array
	 * named 'friend_info'.  This element is an array made up of 3 sub-elements which are defined as
	 * 'uid1', 'uid2', 'are_friends'.  'uid1' is user id 1, 'uid2' is user id 2, and 'are_friends' is 
	 * either a 0 or 1.  
	 * 
	 * For example if the uids in the get request look like
	 * http://www.acme.com/foo?uid1=1,1,1&uid2=2,3,4 and assuming 1 and 2 are friends and 1 and 3 are friends
	 * and 1 and 4 are not friends then the return value would be 
	 *  array( 
	 *     'friend_info' => array(
	 *        array( 'uid1'=>1, 'uid2'=>2, 'are_friends'=>1 ),
	 *        array( 'uid1'=>1, 'uid2'=>3, 'are_friends'=>1 ),
	 *        array( 'uid1'=>1, 'uid2'=>4, 'are_friends'=>0 )
	 *     )
	 * )
	 */
	public function execute()
	{
		return Api_Bo_Friends::getFriendsAreFriends($this->uid1, $this->uid2);
	}
}

?>
