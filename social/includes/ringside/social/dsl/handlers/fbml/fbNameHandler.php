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

class fbNameHandler {

   function doStartTag( $application, $parentHandler, $args ) {
      
      if ( !isset( $args['uid'] ) || empty($args['uid']) ) {
         echo 'RUNTIME ERROR: fb:name: Required attribute "uid" not found in node fb:name';
         return false;
      }

      $uid = $args['uid'];
       
      // For using {{*}} FB-specific functions inside the uid field
      if ( $uid == 'loggedinuser' ) {
         $uid = $application->getCurrentUser();
      } else if ( $uid == 'profileowner') {
         $uid = $application->getProfileOwner();
      }
      
      $subjectId = null;
      if ( isset( $args['subjectid'] )) { 
         $subjectId = $args['subjectid'];
      }
            
      $firstnameonly = HandlerUtil::checkBoolArg('firstnameonly', $args);
      $lastnameonly = HandlerUtil::checkBoolArg('lastnameonly', $args ); 
      $columns = $this->fbUserColumns( $firstnameonly, $lastnameonly, $subjectId );
      
      $capitalize = HandlerUtil::checkBoolArg('capitalize', $args);
      $possessive = HandlerUtil::checkBoolArg('possessive', $args);
      $reflexive = HandlerUtil::checkBoolArg('reflexive', $args);
      $useyou = HandlerUtil::checkBoolArg('useyou', $args, true);
      
      if ( array_key_exists('subjectid', $args) ) {
      	$subjectid = $args['subjectid'];
      	if ( $uid == $application->getCurrentUser() && $subjectid == $uid) {
      		$reflexive = true;
      	}
      }
      
      if ( $uid == $application->getCurrentUser() && $useyou ) {
         echo ($capitalize?'Y':'y').'ou'.(($possessive||$reflexive)?'r':'').($reflexive?($possessive?' own':'self'):'');
      } else {
         $client = $application->getClient();
         $response = $client->users_getInfo( array($uid), $columns);
//      		if ( $uid == $application->getCurrentUser() ) {
//	      		if ( isset($response[0]['sex']) ) {
//	      			echo ($capitalize?'H':'h');
//	      			if ( $response[0]['sex'] == 'M') {
//	      				echo ($reflexive?($possessive?'is own':'imself'):'is');
//	      			} else if ( $response[0]['sex'] == 'F') {
//	      				echo ($reflexive?($possessive?'er own':'erself'):'ers');
//	      			}
//	      		} else {
//	      			echo ($capitalize?'I':'t').($possessive?'s':'').($reflexive?($possessive?' own':'self'):'');
//	      		}
//      		}
//	      } else {
           $name = $response[0];
	         if ( $firstnameonly ) {
	            echo $name['first_name'];
	         } else if ( $lastnameonly ) {
	            echo $name['last_name'];
	         } else {
	            echo $name['first_name']  . ' ' . $name['last_name'];
	         }
	         if ( $possessive ) {
	         	echo '\'s';
	         }
	      }
//      }
      
      return false;
   }

   function fbUserColumns($firstnameonly, $lastnameonly, $subjectid) {
      $columns = array();
      if ( $firstnameonly ) {
         $columns = array('first_name');
      } else if ( $lastnameonly ) {
         $columns = array('last_name');
      } else {
         $columns = array('first_name', 'last_name');
      }
       
      if ( $subjectid ) {
      	$columns[] = 'sex';
      }
      
      return $columns;
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
