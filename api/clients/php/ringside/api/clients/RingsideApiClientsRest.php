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
	private $session_keys = array();
	private $network_key = null;
	public $auth_token = null;

	/**
	 * Create the client.
	 *
	 * @param string $session_key if you haven't gotten a session key yet, leave
	 *                            this as null and then set it later by just
	 *                            directly accessing the $session_key member
	 *                            variable.
	 */
	public function __construct($api_key, $secret, $session_key = null, $url = null, $network_key = null)
	{
		$this->secret = $secret;
		$this->session_key  = $session_key;
		$this->api_key      = $api_key;
		$this->network_key  = $network_key;
		$this->last_call_id = 0;
		$this->printResponse = false;

		if ( $url == null ) {
			$url = RingsideApiClientsConfig::$serverUrl;
		}
		$this->setDefaultServer( $url, $session_key );

	}

	/**
	 * Add a server for a specific namespace of APIs.
	 *
	 * @param string $package
	 * @param string $url
	 */
	public function addServer( $canvasUrlspace, $url, $alt_session_key = null ) {
		$this->servers[$canvasUrlspace] = $url;
		if ( isset($alt_session_key) ) {
			$this->session_keys[$canvasUrlspace] = $alt_session_key;
		}
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
			if ( isset($this->session_keys[$canvasUrlspace]) ) {
				$this->session_key = $this->session_keys[$canvasUrlspace];
			} else if ( isset($this->session_keys['default']) ) {
				$this->session_key = $this->session_keys['default'];
			}
		}
	}

	public function setNetworkKey( $networkKey ) {
		$this->network_key = $networkKey;
	}

	/**
	 * Set the default server to route requests to.
	 * We control the default namespaces and how they route.
	 *
	 * @param unknown_type $url
	 */
	public function setDefaultServer( $url, $session_key ) {
		$this->addServer( 'facebook', $url, $session_key  );
		$this->addServer( 'openfb', $url, $session_key  );
		$this->addServer( 'default', $url, $session_key  );

		$this->setNamespace( 'facebook' );
	}

	public function auth_getSession($auth_token) {
		$result = parent::auth_getSession($auth_token);
		// This will be used as the standard session key
		$this->setDefaultServer($this->servers['default'], $result['session_key']);
		return $result;
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
		$result = parent::call_method( $method, $params );
		return $result;
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
	 * @param boolean is this session to be created an infinite one
	 * @param api_key override default API KEY
	 * @return assoc array containing auth_token
	 */
	public function auth_createToken($infinite = false, $api_key = null ) {
		// TODO: This won't break calling Facebook, will it?
		if ( $api_key == null ) { 
		   $api_key = $this->api_key; 
		}
		// error_log( 'RingsideApiClientsRest, network_key: ' . $this->network_key );
		$result = $this->call_method('facebook.auth.createToken', array("api_key" => $api_key, "network_key" => $this->network_key, "infinite" => ($infinite===true?'true':'false')));
		$this->auth_token = trim($result);
		return $this->auth_token;
	}
	
	public function auth_createSiteConnectSession($uid) {
	    $result = $this->call_method('ringside.auth.createSiteConnectSession', array("user_network_key" => $this->network_key, 'uid' => $uid));
	    return $result;
	}
	/**
	 * Get a users profile FBML. 
	 *
	 * @param string $uid
	 * @param string $aid
	 * @return the users FBML
	 */
	public function profile_getFBML( $uid = null, $aid = null) { 
         return $this->call_method('facebook.profile.getFBML', array('uid' => $uid, 'aid'=>$aid ));	   
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

	public function feed_get( $uid = null,Ê$actorid = null, $friends = false, $actions = true, $stories = true ) {
        if( !isset( $uid ) && !isset( $actorid ) ) {
            throw new Exception( 'Etiher uid or actorid must be set' );
        }
        
        if( isset( $uid ) && isset( $actorid ) ) {
            throw new Exception( 'Etiher uid or actorid must be set, but not both' );
        }
        
        if( isset( $actions ) && isset( $stories ) &&
            $actions == false && $stories == false ) {
            throw new Exception( 'Etiher actions or or stories may be set to false, but not both' );
        }
        if( !empty( $actorid ) && $friends == true ) {
            throw new Exception( 'Cannot specify actorid and friends.  Actors cannot have friends currently' );
        }
        $params = array();
        $params[ 'uid' ] = $uid;
        $params[ 'actorid' ] = $actorid;
        $params[ 'friends' ] = $friends;
        $params[ 'actions' ] = $actions;
        $params[ 'stories' ] = $stories;
        
        return $this->call_method( 'ringside.feed.get', $params );
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
	 * Enter description here...
	 *
	 * @param unknown_type $fuid
	 * @return unknown
	 */
	public function friends_invite($fuid)
	{
		if(isset($fuid) && !empty($fuid))
		{
			return $this->call_method('ringside.friends.invite', array('fuid' => $fuid));
		}else
		{
			return false;
		}
	}

	/**
	 * Invite friends via email address only.
	 *
	 * @param mixed $email one or more email addresses to invite.
	 * @return unknown
	 */
	public function friends_inviteEmail($email, $rsvp)
	{
	    return $this->call_method('ringside.friends.inviteEmail', array('email' => $email, 'rsvp' => $rsvp));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $fuid
	 * @param unknown_type $access
	 */
	public function friends_accept($fuid, $status, $access)
	{
		if(isset($fuid) && !empty($fuid) && $access != null && $status != null)
		{
			return $this->call_method('ringside.friends.accept', array('fuid' => $fuid, 'status' => $status, 'access' => $access));
		}else
		{
			return false;
		}
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function friends_getInvites()
	{
		return $this->call_method('ringside.friends.getInvites', array());
	}
	
	public function friends_getEmailInvite($inv)
	{
	    return $this->call_method('ringside.friends.getEmailInvite', array('inv' => $inv));
	}

	public function admin_getAppKeys($appId = null, $canvasUrl = null, $apiKey = null, $nid = null)
	{
		return $this->call_method('ringside.admin.getAppKeys', array('app_api_key' => $apiKey, 'canvas_url' => $canvasUrl, 'aid' => $appId, 'nid' => $nid));
	}
	
	public function admin_setAppKeys($keys, $app_id = null)
	{
		return $this->call_method('ringside.admin.setAppKeys', array('app_id' => $app_id,
																						 'keys' => json_encode($keys)));
	}
	
	public function admin_getUserMaps($appId = null, $nid = null, $uid = null, $pid = null)
	{
		return $this->call_method('ringside.admin.getUserMaps', array('aid' => $appId, 'nid' => $nid, 'uid' => $uid, 'pid' => $pid));
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
	public function admin_getAppProperties( $properties, $aid = null, $canvasUrl = null, $apiKey = null, $nid = null) {

		$params = array ();
		$parr = is_array($properties) ? $properties : explode( "," , $properties );
		$params['properties'] = json_encode($parr);
		$params['nid'] = $nid;
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
	public function admin_getDomainProperties( $properties, $nid = null, $apiKey = null) {
		$params = array ();
		$parr = is_array($properties) ? $properties : explode( "," , $properties );
		$params['properties'] = json_encode($parr);
		$params['nid'] = $nid;
      $params['domain_api_key'] = $apiKey; 
		$response = $this->call_method('ringside.admin.getDomainProperties', $params );
		return json_decode( $response , true );
	}
	
	/*
	 * $properties is an associative array, key is the property name, value
	 * is the property value. $apiKey must be specified.
	 */
	public function admin_setAppProperties($properties, $aid = null, $canvasUrl = null, $apiKey = null)
	{
		$response = $this->call_method('facebook.admin.setAppProperties', array('app_api_key' => $apiKey, 'canvas_url' => $canvasUrl, 'aid' => $aid, 'properties' => json_encode($properties)));
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
	
	/**
	 * Create a session for an application, this is only accesible for ADMIN api keys. 
	 * 
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $network_key
	 * @param unknown_type $infinite
	 * @return unknown
	 */
	public function auth_createAppSession( $uid, $network_key , $infinite ) {
		$params = array( 'uid' => $uid, 'network_key'=>$network_key, 'infinite'=>$infinite );
		$response = $this->call_method('ringside.auth.createAppSession', $params);
		$this->session_key = $response['session_key'];
		return $response;
		
	}

	public function admin_deleteApp($appId = null, $canvasUrl = null, $apiKey = null)
	{
		$response = $this->call_method('ringside.admin.deleteApp', array('app_api_key' => $apiKey, 'canvas_url' => $canvasUrl, 'aid' => $appId));
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

//		error_log('Created user with id='.$response['user']['id']);
		return $response['user']['id'];
	}

	/**
	 * Creates or returns the principal ID for the current user.
	 *
	 * @return string the principal ID for the current user.
	 */
	public function admin_createPrincipal($sid, $snid, $aid = null) {
		return $this->call_method('ringside.admin.createPrincipal', array('snid' => $snid, 'sid' => $sid, 'aid' => $aid));
	}

	public function admin_getTrustInfo($tids = null ) {
		return $this->call_method('ringside.admin.getTrustInfo', array('tids' => $tids));
	}

	public function admin_mapUser($sid, $snid, $uid, $nid = null, $aid = null) {
		return $this->call_method('ringside.admin.mapUser', array('sid' => $sid, 'snid' => $snid, 'uid' => $uid, 'nid' => $nid, 'aid' => $aid));
	}
	
	public function admin_getServerInfo() {
		return $this->call_method('ringside.admin.getServerInfo', array());
	}

	public function admin_createNetwork($name, $authUrl, $loginUrl, $canvasUrl, $webUrl)
	{
		return $this->call_method('ringside.admin.createNetwork',
										  array('name' => $name, 'auth_url' => $authUrl,
										  		  'login_url' => $loginUrl, 'canvas_url' => $canvasUrl,
										  		  'web_url' => $webUrl));
	}
	
	public function admin_deleteNetwork($nid)
	{
		return $this->call_method('ringside.admin.deleteNetwork', array('nid' => $nid));
	}
	
	public function admin_getNetworkProperties($nids = array(), $props = array())
	{
		return $this->call_method('ringside.admin.getNetworkProperties',
										  array('nids' => implode(',', $nids), 'properties' => implode(',', $props)));
	}
	
	public function admin_setNetworkProperties($nid, $props = array())
	{
		return $this->call_method('ringside.admin.setNetworkProperties',
										  array('nid' => $nid,
										  		  'properties' => json_encode($props)));
	}
	
	public function admin_setPaymentGateway( $type, $subject, $password )
	{
		$params = array( 'type' => $type, 'subject'=>$subject, 'password'=>$password );
		return $this->call_method( 'ringside.admin.setPaymentGateway', $params );
	}
    
    public function admin_getPaymentGateway()
    {
        return $this->call_method( 'ringside.admin.getPaymentGateway', array() );
    }
	
	/**
	 * Return session information.
	 *
	 * @return Session information for a given users/application.
	 */
	public function users_getAppSession()
	{
		$params = array ();

		$response = $this->call_method('ringside.users.getAppSession', $params );
		return $response;
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
	 * Determines whether a user has paid for a subscription to an application and/or an application's payment plan.
	 *
	 * @param integer $uid The user id of the user in question
	 * @param string $nid The network id of the user in question
	 * @param integer $app_id The application id of the application in question
	 * @param string $plans A comma separated list of plan names for the given application
	 * @return True if the user has paid.  False if they have not paid.
	 */
	public function social_pay_user_hasPaid( $uid, $nid = null, $app_id, $plans = null )
	{
		if( !isset( $uid ) )
		{
			throw new Exception("uid is required!");
		}
        if( !isset( $app_id ) )
        {
            throw new Exception("app id is required!");
        }
		$params = array( 'aid' => $app_id, 'uid' => $uid, 'nid'=>$nid, 'plans'=>$plans );
		
//        error_log( 'RingsideApiClientsRest, sending params: ' . var_export( $params, true ) );
        $response = $this->call_method( 'ringside.socialPay.userHasPaid', $params );
		return $response;
	}

	
	/**
	 * Translates one or more uids from a network ID to a common principal ID. If the network is not speficied,
	 * the uids will be translated from the network calling the application to the principal ID.
	 * If the network is specified, the uids are translated from the principal ID to the specified network uid.
	 *
	 * @param array $uids optional - list of uids to be translated; defaults to the logged-in user
	 * @param string $network optional - the network to translate to; defaults to this application's home network
	 */
	public function users_mapToPrincipal($uids = null, $network = null, $app_id = null) {
		if (isset($uids) && ! is_array($uids) ) {
			$uids = array($uids);
		}

		return $this->call_method('ringsideidm.users.mapPrincipal', array('uids' => $uids, 'nid' => $network, 'aid' => $app_id));
	}

	/**
	 * Translates one or more pids to a network uid. If the network is not speficied,
	 * the pids will be translated from the principal ID to the network calling the application.
	 * If the network is specified, the pids are translated from the principal ID to the specified network uid.
	 *
	 * @param array $uids optional - list of uids to be translated; defaults to the logged-in user
	 * @param string $network optional - the network to translate to; defaults to this application's home network
	 */
	public function users_mapToSubject($uids = null, $network = null, $app_id = null) {
		if (isset($uids) && ! is_array($uids) ) {
			$uids = array($uids);
		}

		return $this->call_method('ringsideidm.users.mapSubject', array('uids' => $uids, 'nid' => $network, 'aid' => $app_id));
	}

	/**
	 * Get the PROFILE FBML for a user. 
	 * To override and get for any application you must be running from DEFAULT app.
	 * 
	 * @param string $uid the user id to get
	 * @param string $aid application to retrieve for
	 */
	
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
	public function comments_delete( $xid, $cid, $aid = null )
	{
		$params = array();
		$params['xid'] = $xid;
		$params['cid'] = $cid;
		if ( $aid != null )
		{
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

	/**
	 * Just like friends_search but it queries all users.
	 *
	 * @param unknown_type $query
	 * @return unknown
	 */
	public function users_search($query)
	{
		$params = array();
		$params['query'] = $query;
		return $this->call_method('ringside.users.search', $params);
	}

	/**
	 * Adds a pricing plan for the specified application.
	 *
	 * @param string $plan_name The name of this pricing plan.
	 * @param int $aid The id of the application for whom this pricing plan is being created.
	 * @param float $price The price charged for one month of application use.
	 * @param string $description The description of the benefits provided by this plan.
	 * @param int $num_friends The number of friends that a user is allowed to pay for via this plan.
	 */
	public function subscription_add_app_plan( $aid, $plan_name, $price, $description, $num_friends = null ) {
		if( !isset( $aid ) ) {
			throw new Exception( "Application id is required!" );
		}
		if( !isset( $plan_name ) ) {
			throw new Exception( "Plan name is required!" );
		}
		if( !isset( $price ) ) {
			throw new Exception( "Price is required!" );
		}
		$params = array();
		$params[ 'aid' ] = $aid;
		$params[ 'plan_name' ] = $plan_name;
		$params[ 'price' ] = $price;
		$params[ 'description' ] = $description;
		$params[ 'numfriends' ] = $num_friends;
		return $this->call_method( 'ringside.subscriptions.addAppPlan', $params );
	}

	/**
	 * Adds a pricing plan for the specified application.
	 *
	 * @param int $aid The id of the application for whom this pricing plan is being created.
	 */
	public function subscription_delete_app_plan( $planid ) {
		if( !isset( $planid ) ) {
			throw new Exception( "Plan id is required!" );
		}
		$params = array();
		$params[ 'planid' ] = $planid;
		return $this->call_method( 'ringside.subscriptions.deleteAppPlan', $params );
	}
	
	/**
	 * Returns payment plan information for an application as an array.
	 */
	public function subscriptions_get_app_plans( $aid ) {
		if( !isset( $aid ) ) {
			throw new Exception( "Application id is required!" );
		}
		$params = array();
		$params[ 'aid' ] = $aid;
		return $this->call_method( 'ringside.subscriptions.getAppPlans', $params );
	}
    
    /**
     * Returns payment plan information for an application as an array.
     */
    public function subscriptions_get( $uid, $nid = null ) {
        if( !isset( $uid ) ) {
            throw new Exception( "User id is required!" );
        }
        $params = array();
        $params[ 'uid' ] = $uid;
        if( isset( $nid ) ) {
            $params[ 'nid' ] = $nid;
        }
        return $this->call_method( 'ringside.subscriptions.get', $params );
    }
	
    /**
     * Subscribes a user to an application according to the specified plan.
     *
     * @param integer $uid The user id of the user to be subscribed to the application.
     * @param integer $aid The application id of the app being subscribed to.
     * @param integer $planid The plan id for the plan being subscribed to.
     * @param integer $ccType 
     * @param unknown_type $ccNumber
     * @param unknown_type $expDate
     * @param unknown_type $firstName
     * @param unknown_type $lastName
     * @param unknown_type $email
     * @param unknown_type $phone
     * @return true if successful
     */
    public function subscribe_user_to_app( $uid, $nid = null, $aid, $planId, $ccType, $ccNumber, 
       $expDate, $firstName = null, $lastName = null, $email = null, $phone = null ) {
        if( !isset( $uid ) ) {
            throw new Exception( "User id is required!" );
        }
        if( !isset( $aid ) ) {
            throw new Exception( "Application id is required!" );
        }
        if( !isset( $planId ) ) {
            throw new Exception( "Plan id is required!" );
        }
        if( !isset( $ccType ) ) {
            throw new Exception( "Credit card type is required!" );
        }
        if( !isset( $ccNumber ) ) {
            throw new Exception( "Credit card number is required!" );
        }
        if( !isset( $expDate ) ) {
            throw new Exception( "Credit card expiration date is required!" );
        }
        $params = array();
        $params[ 'uid' ] = $uid;
        $params[ 'aid' ] = $aid;
        $params[ 'planid' ] = $planId;
        $params[ 'cctype' ] = $ccType;
        $params[ 'ccn' ] = $ccNumber;
        $params[ 'expdate' ] = $expDate;
        if( isset( $firstName ) ) {
            $params[ 'firstname' ] = $firstName;
        }
        if( isset( $lastName ) ) {
            $params[ 'lastname' ] = $lastName;
        }
        if( isset( $email ) ) {
            $params[ 'email' ] = $email;
        }
        if( isset( $phone ) ) {
            $params[ 'phone' ] = $phone;
        }
        return $this->call_method( 'ringside.subscribe.userToApp', $params );
    }

    /**
     * Subscribes a user and their friends to an application according to the specified plan.
     *
     * @param integer $uid The user id of the user to be subscribed to the application (and the user who is paying).
     * @param integer $aid The application id of the app being subscribed to.
     * @param integer $planid The plan id for the plan being subscribed to.
     * @param integer $ccType 
     * @param unknown_type $ccNumber
     * @param unknown_type $expDate
     * @param array $friends An array of friends to be included in this subscription.  Each friend is itself an array 
     *                       containing keys 'uid' and 'nid'.
     * @param unknown_type $firstName
     * @param unknown_type $lastName
     * @param unknown_type $email
     * @param unknown_type $phone
     * @return true if successful
     */
    public function social_pay_subscribe_users_to_app( $uid, $nid = null, $aid, $planId, $ccType, $ccNumber, 
       $expDate, $friends, $firstName = null, $lastName = null, $email = null, $phone = null ) {
        if( !isset( $uid ) ) {
            throw new Exception( "User id is required!" );
        }
        if( !isset( $aid ) ) {
            throw new Exception( "Application id is required!" );
        }
        if( !isset( $planId ) ) {
            throw new Exception( "Plan id is required!" );
        }
        if( !isset( $ccType ) ) {
            throw new Exception( "Credit card type is required!" );
        }
        if( !isset( $ccNumber ) ) {
            throw new Exception( "Credit card number is required!" );
        }
        if( !isset( $expDate ) ) {
            throw new Exception( "Credit card expiration date is required!" );
        }
        $params = array();
        $params[ 'uid' ] = $uid;
        $params[ 'aid' ] = $aid;
        $params[ 'planid' ] = $planId;
        $params[ 'cctype' ] = $ccType;
        $params[ 'ccn' ] = $ccNumber;
        $params[ 'expdate' ] = $expDate;
        $params[ 'friends' ] = $friends;
        if( isset( $firstName ) ) {
            $params[ 'firstname' ] = $firstName;
        }
        if( isset( $lastName ) ) {
            $params[ 'lastname' ] = $lastName;
        }
        if( isset( $email ) ) {
            $params[ 'email' ] = $email;
        }
        if( isset( $phone ) ) {
            $params[ 'phone' ] = $phone;
        }
        return $this->call_method( 'ringside.subscribe.usersToApp', $params );
    }
    
    	/**
	 * This method allows an applicaiton to upload files it would like to use later
	 * on.  For example profile page will use this to upload profile pics.  Developer
	 * app will use this for application icons. There are two variations of this
	 * method
	 * 1. This version supports file_upload transfer. It does not upload the file
	 * to the API server, but rather assumes everything is happening in a single
	 * instance.
	 * 2. the more robust version deals with different varying servers.
	 *
	 * @param unknown_type $upload
	 * @param unknown_type $filename
	 * @param unknown_type $aid
	 * @param unknown_type $caption
	 * @return unknown
	 */
	public function move_upload( $upload, $filename, $aid = null, $caption = null ) {
		return $this->call_method('ringside.admin.upload',
		array('tmp_filename' => $upload, 'filename' => $filename, 'aid' => $aid, 'caption' => $caption));
	}

}

?>
