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
require_once('ringside/api/clients/RingsideApiClientsRest.php');

class RingsideRestConstants {
   const RATINGS_SET_METHOD = 'ringside.ratings.set';
   const RATINGS_GET_METHOD = 'ringside.ratings.get';
   const RATINGS_GETAVERAGE_METHOD = 'ringside.ratings.getAverage';
   const ITEMS_SETINFO_METHOD = 'ringside.items.setInfo';
   const ITEMS_GETINFO_METHOD = 'ringside.items.getInfo';
   const ITEMS_REMOVE_METHOD = 'ringside.items.remove';
   const FAVORITES_CREATELIST_METHOD = 'ringside.favorites.createList';
   const FAVORITES_REMOVELIST_METHOD = 'ringside.favorites.removeList';
   const FAVORITES_GETLISTS_METHOD = 'ringside.favorites.getLists';
   const FAVORITES_SET_METHOD = 'ringside.favorites.set';
   const FAVORITES_SETFBML_METHOD = 'ringside.favorites.setFBML';
   const FAVORITES_GET_METHOD = 'ringside.favorites.get';
   const FAVORITES_GETUSERS_METHOD = 'ringside.favorites.getUsers';
   const FAVORITES_GETFBML_METHOD = 'ringside.favorites.getFBML';
   const FAVORITES_DELETE_METHOD = 'ringside.favorites.delete';
}

class RingsideRestClient extends RingsideApiClientsRest  {
   var $delegate;
   static $server_url;
   
   /**
    * Constructs a Ringside-specific REST client.
    *
    * @param OpenFBRestClient $delegate the delegate to be used for invoking the operations.
    */
   public function __construct($delegate) {
      $this->delegate = $delegate;
      $delegate->addServer('ringside', RingsideRestClient::$server_url);
   }
      
   /**
    * Sets the user's rating for an item.
    *
    * @param int $iid the item to rate
    * @param number $vote the score given by the user for the item
    * @param int $type the type of ratings being stored, otherwise the default rating
    */
   public function ratings_set($iid, $vote, $type = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['vote'] = $vote;
      $params['type'] = $type;
      
      return $this->delegate->call_method(RingsideRestConstants::RATINGS_SET_METHOD, $params); 
   }
    
   /**
    * Gets the rating given for an item by one or more users, or for the logged in user.
    *
    * Data format:
    * <code>
    * 
    * </code>
    * @param array $iids the items that are being rated
    * @param array $uids optional - the list of users to retrieve ratings for. Defaults to the logged in user if not provided
    * @return array of ratings
    */
   public function ratings_get($iids, $uids = null) {
   	$params = array();
   	$params['iids'] = $iids;
    	$params['uids'] = $uids;
      
      return $this->delegate->call_method(RingsideRestConstants::RATINGS_GET_METHOD, $params); 
   }
    
   /**
    * Returns the average rating information across the uids.
    *
    * @param int $iid the item used to calculate the average
    * @param array $uids the uids used to retrieve the ratings to calculate the average, otherwise the logged in user
    * @param int $type the type of rating to average across
    */
   public function ratings_getAverage($iid, $uids = null, $type = null) {
   	$params = array();
   	$params['iid'] = $iid;
     	$params['type'] = $type;
    	$params['uids'] = $uids;
      
      return $this->delegate->call_method(RingsideRestConstants::RATINGS_GETAVERAGE_METHOD, $params); 
   }
   
   /**
    * Sets the definition of an item.
    *
    * @param string $iid the identifier of the item
    * @param string $url the URL that refers directly to the item
    * @param string $refurl the URL of the page where the user can get information on the item
    * @param int $datatype optional - the type of the item, otherwise the default type
    */
   public function items_setInfo($iid, $url, $refurl, $datatype = null, $aid=null) {
      $params = array();
      $params['iid'] = $iid;
      $params['url'] = $url;
      $params['refurl'] = $refurl;
      $params['datatype'] = $datatype;
      $params['aid'] = $aid;
      
      return $this->delegate->call_method(RingsideRestConstants::ITEMS_SETINFO_METHOD, $params); 
   }
   
   /**
    * Removes the item so that it no longer appears in item queries.
    *
    * @param int $iid the ID of the item to remove
    */
   public function items_remove($iid, $datatype = null) {
      return $this->delegate->call_method(RingsideRestConstants::ITEMS_REMOVE_METHOD, array( "iid" => $iid, "datatype" => $datatype ));
   }
   
   /**
    * Gets the definition of a set of items.
    *
    * @param array $iids item identifiers for which to retrieve definitions
    * @param array $datatype optional - the set of data types to filter 
    */
   public function items_getInfo($iids, $datatype = null, $aid=null) {
      $params = array();
      $params['iids'] = $iids;
      $params['datatype'] = $datatype;
      $params['aid'] = $aid;
      
      return $this->delegate->call_method(RingsideRestConstants::ITEMS_GETINFO_METHOD, $params); 
   }

