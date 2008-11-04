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

include_once 'ringside/api/clients/RingsideApiClients.php' ;
include_once 'ringside/api/clients/facebookapi_php5_restlib.php' ;
include_once 'ringside/api/clients/RingsideApiClientsConfig.php' ;

/**
 * The OpenFBRestClient extends the facebook client to enable
 * - Specific openfb calls or extensions and provide a hooking mechanism
 * for other api's/client libraries which would like to link in but have
 * different end points.
 *
 * To use OpenFBRestClient directly.
 * new OpenFBRestClient ( APIKEY, SECRET, default-server, session key if you have one. )
 *
 * If you are a client library extension there are a few options
 * 1. Be your own client library and use and OpenFBRestClient to make calls through call_method.
 * 2. TODO Plugin into our proxy mechanism and register yourself with OpenFBRestClient
 * 3. Extend OpenFBRestClient (but note folks could hold onto multiple clients that way.
 *
 * @author Richard Friedman
 */
class RingsideApiClientsRest extends FacebookRestClient {

	private $canvasUrlspace = null;
	private $servers = array();
	public $auth_token = null;

	/**
	 * Create the client.
	 *
	 * @param string $session_key if you haven't gotten a session key yet, leave
	 *                            this as null and then set it later by just
	 *                            directly accessing the $session_key member
	 *                            variable.
	 */
	public function __construct($api_key, $secret, $session_key = null, $url = null) {

		$this->secret = $secret;
		$this->session_key  = $session_key;
		$this->api_key      = $api_key;
		$this->last_call_id = 0;
		$this->printResponse = false;

		if ( $url == null ) {
			$url = RingsideApiClientsConfig::$serverUrl;
		}
		$this->setDefaultServer( $url );

	}

	/**
	 * Add a server for a specific namespace of APIs.
	 *
	 * @param string $package
	 * @param string $url
	 */
	public function addServer( $canvasUrlspace, $url ) {
		$this->servers[$canvasUrlspace] = $url;
	}

	/**
	 * Set the underlying server to a chosen namespace.
	 * If the namespace does not exist the the default namesepace is chosen.
	 *
	 * @param unknown_type $canvasUrlspace
	 */
	public function setNamespace( $canvasUrlspace ) {
		if ( $canvasUrlspace != $this->canvasUrlspace  ) {
			$server = isset( $this->servers[$canvasUrlspace]) ?   $this->servers[$canvasUrlspace] : $this->servers['default'];
			$this->server_addr = $server ;
		}
	}

	/**
	 * Set the default server to route requests to.
	 * We control the default namespaces and how they route.
	 *
	 * @param unknown_type $url
	 */
	public function setDefaultServer( $url ) {

		$this->addServer( 'facebook', $url  );
		$this->addServer( 'openfb', $url  );
		$this->addServer( 'default', $url  );

		$this->setNamespace( 'facebook' );
	}

	/**
	 * For every invocation we need to figure where the call
	 * should go.  For example a rating service should be redirected to the
	 * rating service.
	 *
	 * @param unknown_type $method
	 * @param unknown_type $params
	 * @return unknown
	 */
	public function call_method($method, $params) {
		$stack = explode( ".", $method );
		$this->setNamespace( $stack[0] );

		return parent::call_method( $method, $params );
	}

	/**
	 * For each registered client we can proxy the call back to the client.
	 *
	 * @param string $method
	 * @param array $args
	 */
	public function __call( $method, $args ) {
		// TODO support client libraries which get wired in behin the scenes.
	}

	/**
	 * Create an authentication token for a user.
	 * @return assoc array containing auth_token
	 */
	public function auth_createToken() {
		$result = $this->call_method('facebook.auth.createToken', array("api_key" => $this->api_key));
		$this->auth_token = trim($result);
		return $result;
	}

	/**
	 * Force an approval of the token, something ONLY possible via the communication
	 * between openFB and Ringside.
	 *
	 * @return assoc array containing result
	 */
	public function auth_approveToken($uid) {
		// TODO add back in 'facebook.' to all method calls once ...
		$result = $this->call_method('facebook.auth.approveToken', array('auth_token'=>$this->auth_token, 'uid'=>$uid));
		return $result;
	}

	/**
	 * OpenFB Extension allows friends_get to pass in a UID, where you are getting the friends of a users.
	 *
	 * @param int $uid
	 * @return Friends of a given user, current logged in user by default.
	 */
	public function friends_get( $uid = null ) {

		if ( $uid==null && isset($this->friends_list)) {
			return $this->friends_list;
		}
		if ( $uid == null )
		return $this->call_method('facebook.friends.get', array());
		else
		return $this->call_method('facebook.friends.get', array('uid'=>$uid));
	}


