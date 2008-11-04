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

require_once( 'LocalSettings.php' );
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'shindig/gadgets/samplecontainer/BasicGadgetToken.php' );
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( "ringside/social/session/RingsideSocialSession.php" );
require_once( 'ringside/api/Session.php' );




/**
 * Layers the ability to construct the apropritate FB API
 * Client from an OS Token.
 * 
 * @author William Reichardt <wreichardt@ringsidenetworks.com>
 * 
 */

class  RingsideGadgetToken extends BasicGadgetToken {
    private $socialSession;
	
  /**
   * Generates a token from an input array of values
   * @param owner owner of this gadget
   * @param viewer viewer of this gadget
   * @param app application id
   * @param domain domain of the container
   * @param appUrl url where the application lives
   * @param moduleId module id of this gadget 
   * @throws BlobCrypterException 
   */
  static public function createFromSocialSession($stringToken)
  {
  	    ini_set( 'session.use_cookies', '0' );
    	ini_set('session.save_handler', 'user');
      	
	    session_set_save_handler(array('Session', 'open'),
	         array('Session', 'close'),
	         array('Session', 'read'),
	         array('Session', 'write'),
	         array('Session', 'destroy'),
	         array('Session', 'gc')
	      );
  	
  		$partsArry=explode(":",$stringToken);
		$session_key=$partsArry[0];
		$api_key=$partsArry[2];
		$network_session = new RingsideSocialSession($session_key);
  	
  		$uid = $network_session->getUserId();
		$owner=$partsArry[5];
		if($owner==''||$owner=='null'||$owner=='undefined'){
			$owner=$uid;
		}
		$viewer=$uid;
		$app=$api_key;
		$domain=$partsArry[4];
		
		$adminClient=RingsideSocialUtils::getAdminClient();
	  	$app_properties = $adminClient->admin_getAppProperties(array('api_key', 'callback_url', 'canvas_url'), null, null, $api_key);
  		$moduleId=$app_properties['canvas_url'];
		$appUrl=$app_properties['callback_url'];
  		$app=$app_properties['api_key'];
  		error_log("******* Creating RingsideGadgetToken(null,null,$owner, $viewer, $app, $domain, $appUrl, $moduleId)");
		$rsToken=new RingsideGadgetToken(null,null,$owner, $viewer, $app, $domain, $appUrl, $moduleId);
  		$rsToken->setSocialSession($network_session);
  		
		return $rsToken;
  }

  
  
  /**
   * {@inheritDoc}
   */
  
  public function getSocialSession() {
    return $this->socialSession;
  }

  /**
   * Returns the Ringside Social Session used in the construction of this Token.
   *
   * @param unknown_type $session
   */
  public function setSocialSession($session) {
    $this->socialSession=$session;
  }
  
  /**
   * Convenience method which will look up an apps secret given that its api_key was provided
   * when the token was constructed.
   *
   * @return unknown api_secret
   */
  public function getAppSecret(){
  	$adminClient=RingsideSocialUtils::getAdminClient();
  	$api_key=$this->getAppId();
  	$app_properties = $adminClient->admin_getAppProperties(array('api_key', 'secret_key', 'canvas_url'), null, null, $api_key);
  	$secretKey=$app_properties['secret_key'];
  	return $secretKey;
  }

  /**
   * Returns a valid app client using the information inside this gadget token.
   * The client will act on behalf of the api_key inside this gadget token.
   *
   * @return unknown
   */
  public function getAppClient(){
  	$apiKey=$this->getAppId();
	$socialSession=$this->getSocialSession();
	$secretKey=$this->getAppSecret();
	error_log("OS Producing Client: apiKey= $apiKey secretKey= $secretKey ");
	$apiSessionKeyApp = RingsideSocialUtils::getApiSessionKey( $apiKey, $secretKey, $socialSession );
    $apiClientApplication = new RingsideApiClientsRest( $apiKey, $secretKey, $apiSessionKeyApp );
  	return $apiClientApplication;
  }
  
//  public function getModuleId() {
//    return intval($this->tokenData[$this->MODULE_KEY]);
//  }

  
}