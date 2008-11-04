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

class MockClient {

	public $method = array();

	public function auth_getSession($auth_token) {
		$result = $this->call_method('facebook.auth.getSession', array('auth_token'=>$auth_token));
		return $result;
	}

	public function admin_getAppProperties( $fields, $aid=null, $name=null ) {
		$result = null;

		if ( !empty($aid) ) {
			$result = $this->call_method( 'facebook.admin.getAppProperties', array( "aid"=>$aid, "properties"=>$fields ));
		} else {
			$result = $this->call_method( 'facebook.admin.getAppProperties', array( "properties"=>$fields ));
		}

		return $result;
	}
	 
	public function events_get($uid, $eids, $start_time, $end_time, $rsvp_status) {
		return $this->call_method('facebook.events.get', array( 'uid' => $uid, 'eids' => $eids, 'start_time' => $start_time, 'end_time' => $end_time, 'rsvp_status' => $rsvp_status));
	}

	public function events_getMembers($eid) {
		return $this->call_method('facebook.events.getMembers', array('eid' => $eid));
	}

	public function fql_query($query) {
		return $this->call_method('facebook.fql.query', array('query' => $query));
	}

	public function feed_publishStoryToUser($title, $body,
	$image_1=null, $image_1_link=null, $image_2=null, $image_2_link=null,
	$image_3=null, $image_3_link=null, $image_4=null, $image_4_link=null) {
		return $this->call_method('facebook.feed.publishStoryToUser',
		array('title' => $title, 'body' => $body,
            'image_1' => $image_1, 'image_1_link' => $image_1_link,
            'image_2' => $image_2, 'image_2_link' => $image_2_link,
            'image_3' => $image_3, 'image_3_link' => $image_3_link,
            'image_4' => $image_4, 'image_4_link' => $image_4_link));
	}

	public function feed_publishActionOfUser($title, $body,
	$image_1=null, $image_1_link=null, $image_2=null, $image_2_link=null,
	$image_3=null, $image_3_link=null, $image_4=null, $image_4_link=null) {
		return $this->call_method('facebook.feed.publishActionOfUser',
		array('title' => $title, 'body' => $body,
            'image_1' => $image_1, 'image_1_link' => $image_1_link,
            'image_2' => $image_2, 'image_2_link' => $image_2_link,
            'image_3' => $image_3, 'image_3_link' => $image_3_link,
            'image_4' => $image_4, 'image_4_link' => $image_4_link));
	}

	public function feed_publishTemplatizedAction($actor_id, $title_template, $title_data,
	$body_template, $body_data, $body_general,
	$image_1=null, $image_1_link=null, $image_2=null, $image_2_link=null,
	$image_3=null, $image_3_link=null, $image_4=null, $image_4_link=null,
	$target_ids='') {
		return $this->call_method('facebook.feed.publishTemplatizedAction',
		array('actor_id' => $actor_id,
            'title_template' => $title_template,
            'title_data' => $title_data,
            'body_template' => $body_template,
            'body_data' => $body_data,
            'body_general' => $body_general,
            'image_1' => $image_1, 'image_1_link' => $image_1_link,
            'image_2' => $image_2, 'image_2_link' => $image_2_link,
            'image_3' => $image_3, 'image_3_link' => $image_3_link,
            'image_4' => $image_4, 'image_4_link' => $image_4_link,
            'target_ids' => $target_ids));
	}

	public function friends_areFriends($uids1, $uids2) {
		return $this->call_method('facebook.friends.areFriends', array('uids1'=>$uids1, 'uids2'=>$uids2));
	}

	public function friends_get( $uid = null ) {

		if ( $uid == null )
		return $this->call_method('facebook.friends.get', array());
		else
		return $this->call_method('facebook.friends.get', array('uid'=>$uid));
	}

	public function friends_getAppUsers() {
		return $this->call_method('facebook.friends.getAppUsers', array());
	}

	public function groups_get($uid, $gids) {
		return $this->call_method('facebook.groups.get', array( 'uid' => $uid, 'gids' => $gids));
	}

	public function groups_getMembers($gid) {
		return $this->call_method('facebook.groups.getMembers', array('gid' => $gid));
	}

	public function notifications_get() {
		return $this->call_method('facebook.notifications.get', array());
	}

	public function notifications_send($to_ids, $notification) {
		return $this->call_method('facebook.notifications.send', array('to_ids' => $to_ids, 'notification' => $notification));
	}

	public function notifications_sendEmail($recipients, $subject, $text, $fbml) {
		return $this->call_method('facebook.notifications.sendEmail', array('recipients' => $recipients, 'subject' => $subject, 'text' => $text, 'fbml' => $fbml));
	}

