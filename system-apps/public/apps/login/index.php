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
define('BAD_PASSWORD', 1);
define('NO_USER', 2);
define( "LOGIN_ERROR_NOUSER", 'No such user.');
define( "LOGIN_ERROR_BADPASS", '<b>Incorrect email/password combination.</b><br />Ringside passwords are case sensitive. Please check your CAPS lock key.');

// FIXME: DAO does not belong here
require_once( 'ringside/api/clients/RingsideApiClients.php');
require_once( 'ringside/api/db/RingsideApiDbDatabase.php');
require_once( 'ringside/api/dao/User.php');

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$flavor = $client->getFlavor();

if( !isset($_POST['email']) || !isset($_POST['p']) ) {
   
	loadForm($flavor, '', $_REQUEST);

} else {

   /** the user name password settings **/
    $username = $_REQUEST['email'];
	$password = $_REQUEST['p'];

	$uid = authenticate( $username, $password, $flavor );
	if ( $uid === false ) { 
	   return;
	}
	
	/** settings that indicate this request is from web app or desktop **/
	$apikey = isset( $_REQUEST['api_key'] ) ? $_REQUEST['api_key'] : null;
	$authtoken = isset ( $_REQUEST['auth_token'] ) ? $_REQUEST['auth_token'] : null ;
	
	/** Settings which indicate if sessions created should be infinite **/
	$infinite = isset ( $_REQUEST['infinite'] ) ? $_REQUEST['infinite'] : 'false' ;
	
	if ( !empty($apikey) && !empty($authtoken)) { 
	   // desktop login
	   echo "<rs:authorize uid='$uid' apikey='$apikey' authtoken='$authtoken' infinite='$infinite' />";
	   echo "You have been authenticated succesfully. Close the browser and go back to your desktop application";
	   
	} else if ( !empty( $apikey )) { 
	   // app login
	   $next = isset( $_REQUEST['next'] ) ? $_REQUEST['next'] : null;
	   $version = isset ( $_REQUEST['v'] ) ? $_REQUEST['v'] : "1.0" ;
	   $canvas = isset ( $_REQUEST['canvas'] ) ? 'true' : 'false' ;
	   $trust = isset( $_REQUEST['trust'] ) ? 'true' : 'false';
	
	   echo "<rs:authorize uid='$uid' apikey='$apikey' infinite='$infinite' trust='$trust' canvas='$canvas' next='$next'/>";
	   
	} else {	   
	   // web login
	   $nextPage = isset( $_REQUEST['next'] ) ? $_REQUEST['next'] : "index.php";
	   $nextUrl = getNextUrl( $nextPage );
	   
	   echo "<rs:authorize uid='$uid' infinite='$infinite' />";
	   echo "<fb:redirect url='$nextUrl'/>";
	}

}
 
/**
 * Authenticate a user.
 *
 * @param string $username
 * @param string $password
 * @return true if there were no errors and user was authenticated, error string if there was an error. 
 */
function authenticate( $username, $password, $flavor ) {
   
	// Authenticate user. 
	try {
		
	    // TODO move to use PHP Auth?
	    $dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$userDb = new Api_Dao_User();
		$uid = $userDb->login($username, $password, $dbCon);
		return $uid;
		
	} catch(Exception $e) {

	   $error = '';
	   $code = $e->getCode();
	   if($code == NO_USER) {
	      $error = "No User with User Name $username exists!<BR><a href=\"register.php\">Sign Up!</a>";
	   } else if($code == BAD_PASSWORD) {
	      $error = 'Invalid Password';
	   } else {
	      $error = $e->getMessage();
	   }

	   loadForm($flavor, $error, $_REQUEST);
	}
	
    return false;
}

/**
 * For a WEB request or a failed login we need to build
 * the next parameters to pass in. 
 * 
 * @return array of parameters to pass on. 
 */
function getNextUrl( $nextPage ) {

   // TODO move this pretty much to a utility function for keeping parameters.
   $nextRequest = array();
   foreach ( $_REQUEST as $key=>$value ) {
      if ( $key!='PHPSESSID' && $key!='next' && $key != 'email' && $key != 'p' && $key!= 'doquicklogin' && $key!= 'persistent') {
         if ( strpos($key, 'fb_sig') != 0 ) {
            $nextRequest[$key] = $value;
         }
      }
   }

   $nextReqKeys = '';
   if ( count( $nextRequest) > 0 ) {
      error_log("Next request is $nextRequest");
      if ( strpos($nextPage, '?') === false ) {
         $nextReqKeys = '?';
      } else {
         $nextReqKeys = '&';
      }
      $nextReqKeys .= http_build_query($nextRequest);
   }

   return $nextPage . $nextReqKeys;
}

/**
 * TODO: Belongs in an action class, but a util class if we don't have them
 * Just loads the correct page on an error.  This code was duplicated 3 times in this one file.
 *
 * @param string $flavor
 */
function loadForm($flavor, $msg, $request)
{
	$error = $msg;
	$result = include ('apps/login/'.$flavor .'.php');
	if ( $result == null ) {
		error_log( 'apps/login/'.$flavor .'.php' . " not loaded " );
		$result = include ( 'apps/login/canvas.php'  );
		if ( $result == null ) {
			error_log( 'apps/login/canvas.php' . " not loaded " );
		}
	}
}

?>
