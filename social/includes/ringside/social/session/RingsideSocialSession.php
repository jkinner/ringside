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
 * This class is used to establish or re-connect to the existing authenticated user identity. 
 * An authenticated user will have an established SocialSession. It will
 * contain information about the currently logged in user such as their USERID,
 * if they are logged in or not and what NETWORK this USERID has meaning to (such as
 * FACEBOOK) and which restserver.php should service their API calls.
 * 
 * In addition, the SocialSession acts as a keychain for restserver API sessions. 
 * This allows a user to build a collection of API sessions which can be re-used
 * between requests as long as the client can provide the users original social
 * session key.
 * 
 * An example of the use of a SocialSession would be:
 * 			// Determine the UID of the current user they have logged in
 *          // and there social_session_key is defined as either a cookie or
 * 	 	    // a request parameter
 * 			$session_key = $_REQUEST['social_session_key'];
 *			$network_session = new RingsideSocialSession($session_key);
 *			$uid = $network_session->getUserId();
 * 
 */
class RingsideSocialSession 
{
	const TRUST = '.social.trust.key';	 
	const PRINCIPAL_ID = '.social.principal.id';
	const USERID = '.social.user.id';
	const EXPIRY = '.social.user.expiry';
	const NETWORK = '.social.network.key';
	const APISESSION = '.social.api.session.';
	const CALLBACK = '.social.api.callback.';
	const LOGGED_IN = '.social.api.loggedin.';
	
	private $sessionKey;

	/**
	 * When constructed, establishes a PHP session if one does not already exist
	 * or resumes an existing session if $sessionKey already exists as a PHP session.
	 * 
	 * Once constructed, the $_SESSION[] will point to this users current social
	 * context.
	 *
	 * @param string $sessionKey Optional. If none is provided a PHP session key
	 * will be automatically generated and can be obtained by calling getSessionKey().
	 */
	public function __construct( $sessionKey = null ) {
		if ( $sessionKey == null ) {
			if ( session_id() == '' ) {
				session_start();
			}
			$sessionKey = session_id();
			$this->sessionKey = $sessionKey;
		} else {
			$this->sessionKey = $sessionKey;
			if ( session_id() == '' ) {
				session_id($this->sessionKey);
				session_start();
			} else if ( session_id() != $sessionKey ) {
			    error_log("WARNING: RingsideSocialSession: Could not restart session. Retaining session ID ".session_id());
			}
		}
	}

	/**
	 * Invalidates the current social session and all of its child api sessions.
	 * NOTE: Does not destory the session, it only makes it unusable.
	 */
	public function clearSession() {
		unset ( $_SESSION[$this->sessionKey . RingsideSocialSession::USERID] );
		unset( $_SESSION[$this->sessionKey . RingsideSocialSession::NETWORK] );
		foreach ( $_SESSION as $k=>$v ) {
			if( strpos( $k, $this->sessionKey ) == 0 ) {
				unset( $_SESSION[$k] );
			}
		}
	}
	 
	/**
	 * Sets the principal or parent identity of this session. Principals can be used
	 * to discover other identities for this user. Principals can be converted to
	 * alternate user identities by using the users_MapSubject Ringside rest api call
	 * or internally using the Social_DatabaseIdMappingService.
	 *
	 * @param string $pid a token identifying the principal
	 */
	public function setPrincipalId($pid)
	{
		$_SESSION[$this->sessionKey . RingsideSocialSession::PRINCIPAL_ID] = $pid;
	}
	
