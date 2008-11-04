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

require_once( 'ringside/social/dsl/handlers/HandlerUtil.php');

class fbProfilePicHandler {

   private static $sizes = array( 
   		"thumb"=>array('w'=>50 ), 
   		"small"=>array('w'=>100 ),
   		"normal"=>array('w'=>250 ),
        "square"=>array('w'=>50 ) );
   
   function doStartTag( $application, $parentHandler, $args ) {

      $uid = isset( $args['uid'] ) ? $args['uid'] : null ;
      $size = isset( $args['size']) ? $args['size'] : "thumb";
      $linked = HandlerUtil::checkBoolArg('linked', $args, true );
      
      if ( empty( $uid ) ) {
         echo 'RUNTIME ERROR: fb:name: Required attribute "uid" not found in node fb:name';
         return false;
      } 
      
      if ( !array_key_exists($size, self::$sizes ) ) {
         echo 'RUNTIME ERROR: fb:profile-pic: Invalid size for profile pic ('.$size.')';
         return false;
      }
      
      // Get pic URL, get Name.
      $client = $application->getClient();
      $response = $client->users_getInfo( array($uid), array('first_name,last_name', 'pic'));
      
      if ( empty($response) || (count($response) == 0)) {
      	error_log("fb:profile-pic: could not find user information for $uid");
      	return false;
      }
      $name = $response[0]['first_name'] . ' ' . $response[0]['last_name'];
      $url = $response[0]['pic'];
      
      // Make image tag. 
      
      $imgTag = "<img src=\"$url\" alt=\"$name\" width=\"". self::$sizes[$size]['w'] ."\"/>";
      
      // Make anchorTag
      if ( $linked ) {
         $profileLink = RingsideSocialConfig::$webRoot . "/profile.php";
         echo "<a href='$profileLink?id=$uid'>$imgTag</a>";
      } else {
         echo $imgTag;
      }
      
      return false;
   }
   
   function isEmpty()
   {
   		return true;
   }
   
	function getType()
   	{
   		return 'inline';   	
   	}

}
?>
