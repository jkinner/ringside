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
require_once ('BaseAPITestCase.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/rest/BetterFriendsGet.php");
require_once ("ringside/rest/BetterFriendsAdd.php");
require_once ("ringside/rest/BetterFriendsRemove.php");

class BetterFriendsGetTestCase extends BaseAPITestCase {
    
    public function testAddSomeFriends() {
        $params = array("domain_key" => "1","owner_uid" => "1000","target_uid" => "5000");
        $apiCall = $this->initRest(new BetterFriendsAdd(), $params);
		$resp = $apiCall->execute();
		$this->assertTrue($resp);
		
		
        $params = array("domain_key" => "1","owner_uid" => "1000","target_uid" => "5001");
        $apiCall = $this->initRest(new BetterFriendsAdd(), $params);
		$resp = $apiCall->execute();
		$this->assertTrue($resp);
    }

	public function testGetFriends() {
	    $params = array("domain_key" => "1","owner_uid" => "1000");
		$apiCall = $this->initRest(new BetterFriendsGet(), $params);
		$resp = $apiCall->execute();
		//error_log(var_export($resp,1));
		$this->assertEquals($resp,array('uid'=>array(5000,5001)));

	}
	
	public function testRemove() {
	    $params = array("domain_key" => "1","owner_uid" => "1000","target_uid" => "5000");
	    $apiCall = $this->initRest(new BetterFriendsRemove(), $params);
		$resp = $apiCall->execute();
		$this->assertTrue($resp);
		
    	$params = array("domain_key" => "1","owner_uid" => "1000","target_uid" => "5001");
	    $apiCall = $this->initRest(new BetterFriendsRemove(), $params);
		$resp = $apiCall->execute();
		$this->assertTrue($resp);
	}

}
?>