	/**
	 * Returns the principal or parent identity of this session.
	 * @see setPrincipalId
	 * @return string A token identifying the principal
	 */
	public function getPrincipalId()
	{
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::PRINCIPAL_ID] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::PRINCIPAL_ID] : null;
	}
	
	public function setTrust($trust_key)
	{
		$_SESSION[$this->sessionKey . RingsideSocialSession::TRUST] = $trust_key;	
	}
	
	public function getTrust()
	{
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::TRUST] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::TRUST] : null;
	}
	
	/**
	 * Sets a string token representing the current user.
	 *
	 * @param string $uid 
	 */
	public function setUserId( $uid ) {
	    if ( ! isset($_SESSION[$this->sessionKey.self::NETWORK]) ) {
	        error_log("Warning: Setting user ID of social session with no network set");
	    }
		$_SESSION[$this->sessionKey . RingsideSocialSession::USERID] = $uid;
	}
	 
	/**
	 * Returns a string token representing the current user.
	 *
	 * @return string $uid
	 */
	public function getUserId( )  {
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::USERID] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::USERID] : null;
	}

	public function setExpiry($expiry) { 
		$_SESSION[$this->sessionKey . RingsideSocialSession::EXPIRY] = $expiry;
	}
	
	public function getExpiry() {
	   return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::EXPIRY] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::EXPIRY] : null;
	}
	
	public function setNetwork( $networkKey ) {
		$_SESSION[$this->sessionKey . RingsideSocialSession::NETWORK] = $networkKey;
	}
	 
	public function getNetwork( )  {
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::NETWORK] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::NETWORK] : null;
	}
	/**
	 * Returns a string which is used to identify the current PHP session key.
	 *
	 * @return string 
	 */
	public function getSessionKey() {
		return $this->sessionKey;
	}
	
	/**
	 * Inserts a restserver API key into this session's keychain. A social
	 * session can store muliple API session which represent this user's permission
	 * to use a specific application API on the restserver.
	 *
	 * @param string $apiKey The api_key identifing the application to be accessed
	 * @param string $apiSessionKey The session key generated by authenticating a user against
	 * the provided api_key
	 */
	public function addApiSessionKey( $apiKey, $apiSessionKey ) {
		$_SESSION[$this->sessionKey . RingsideSocialSession::APISESSION . $apiKey ] = $apiSessionKey;
	}
	
	/**
	 * Looks up and returns a restserver API session which has been previsouly used
	 * to access $apiKey.
	 * 
	 * returns null if the session does not exist.
	 *
	 * @param string $apiKey The api_key identifing the application to be accessed
	 * @return string A valid api session key
	 */
	public function getApiSessionKey( $apiKey ) {
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::APISESSION . $apiKey] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::APISESSION . $apiKey] : null;
	}
	
	/**
	 * Removes an existing restserver API session from the keychain for this user.
	 *
	 * @param string $apiKey The key to remove.
	 */
	public function unsetApiSessionKey( $apiKey ) {
		unset( $_SESSION[$this->sessionKey . RingsideSocialSession::APISESSION] );
	}
	 
	public function setCallbackUrl($url)
	{
		$_SESSION[$this->sessionKey . RingsideSocialSession::CALLBACK] = $url;
	}
	
	public function getCallbackUrl()
	{
		return isset( $_SESSION[$this->sessionKey . RingsideSocialSession::CALLBACK] ) ? $_SESSION[$this->sessionKey . RingsideSocialSession::CALLBACK] : null;
	}
	
	/**
	 * Checks the session to verify that RingsideSocialSession::LOGGED_IN
	 * is set in the PHP session and returns it. Will return true if the
	 * user has successully logged in.
	 *
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		if(isset( $_SESSION[$this->sessionKey . RingsideSocialSession::LOGGED_IN] ))
		{
			return $_SESSION[$this->sessionKey . RingsideSocialSession::LOGGED_IN];
		}else
		{
			return false;
		}
	}
	
	/**
	 * Used to set the session's RingsideSocialSession::LOGGED_IN value.
	 * Set to true if this user has been validated by loggin in.
	 *
	 * @param unknown_type $b
	 */
	public function setLoggedIn($b)
	{
		$_SESSION[$this->sessionKey . RingsideSocialSession::LOGGED_IN] = $b;
	}
	
	/**
	 * Renders the contents of the session as a string when this class is printed.
	 *
	 * @return string
	 */
	public function __toString() {
		$str = '[RingsideSocialSession] { ';
		foreach( $_SESSION as $key => $value ) {
			$str .= $key . ' = ' . $value . ', ';
		}
		$str .= '}';
		return $str;
	}
}

?>