	/**
	 * Returns whether or not the user corresponding to the current session object has the app installed.
	 * There is an openfb extensioon here as in normal FB you can not get the current user.
	 *
	 * @param string $uid which user do you want to know app information about.
	 * @param string $aid which application are you trying to figure out.
	 * @return Is the application added for the given (or current) users.
	 */
	public function users_isAppAdded( $uid = null, $aid = null ) {
		if ( $aid == null && isset($this->added)) {
			return $this->added;
		}

		$params = array();
		if  ( !empty ( $uid ) ) {
			$params['uid']=$uid;
		}
		if ( !empty( $aid ) ){
			$params['aid'] = $aid;
		}

		return $this->call_method('facebook.users.isAppAdded', $params );
	}

	/**
	 * OpenFBExtension which tells if the application is disabled, installed, never seen before.
	 *
	 * @param string $uid which user do you want to know app information about.
	 * @param string $aid which application are you trying to figure out.
	 * @return Is the application added for the given (or current) users.
	 */
	public function users_isAppEnabled( $uid = null, $aid ) {
		$params = array();
		if  ( !empty ( $uid ) ) {
			$params['uid']=$uid;
		}
		if ( !empty( $aid ) ){
			$params['aid'] = $aid;
		}
		return $this->call_method('facebook.users.isAppEnabled', $params );
	}

	/**
	 * Return the properites of either the connected application or a specific application.
	 *
	 * TODO have not validate this against the official Facebook API.
	 * Their json_encoded format within the xml seems just problematic. would need to chunk a cdata section.
	 *
	 * @param comma-separated-string, or array of strings $properties
	 * @param string $aid
	 * @return properties of said application
	 */
	public function admin_getAppProperties( $properties, $aid = null, $canvasUrl = null, $apiKey = null) {

		$params = array ();
		$parr = is_array($properties) ? $properties : explode( "," , $properties );
		$params['properties'] = json_encode($parr);
		if ( $aid != null ) {
			$params['aid'] = $aid;
		}
		if ( $canvasUrl != null ) {
			$params['canvas_url'] = $canvasUrl;
		}
		if ( $apiKey != null ) {
			$params["app_api_key"] = $apiKey;
		}
			
		$response = $this->call_method('facebook.admin.getAppProperties', $params );
		return json_decode( $response , true );
	}

	/*
	 * $properties is an associative array, key is the property name, value
	 * is the property value. $apiKey must be specified.
	 */
	public function admin_setAppProperties($properties, $apiKey = null)
	{
		$params = array();
		$params['properties'] = json_encode($properties);

		if ( $apiKey != null ) {
			$params["app_api_key"] = $apiKey;
		}
			
		$response = $this->call_method('facebook.admin.setAppProperties', $params);
		return json_decode($response, true);
	}

	/**
	 * 
	 *
	 * @param int $application_id
	 * @param string $application_canvas_name
	 * @param string $application_api_key
	 * @return unknown
	 */
	public function application_getPublicInfo( $application_id = null, $application_canvas_name = null, $application_api_key = null) 
	{
		$params = array ();
		if ( $application_id != null ) {
			$params['aid'] = $application_id;
		}
		if ( $application_canvas_name != null ) {
			$params['canvas_url'] = $application_canvas_name;
		}
		if ( $application_api_key != null ) {
			$params["app_api_key"] = $application_api_key;
		}
			
		$response = $this->call_method('facebook.application.getPublicInfo', $params );
		return json_decode( $response , true );
	}


	/**
	 * Return the full list of Applications registered on this server.
	 *
	 * @return List of Applications and their properties
	 */
	public function admin_getAppList()
	{
		$params = array ();
		$response = $this->call_method('ringside.admin.getAppList', $params );
		return $response;
	}

	/**
	 * Creates an app with the given name
	 *
	 * @param string $name
	 * @return unknown
	 */
	public function admin_createApp($name)
	{
		$params = array('name' => $name);
		$response = $this->call_method('ringside.admin.createApp', $params);
		return $response;
	}
	
	public function admin_deleteApp($apiKey)
	{
		$params = array('app_api_key' => $apiKey);
		$response = $this->call_method('ringside.admin.deleteApp', $params);
		return $response;
	}	

