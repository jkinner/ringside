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

/**
 * We are either processing the login page or we are showing it.
 * There are two types of login pages in the end, either one to
 * process normal page or one which is for a remote application
 * requesting access.
 *
 * TODO process pages from remote requests slightly differently.
 * TODO support auth_key passed in
 */
// FIXME: Remove DAOs from file
require_once( "ringside/api/dao/User.php" );
require_once( 'ringside/api/clients/RingsideApiClients.php');
require_once( 'ringside/api/db/RingsideApiDbDatabase.php');
require_once( 'ringside/apps/model/user.php');

// Registration is an app, and though not logged in has access to system.
$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$flavor = $client->getFlavor();

if( !isset($_POST['email']) || !isset($_POST['reg_passwd__']) ) {
   
   loadErrorClass( $flavor );
   return;

} else {

   $login = isset ( $_POST['login'] ) ? true : false ;
   $name = $_POST['name'];
   $email = $_POST['email'];
   $password = $_POST['reg_passwd__'];
   $nextPage = isset( $_POST['next'] ) ? $_POST['next'] : "index.php" ;
   $auth_token = isset ( $_POST['auth_token'] ) ? $_POST['auth_token'] : null ;

   if ( !validateEmail($email) ) {
      loadErrorClass($flavor, 'Email validation failed.');
      return;
   }

   if ( !validateName($name) ) {
      loadErrorClass($flavor, 'Please specify a first and last name');
      return;
   }

   if ( !validatePassword( $password )) {
      loadErrorClass($flavor, 'Password must be specified' );
      return;
   }

   $uid = null;
   try{
       
      $dbCon = RingsideApiDbDatabase::getDatabaseConnection();
      //$client->api_client->admin_createUser($email, $password);
      $user = new Api_Dao_User();
      $user->setUsername($email);
      $user->setPassword(sha1($password));

      if(!$user->initByUserName($email, $dbCon)) {
         try {
            $user->insertIntoDb($dbCon);
            saveName( $user->getId(), $name );
            $uid = $user->getId();
            error_log ( "REGISTERED NEW ONE " . $user->getId() );
         } catch(Exception $e) {
            $error = 'Failed to create user: '.$e->getMessage();
         }
      } else {
         $error = 'User already exists!';
      }
   } catch(Exception $e) {
      $error = 'Registration Error: '.$e->getMessage();
   }

   if( !isset($error) || empty($error) || strlen($error) == 0) {

      if ( $login === true ) { 
         echo "<rs:authorize uid='$uid' />";
      }
      
      $nextRequest = array();

      foreach ( $_REQUEST as $key=>$value ) {
         if ( $key!='PHPSESSID' && $key!='next' && $key!= 'doquicklogin' && $key!= 'persistent') {
            if ( strpos($key, 'fb_sig') != 0 ) {
               $nextRequest[$key] = $value;
               error_log("Adding $key=$value to nextRequest!");
            }
         }
      }

      $nextReqKeys = '';
      $nextRequest['newUser']=$email;
      $nextRequest['newUid']=$uid;
      if ( count( $nextRequest) > 0 ) {
         $nextReqKeys = '?' . http_build_query($nextRequest);
      }
      if ( empty( $nextPage) ) {
         $nextPage = 'index.php';
      }
      echo "<fb:redirect url='$nextPage$nextReqKeys'/>";
   } else {
      loadErrorClass($flavor, $error);
   }


   // TODO This needs to send an email and allow users to verify registration
   // with a token.  Then they can use their credentials to login, we don't want to autologin
   // after a registration approval.   
}

/**
 * Savest the user information.
 *
 * @param unknown_type $uid
 * @param unknown_type $first
 * @param unknown_type $last
 * @return unknown
 */
function saveName( $uid, $name ) {

   $first = '';
   $last = '';
   $fullname = explode( " ", $name, 2 );
   if ( count( $fullname ) == 1 ) {
      $first = $name;
   } else if ( count ($fullname == 2 ) ) {
      $first = $fullname[0];
      $last = $fullname[1];
   }
    
   $userData = array();
   $userData['user_id']=$uid;
   $userData['first_name']=$first;
   $userData['last_name']=$last;

   $user = new User();
   $userModel = $user->find( $uid );

   if( !is_object($userModel->userbasicprofile)){
      $userModel->userbasicprofile=new Userbasicprofile($userData);
   }

   $saveResult = $userModel->userbasicprofile->save($userData,true);
   return $saveResult;
}

/**
 * TODO: Belongs in an action class, but a util class if we don't have them
 * Just loads the correct page on an error.  This code was duplicated 3 times in this one file.
 *
 * @param string $flavor
 */
function loadErrorClass($flavor, $msg = null) {
   if ( $msg != null ) {
      $error = $msg;
   }
   
   $result = include ('apps/register/'.$flavor .'.php');
   if ( $result == null ) {
      error_log( 'apps/register/'.$flavor .'.php' . " not loaded " );
      $result = include ( 'apps/login/canvas.php'  );
      if ( $result == null ) {
         error_log( 'apps/login/canvas.php' . " not loaded " );
      }
   }
}

function validateEmail( $email ) {
   return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

function validatePassword ( $password ) {
   return !empty( $password );
}

function validateName( $name ) {
   
   if ( empty( $name ) ) { 
      return false; 
   }
   
   $full = explode( " ", trim($name), 2 );
   if ( $full === false || !is_array($full) || count($full) != 2 ) {
      return false;
   }
   
   if ( empty($full[0]) || empty( $full[1]) ) { 
      return false;
   }
   
   return true;
}

?>
