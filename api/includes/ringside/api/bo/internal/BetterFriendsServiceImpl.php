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
include_once 'ringside/api/ServiceFactory.php';
include_once 'ringside/api/bo/BetterFriendsService.php';
include_once 'ringside/api/dao/records/RingsideFriend.php';

class Api_Bo_BetterFriendsServiceImpl extends Api_Bo_BetterFriendsService {
    
    public function get($domain_key,$owner_uid) {
        if(!$domain_key || !$owner_uid)
		    return false;
		    
        //$domain_table = Doctrine::getTable('BetterFriends');
	    //$friends = $domain_table->findBydomain_keyAndFriend_two_uid($domainId);
	    $q = new Doctrine_Query();
		$q->select('from_id,to_id');
		$q->from('RingsideFriend');
		$q->where("domain_key='$domain_key' and from_id='$owner_uid' or to_id='$owner_uid'");
		
		$res = $q->execute();
		$res = $res->toArray();
		if (count($res) > 0) {
			
		    foreach($res as $r) {
		        if($r['from_id']==$owner_uid)
		            $friends[] = $r['to_id'];
		        elseif($r['to_id']==$owner_uid)
		            $friends[] = $r['from_id'];
		    }
		    
			return $friends;
		}
		return false;
    }
  
	public function add($domain_key,$owner_uid,$target_uid) {
        // error_log('in the add method of better friends **********');
        // error_log("domain=".$domain_key." owner=".$owner_uid." target=".$target_uid);
	    if(!$domain_key || !$owner_uid || !$target_uid)
		    return false;
		elseif(is_array($this->get($domain_key,$owner_uid)) && array_search($target_uid,$this->get($domain_key,$owner_uid))!==false)
		    return false; // if the owner_uid and target_uid are already friends
		    
	    $friend = new RingsideFriend();
		$friend->domain_key = $domain_key;
		$friend->from_id = $owner_uid;
		$friend->to_id = $target_uid;
		$friend->access = 1;
		$friend->status = 2;
		if ($friend->trySave())
		    return true;
		else
		    throw new Exception("[BetterFriendsImpl] could not create friend with domain=$domain_key, owner_uid=$owner_uid, target_uid=$target_uid");
	}
  
	public function remove($domain_key,$owner_uid,$target_uid) {
	    if(!$domain_key || !$owner_uid || !$target_uid)
		    return false;
		
		$q = new Doctrine_Query();
		$q->select('id');
		$q->from('RingsideFriend');
		$q->where("domain_key='$domain_key' and from_id='$owner_uid' and to_id='$target_uid'");
		$res = $q->execute();
		if (count($res) > 0) {
		    $res = $res->toArray();
		    $friend_id = $res[0]['id'];
		    $row = Doctrine::getTable('RingsideFriend')->find($friend_id);
		    if($row !==false)
    			return $row->delete();
			else
			    return false;
		}
		
		return false;
	}

	
}

?>