	/**
	 * Creates the user if it doesn't exist. Will get an exception if the user already exists.
	 *
	 * @param unknown_type $user_name
	 * @param unknown_type $password
	 * @return unknown
	 */
	public function admin_createUser($user_name, $password)
	{
		$params = array('user_name' => $user_name, 'password' => $password);
		$response = $this->call_method('ringside.admin.createUser', $params);

		error_log('Created user with id='.$response['user']['id']);
		return $response['user']['id'];
	}
	
	/**
	 * Return the full list of Applications this user has added to their profile.
	 *
	 * @return List of Applications and their properties
	 */
	public function users_getAppList()
	{
		$params = array ();
			
		$response = $this->call_method('ringside.users.getAppList', $params );
		return $response;
	}

	/**
	 * Sets properties for an application added to a user profile
	 *
	 * @param unknown_type $params
	 * @param unknown_type $app_id
	 * @return unknown
	 */
	public function users_setApp($params, $app_id)
	{
		if(!isset($app_id))
		{
			throw new Exception("app id is required!");
		}

		if(isset($params) && is_array($params))
		{
			if(!isset($params['app_id']))
			{
				array_push($params, $app_id);
			}
		}else
		{
			$params = array("app_id" => $app_id);
		}

		$response = $this->call_method('ringside.users.setApp', $params );

		return $response;
	}

	/**
	 * Removes an app from the users profile
	 *
	 * @param unknown_type $app_id
	 * @return unknown
	 */
	public function users_removeApp($app_id)
	{
		if(!isset($app_id))
		{
			throw new Exception("app id is required!");
		}
		$params = array("app_id" => $app_id);

		$response = $this->call_method('ringside.users.removeApp', $params );

		return $response;
	}

	/**
	 * Gets an app and it's properties from a users profile
	 *
	 * @param unknown_type $app_id
	 * @return unknown
	 */
	public function users_getApp($app_id)
	{
		if(!isset($app_id))
		{
			throw new Exception("app id is required!");
		}
		$params = array("app_id" => $app_id);

		$response = $this->call_method('ringside.users.getApp', $params );
		return $response;
	}
	
   /**
    * Get a comments for a specific thread. 
    *
    * @param string $xid
    * @param integer $first
    * @param integer $count
    * @param integer $aid
    * @return array of comments where each comment includes uid, cid, text, created
    */
   public function comments_get( $xid, $first = null, $count = null, $aid = null ) {

      $params = array();
      $params['xid'] = $xid;
      if ( $first != null ) { 
         $params['first'] = $first;
         if ( $count != null ) { 
            $params['count'] = $count;
         }
      } else if ( $count != null ) { 
         $params['first'] = 0;
         $params['count'] = $count;
      }
      if ( $aid != null ) {
         $params['aid'] = $aid;
      }
      
      return $this->call_method('ringside.comments.get', $params); 
   }
   
   /**
    * Add a comment to a Thread id. 
    *
    * @param string $xid
    * @param string $msg
    * @param integer $aid
    * @return 1/0 if the comment was added. 
    */
   public function comments_add( $xid, $msg, $aid = null ) {

      $params = array();
      $params['xid'] = $xid;
      $params['text'] = $msg;
      if ( $aid != null ) {
         $params['aid'] = $aid;
      }
      
      return $this->call_method('ringside.comments.add', $params); 
   }

   /**
    * Get the number of comments for a given thread. 
    *
    * @param string $xid
    * @param integer $aid
    * @return # of messages
    */
   public function comments_count( $xid, $aid = null ) {

      $params = array();
      $params['xid'] = $xid;
      if ( $aid != null ) {
         $params['aid'] = $aid;
      }
      
      return $this->call_method( 'ringside.comments.count', $params); 
   }

   /**
    * Delete a comment from a thread.
    *
    * @param string $xid
    * @param integer $cid
    * @param integer $aid
    * @return 1/0 success or failure
    */
   public function comments_delete( $xid, $cid, $aid = null ) {

      $params = array();
      $params['xid'] = $xid;
      $params['cid'] = $cid;
      if ( $aid != null ) {
         $params['aid'] = $aid;
      }
      
      return $this->call_method( 'ringside.comments.delete', $params); 
   }
   
   /**
    * This method searches for all the friends of this user, first and last name which matches
    * the query.
    * 
    * First name starts with.
    * Last name equals.
    * 
    * Works like the facebook friend search.
    *
    * @param string $query
    * @return Array
    */
   public function friends_search($query)
   {
   		$params = array();
   		$params['query'] = $query;
   		return $this->call_method('ringside.friends.search', $params);
   }
   
}

?>