	public function pages_getInfo($page_ids, $fields, $uid, $type) {
		return $this->call_method('facebook.pages.getInfo', array('page_ids' => $page_ids, 'fields' => $fields, 'uid' => $uid, 'type' => $type));
	}

	public function pages_isAdmin($page_id) {
		return $this->call_method('facebook.pages.isAdmin', array('page_id' => $page_id));
	}

	public function pages_isAppAdded() {
		if (isset($this->added)) {
			return $this->added;
		}
		return $this->call_method('facebook.pages.isAppAdded', array());
	}

	public function pages_isFan($page_id, $uid) {
		return $this->call_method('facebook.pages.isFan', array('page_id' => $page_id, 'uid' => $uid));
	}

	public function photos_get($subj_id, $aid, $pids) {
		return $this->call_method('facebook.photos.get',
		array('subj_id' => $subj_id, 'aid' => $aid, 'pids' => $pids));
	}

	public function photos_getAlbums($uid, $aids) {
		return $this->call_method('facebook.photos.getAlbums', array('uid' => $uid, 'aids' => $aids));
	}

	public function photos_getTags($pids) {
		return $this->call_method('facebook.photos.getTags', array('pids' => $pids));
	}

	public function users_getInfo($uids, $fields) {
		return $this->call_method('facebook.users.getInfo', array('uids' => $uids, 'fields' => $fields));
	}

	public function users_getLoggedInUser() {
		return $this->call_method('facebook.users.getLoggedInUser', array());
	}

	public function users_isAppAdded( $uid = null, $aid = null ) {
		if ( $uid == null && $aid == null && isset($this->added)) {
			return $this->added;
		}

		if ( $uid == null && $aid == null ) {
			return $this->call_method('facebook.users.isAppAdded', array());
		} else {

			if ( $uid == null ) {
				return $this->call_method('facebook.users.isAppAdded', array('aid'=>$aid));
			} else if ( $aid = null ) {
				return $this->call_method('facebook.users.isAppAdded', array('uid'=>$uid));
			} else {
				return $this->call_method('facebook.users.isAppAdded', array('uid'=>$uid, 'aid'=>$aid));
			}
		}

	}

	function profile_setFBML($markup, $uid = null, $profile='', $profile_action='', $mobile_profile='') {
		return $this->call_method('facebook.profile.setFBML', array('markup' => $markup, 'uid' => $uid, 'profile' => $profile, 'profile_action' => $profile_action, 'mobile_profile' => $mobile_profile));
	}

	public function profile_getFBML($uid) {
		return $this->call_method('facebook.profile.getFBML', array('uid' => $uid));
	}

	public function fbml_refreshImgSrc($url) {
		return $this->call_method('facebook.fbml.refreshImgSrc', array('url' => $url));
	}

	public function fbml_refreshRefUrl($url) {
		return $this->call_method('facebook.fbml.refreshRefUrl', array('url' => $url));
	}

	public function fbml_setRefHandle($handle, $fbml) {
		return $this->call_method('facebook.fbml.setRefHandle', array('handle' => $handle, 'fbml' => $fbml));
	}

	function marketplace_getCategories() {
		return $this->call_method('facebook.marketplace.getCategories', array());
	}

	function marketplace_getSubCategories($category) {
		return $this->call_method('facebook.marketplace.getSubCategories', array('category' => $category));
	}

	function marketplace_getListings($listing_ids, $uids) {
		return $this->call_method('facebook.marketplace.getListings', array('listing_ids' => $listing_ids, 'uids' => $uids));
	}

	function marketplace_search($category, $subcategory, $query) {
		return $this->call_method('facebook.marketplace.search', array('category' => $category, 'subcategory' => $subcategory, 'query' => $query));
	}

	function marketplace_removeListing($listing_id, $status='DEFAULT') {
		return $this->call_method('facebook.marketplace.removeListing', array('listing_id'=>$listing_id, 'status'=>$status));
	}

	function marketplace_createListing($listing_id, $show_on_profile, $attrs) {
		return $this->call_method('facebook.marketplace.createListing', array('listing_id'=>$listing_id, 'show_on_profile'=>$show_on_profile, 'listing_attrs'=>json_encode($attrs)));
	}


	/////////////////////////////////////////////////////////////////////////////
	// Data Store API

	public function data_setUserPreference($pref_id, $value) {
		return $this->call_method ('facebook.data.setUserPreference', array('pref_id' => $pref_id, 'value' => $value));
	}

	public function data_setUserPreferences($values, $replace = false) {
		return $this->call_method ('facebook.data.setUserPreferences', array('values' => json_encode($values), 'replace' => $replace));
	}

	public function data_getUserPreference($pref_id) {
		return $this->call_method ('facebook.data.getUserPreference', array('pref_id' => $pref_id));
	}

