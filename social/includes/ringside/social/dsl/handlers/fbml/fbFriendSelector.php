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

class fbFriendSelector {
    
   private $are_friends;

   function doStartTag( $application, $parentHandler, $args ) {

      // Some special rule apply if we are inside fb:request-form
      if ( $parentHandler != null ) {
          
      }

      $uid = isset( $args['uid'] )  ? $args['uid'] : null;
      $name = isset( $args['name'] )  ? $args['name'] : 'friend_selector_name';
      $idname = isset( $args['idname'] )  ? $args['idname'] : 'friend_selector_id';
      $include_me = isset( $args['include_me'] )  ? ($args['include_me']=='true'?true:false) : false;

      $client = $application->getClient();
      $uids = $client->friends_get( $uid );
      if ( !isset($uids) || !is_array($uids) ) { 
         $uids = array();
      }
      
      if ( $include_me ) {
         $uids[] = $application->getCurrentUser() ;
      }
            
      if ( empty( $uids ) ) {
         $friendUrl = RingsideSocialConfig::$webRoot . "/friends.php?view=add_friend";    
         echo " <a href='$friendUrl'>Add some friends </a>";
      } else {
          
         $response = $client->users_getInfo( $uids, 'first_name,last_name');

         echo "<select name=\"$name\" idname=\"$idname\">";
         foreach ( $response as $friend ){
            echo "<option value=\"{$friend['uid']}\">{$friend['first_name']} {$friend['last_name']}</option>";
         }
         echo "</select>";
      }

      return false;

   }

   function doEndTag( $application, $parentHandler, $args ) {

   }
   
   function isEmpty()
   {
   		return true;
   }
   
   function getType()
   {
   		return 'none';
   }
   
    
}

?>
