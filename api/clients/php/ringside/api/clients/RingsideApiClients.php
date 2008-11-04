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

include_once 'ringside/api/clients/RingsideApiClientsRest.php';
include_once 'ringside/api/clients/RingsideApiClientsConfig.php' ;
include_once 'ringside/api/clients/facebook.php' ;

class RingsideApiClients extends Facebook {

   private $webUrl = null;
   private $serverUrl = null;
   private $flavor = null;
   private $network = null;
   private $network_user = null;
   private $socialUrl = null;
   private $localClient = false;
   
   /**
    * Construct the WEB client and the REST client. 
    * webUrl and serverUrl allow you to repoint the client and web to different urls when the object
    * is constructed.  If your application is being accesed from an openfb server or a facebook you can 
    * determine the originator and just create the client to point to the right place. 
    * 
    * If they are not passed in they will default to localhost/openfb
    * 
    * @param string $api_key
    * @param string $secret
    * @param string $webUrl Web URL for the social network
    * @param string $serverUrl Server URL for rest API of social network
    * @param RestClient $client Override the rest client to use/return.
    */
  public function __construct($api_key, $secret, $webUrl = null, $serverUrl = null, $socialUrl = null ) {
    
    $this->api_key    = $api_key;
    $this->secret     = $secret;

    // TODO: MAPPING: Different configurations by network; these are the defaults
    $this->webUrl = ( $webUrl != null ) ? $webUrl : RingsideApiClientsConfig::$webUrl;
    $this->serverUrl = ( $serverUrl != null ) ? $serverUrl : RingsideApiClientsConfig::$serverUrl;
    $this->socialUrl = ( $socialUrl != null ) ? $socialUrl : RingsideApiClientsConfig::$socialUrl;
    
    // The REST client to the api server is created pointing at the default server url.
    $this->api_client = new RingsideApiClientsRest($api_key, $secret, null, $this->serverUrl );
    $this->validate_fb_params();
    $this->api_client->setDefaultServer( $this->serverUrl, $this->api_client->session_key );
    
    if (isset($this->fb_params['friends'])) {
      $this->api_client->friends_list = explode(',', $this->fb_params['friends']);
    }
    if (isset($this->fb_params['added'])) {
      $this->api_client->added = $this->fb_params['added'];
    }
    if (isset( $this->fb_params['flavor'])) {
       $this->flavor = $this->fb_params['flavor'];
    } 
    if (isset( $this->fb_params['nid'])) {
       $this->network = $this->fb_params['nid'];
       $this->api_client->setNetworkKey($this->network);
    }
    if (isset( $this->fb_params['nuser'])) {
       $this->network_user = $this->fb_params['nuser'];
    }
//    error_log("Application running for user ".$this->network_user." on ".$this->network);
  }
  
  /**
   * In the request it might indicate what flavor to render.
   *
   * @return string flavor to render. 
   */
  public function getFlavor() {
     return $this->flavor;
  }
  
  public function setLocalClient( $islocal ) { 
     $this->localClient = $islocal;
  }

  public function redirect($url) {
    
     // TODO need to test this for different cases, I think we need to escape some stuff in the string for the regex match. 
     //    $regexDomain = 'facebook\.com';
    
    if ($this->in_fb_canvas()) {
      echo '<fb:redirect url="' . $url . '"/>';
    } else if (preg_match('/^https?:\/\/([^\/]*\.)?'.str_replace('/', '\\/', $this->webUrl).'(:\d+)?/i', $url)) {
      // make sure facebook.com url's load in the full frame so that we don't
      // get a frame within a frame.
      echo "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
    } else {
      header('Location: ' . $url);
    }
    
    if ( $this->localClient == false ) {
       exit;
    }
    
  }

  public function get_openfb_url() {
        return $this->webUrl;
  }

  public function get_social_url() {
  	return $this->socialUrl;
  }
  public function get_add_url($next=null) {
    return self::get_openfb_url().'/add.php?api_key='.$this->api_key .
      ($next ? '&next=' . urlencode($next) : '');
  }

  public function get_login_url($next, $canvas) {
    return self::get_openfb_url().'/login.php?v=1.0&api_key=' . $this->api_key .
      ($next ? '&next=' . urlencode($next)  : '') .
      ($canvas ? '&canvas' : '');
  }

  public function get_mapping_url($next, $canvas) {
  	
    return self::get_social_url().'/map.php?v=1.0&method=map&api_key=' . $this->api_key .
    '&snid='.$this->network.
    '&sid='.$this->user.
    '&social_session_key='.$this->fb_params['soc_session_key'].
    '&session_key='.$this->fb_params['session_key'].
    '&next='.urlencode($next).
    ($canvas ? '&canvas' : '');
    
//method=map&pid=100000&nid=facebook&trust_key=facebook&aid=80000&social_session_key=75b646583c694f00c0b8c7c497e9d507      ($next ? '&next=' . urlencode($next)  : '') .
//      ($canvas ? '&canvas' : '');
  }
  
  public function get_network_user() {
  	 return $this->network_user;
  }
  
  public function get_network_id() {
  	 return $this->network;
  }
  
  public function require_network_login() {
    if ($user = $this->require_login() ) {
    	if ($user = $this->get_network_user()) {
    		return $user;
    	}
    }
    $this->redirect($this->get_mapping_url(self::current_url(), $this->in_frame()));
  }
  
  public static function get_facebook_url($subdomain = 'www') {
  	$url = RingsideApiClientsConfig::$webUrl;
	switch( $subdomain ) {
		case 'api':
		  $url = RingsideApiClientsConfig::$serverUrl;
		  break;
		case 'apps':
		  $url = RingsideApiClientsConfig::$webUrl.'/canvas.php';
		  break;
	}
	return $url;
  	
  }
}

?>