	public function data_getUserPreferences() {
		return $this->call_method ('facebook.data.getUserPreferences', array());
	}

	public function data_createObjectType($name) {
		return $this->call_method ('facebook.data.createObjectType', array('name' => $name));
	}

	public function data_dropObjectType($obj_type) {
		return $this->call_method ('facebook.data.dropObjectType', array('obj_type' => $obj_type));
	}

	public function data_renameObjectType($obj_type, $new_name) {
		return $this->call_method ('facebook.data.renameObjectType', array('obj_type' => $obj_type, 'new_name' => $new_name));
	}

	public function data_defineObjectProperty($obj_type, $prop_name, $prop_type) {
		return $this->call_method ('facebook.data.defineObjectProperty', array('obj_type' => $obj_type, 'prop_name' => $prop_name, 'prop_type' => $prop_type));
	}

	public function data_undefineObjectProperty($obj_type, $prop_name) {
		return $this->call_method ('facebook.data.undefineObjectProperty', array('obj_type' => $obj_type, 'prop_name' => $prop_name));
	}

	public function data_renameObjectProperty($obj_type, $prop_name, $new_name) {
		return $this->call_method ('facebook.data.renameObjectProperty', array('obj_type' => $obj_type, 'prop_name' => $prop_name,'new_name' => $new_name));
	}

	public function data_getObjectTypes() {
		return $this->call_method ('facebook.data.getObjectTypes', array());
	}

	public function data_getObjectType($obj_type) {
		return $this->call_method ('facebook.data.getObjectType', array('obj_type' => $obj_type));
	}

	public function data_createObject($obj_type, $properties = null) {
		return $this->call_method ('facebook.data.createObject', array('obj_type' => $obj_type, 'properties' => json_encode($properties)));
	}


	public function data_updateObject($obj_id, $properties, $replace = false) {
		return $this->call_method ('facebook.data.updateObject', array('obj_id' => $obj_id, 'properties' => json_encode($properties), 'replace' => $replace));
	}

	public function data_deleteObject($obj_id) {
		return $this->call_method ('facebook.data.deleteObject', array('obj_id' => $obj_id));
	}


	public function data_deleteObjects($obj_ids) {
		return $this->call_method ('facebook.data.deleteObjects', array('obj_ids' => json_encode($obj_ids)));
	}

	public function data_getObjectProperty($obj_id, $prop_name) {
		return $this->call_method ('facebook.data.getObjectProperty', array('obj_id' => $obj_id, 'prop_name' => $prop_name));
	}

	public function data_getObject($obj_id, $prop_names = null) {
		return $this->call_method ('facebook.data.getObject', array('obj_id' => $obj_id, 'prop_names' => json_encode($prop_names)));
	}

	public function data_getObjects($obj_ids, $prop_names = null) {
		return $this->call_method ('facebook.data.getObjects', array('obj_ids' => json_encode($obj_ids), 'prop_names' => json_encode($prop_names)));
	}

	public function data_setObjectProperty($obj_id, $prop_name, $prop_value) {
		return $this->call_method ('facebook.data.setObjectProperty', array('obj_id' => $obj_id, 'prop_name' => $prop_name, 'prop_value' => $prop_value));
	}

	public function data_getHashValue($obj_type, $key, $prop_name = null) {
		return $this->call_method ('facebook.data.getHashValue', array('obj_type' => $obj_type, 'key' => $key, 'prop_name' => $prop_name));
	}

	public function data_setHashValue($obj_type, $key, $value, $prop_name = null) {
		return $this->call_method ('facebook.data.setHashValue', array('obj_type' => $obj_type, 'key' => $key, 'value' => $value, 'prop_name' => $prop_name));
	}

	public function data_incHashValue($obj_type, $key, $prop_name, $increment = 1) {
		return $this->call_method ('facebook.data.incHashValue', array('obj_type' => $obj_type, 'key' => $key, 'prop_name' => $prop_name, 'increment' => $increment));
	}

	public function data_removeHashKey($obj_type, $key) {
		return $this->call_method ('facebook.data.removeHashKey', array('obj_type' => $obj_type, 'key' => $key));
	}

	public function data_removeHashKeys($obj_type, $keys) {
		return $this->call_method ('facebook.data.removeHashKeys', array('obj_type' => $obj_type, 'keys' => json_encode($keys)));
	}

	public function data_defineAssociation($name, $assoc_type, $assoc_info1, $assoc_info2, $inverse = null) {
		return $this->call_method ('facebook.data.defineAssociation', array('name' => $name, 'assoc_type' => $assoc_type, 'assoc_info1' => json_encode($assoc_info1), 'assoc_info2' => json_encode($assoc_info2), 'inverse' => $inverse));
	}