   /**
    * Creates a list of favorites for the logged in user.
    *
    * @param $name the name of the list
    * @return int the identifier of the list
    */
   public function favorites_createList($name) {
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_CREATELIST_METHOD, array( 'name' => $name )); 
   }

   /**
    * Removes a list of favorites for the logged in user. The favorites in the list may no longer be accessible
    * after this call. If an application wants to preserve the contents of the list, it should place the
    * favorites into another list.
    *
    * @param $lid the ID of the list
    * @return boolean true if the list was removed
    */
   public function favorites_removeList($lid) {
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_REMOVELIST_METHOD, array( 'name' => $name )); 
   }
   
   /**
    * Gets the favorites lists for a given user or for the logged-in user.
    *
    * @param int $uid optional - the user for whom to get the lists. If not specified, the logged in user will be used.
    */
   public function favorites_getLists($uid = null) {
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_GETLISTS_METHOD, array( 'uid' => $uid )); 
   }

   /**
    * Sets the item as a favorite for the user, optionally part of a user-scoped list ID. User-scoped
    * lists MUST be registered by using favorites_createList prior to being used in this method.
    *
    * @param int $iid the item id.
    * @param int $lid optional - the list the favorite should be added to, otherwise the default list
    */
   public function favorites_set($iid, $lid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_SET_METHOD, $params); 
   }
    
   /**
    * Gets the favorite items for users. If specified, the lid must have been registered via a call to
    * favorites_createList.
    *
    * @param array $uids the users for whom to retrieve favorite items, or the logged in user if not specified
    * @param int $lid optional - the list that is used to organize the favorites for the user(s), or the default list if not specified
    *
    * @return unknown
    */
   public function favorites_get($uids = null, $lid = null) {
      $params = array();
      
      $params['uids'] = $uids;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_GET_METHOD, $params); 
   }
   
   /**
    * Sets the item as a favorite for the user using an application-scoped list ID. Unlike
    * favorites_set, the lists do not need to be registered via favorites_createList.
    *
    * @param int $iid the item id.
    * @param int $alid optional - the list the favorite should be added to, otherwise the default list for the app
    */
   public function favorites_setApp($iid, $alid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['alid'] = $alid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_SET_METHOD, $params); 
   }
    
   /**
    * Gets the favorite items for users using an application-scoped list ID.
    *
    * @param array $uids the users for whom to retrieve favorite items, or the logged in user if not specified
    * @param int $alid optional - the list that is used to organize the favorites for the app, otherwise the default list for the app
    *
    * @return unknown
    */
   public function favorites_getApp($uids = null, $alid = null) {
      $params = array();
      
      $params['uids'] = $uids;
      $params['alid'] = $alid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_GET_METHOD, $params); 
   }
   
   /**
   * Checks whether the user (or users) has (have) marked this item as a favorite.
   *
   * @param string $iid the item to check
   * @param int $type the type of favorite list to check
   * @param array $uids optional - the uids to check, otherwise the logged in user
   * @param int $lids optional - the array of lists to check, otherwise the default list
   *
   * @return array users who have the item marked favorite.
   */
   public function favorites_getUsers($iid, $uids = null, $lid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['uids'] = $uids;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_GETUSERS_METHOD, $params);
   }

   /**
    * Gets the FBML description of the item that the user set.
    *
    * @param int $iid the item for which to retrieve the user's FBML description
    * @param array $uids optional - list of uids to retrieve FBML for, otherwise the logged in user
    * @param int $lid optional - the list to retrieve the FBML description for, otherwise the default list
    */
   public function favorites_getFBML($iid, $uids = null, $lid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['uids'] = $uids;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_GETFBML_METHOD, $params); 
   }
    
   /**
    * Sets the FBML description of the favorite within the list.
    *
    * @param int $iid the item to describe with the FBML.
    * @param string $fbml the FBML description to associate with the item in the favorites list.
    * @param int $lid optional - the list on which to set the description. The default list will be used as the default.
    */
   public function favorites_setFBML($iid, $fbml, $lid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['fbml'] = $fbml;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_SETFBML_METHOD, $params); 
   }

   /**
    * Marks the favorite as Deleted
    *
    * @param unknown_type $iid
    * @param unknown_type $lid
    * @return unknown
    */
   public function favorites_delete($iid, $lid = null) {
      $params = array();
      $params['iid'] = $iid;
      $params['lid'] = $lid;
      
      return $this->delegate->call_method(RingsideRestConstants::FAVORITES_DELETE_METHOD, $params); 
   }

}