	public function data_undefineAssociation($name) {
		return $this->call_method ('facebook.data.undefineAssociation', array('name' => $name));
	}

	public function data_renameAssociation($name, $new_name, $new_alias1 = null, $new_alias2 = null) {
		return $this->call_method ('facebook.data.renameAssociation', array('name' => $name, 'new_name' => $new_name, 'new_alias1' => $new_alias1, 'new_alias2' => $new_alias2));
	}

	public function data_getAssociationDefinition($name) {
		return $this->call_method ('facebook.data.getAssociationDefinition', array('name' => $name));
	}

	public function data_getAssociationDefinitions() {
		return $this->call_method('facebook.data.getAssociationDefinitions', array());
	}

	public function data_setAssociation($name, $obj_id1, $obj_id2, $data = null, $assoc_time = null) {
		return $this->call_method ('facebook.data.setAssociation', array('name' => $name, 'obj_id1' => $obj_id1, 'obj_id2' => $obj_id2, 'data' => $data, 'assoc_time' => $assoc_time));
	}

	public function data_setAssociations($assocs, $name = null) {
		return $this->call_method ('facebook.data.setAssociations', array('assocs' => json_encode($assocs), 'name' => $name));
	}

	public function data_removeAssociation($name, $obj_id1, $obj_id2) {
		return $this->call_method ('facebook.data.removeAssociation', array('name' => $name, 'obj_id1' => $obj_id1, 'obj_id2' => $obj_id2));
	}

	public function data_removeAssociations($assocs, $name = null) {
		return $this->call_method ('facebook.data.removeAssociations', array('assocs' => json_encode($assocs), 'name' => $name));
	}

	public function data_removeAssociatedObjects($name, $obj_id) {
		return $this->call_method ('facebook.data.removeAssociatedObjects', array('name' => $name, 'obj_id' => $obj_id));
	}

	public function data_getAssociatedObjects($name, $obj_id, $no_data = true) {
		return $this->call_method ('facebook.data.getAssociatedObjects', array('name' => $name, 'obj_id' => $obj_id, 'no_data' => $no_data));
	}

	public function data_getAssociatedObjectCount($name, $obj_id) {
		return $this->call_method ('facebook.data.getAssociatedObjectCount', array('name' => $name, 'obj_id' => $obj_id));
	}

	public function data_getAssociatedObjectCounts($name, $obj_ids) {
		return $this->call_method ('facebook.data.getAssociatedObjectCounts', array('name' => $name,'obj_ids' => json_encode($obj_ids)));
	}

	public function data_getAssociations($obj_id1, $obj_id2, $no_data = true) {
		return $this->call_method('facebook.data.getAssociations', array('obj_id1' => $obj_id1,'obj_id2' => $obj_id2, 'no_data' => $no_data));
	}

	/////////////////////////////////////////////////////////////////////////////
	// Ringside API
	 
	public function comments_get( $xid, $first = null, $count = null, $aid = null ) {
		return $this->call_method('ringside.comments.get', array('xid' => $xid, 'first' => $first, 'count' => $count, 'aid' => $aid ) );
	}
	 
	public function comments_add( $xid, $msg, $aid = null ) {
		return $this->call_method('ringside.comments.add', array('xid' => $xid, 'msg' => $msg, 'aid' => $aid ) );
	}

	public function comments_count( $xid, $aid = null ) {
		return $this->call_method('ringside.comments.count', array('xid' => $xid, 'aid' => $aid ) );
	}

	public function comments_delete( $xid, $cid, $aid = null ) {
		return $this->call_method('ringside.comments.delete', array('xid' => $xid, 'cid' => $cid, 'aid' => $aid ) );
	}

    public function feed_get( $uid = null,Ê$actorid = null, $friends = false, $actions = true, $stories = true ) {
        return $this->call_method('ringside.feed.get', array( 'uid' => $uid, 'actorid'=>$actorid, 'friends'=>false, 'actions'=>$actions, 'stories'=>$stories ) );
    }	
	
	public function subscriptions_get_app_plans( $aid ) {
		return $this->call_method('ringside.subscriptions.getAppPlans', array( 'aid' => $aid ) );

	}

	public function social_pay_user_hasPaid( $uid, $nid, $app_id, $plans = null ) {
        return $this->call_method( 'ringside.socialPay.userHasPaid', array( 'uid'=>$uid, 'nid'=>$nid, 'aid' => $app_id, 'plans'=>$plans ) );
	}
	
	/* UTILITY FUNCTIONS */
	public function call_method($method, $params) {
		$result = $this->method[$method];
		if (is_array($result) && isset($result['error_code'])) {
			throw new MockClientException($result['error_msg'], $result['error_code']);
		}
		return $result;
	}

}

class MockClientException extends Exception {
}

?>
