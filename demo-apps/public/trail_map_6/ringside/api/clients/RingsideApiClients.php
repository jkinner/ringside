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
include_once 'ringside/api/clients/facebook.php' ;
include_once 'ringside/api/clients/RingsideApiClientsConfig.php' ;

class RingsideApiClients extends Facebook {

   private $webUrl = null;
   private $serverUrl = null;
   private $flavor = null;
   
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
  public function __construct($api_key, $secret, $webUrl = null, $serverUrl = null, $client = null ) {
    
    $this->api_key    = $api_key;
    $this->secret     = $secret;

    $this->webUrl = ( $webUrl != null ) ? $webUrl : RingsideApiClientsConfig::$webUrl;
    $this->serverUrl = ( $serverUrl != null ) ? $serverUrl : RingsideApiClientsConfig::$serverUrl;
    
    if ( $client != null ) { 
       $this->api_client = $client;
    } else { 
       $this->api_client = new RingsideApiClientsRest($api_key, $secret, null, $this->serverUrl );
    }
    
    $this->validate_fb_params();
    if (isset($this->fb_params['friends'])) {
      $this->api_client->friends_list = explode(',', $this->fb_params['friends']);
    }
    if (isset($this->fb_params['added'])) {
      $this->api_client->added = $this->fb_params['added'];
    }
    if (isset( $this->fb_params['flavor'])) {
       $this->flavor = $this->fb_params['flavor'];
    }
  }
  
  /**
   * In the request it might indicate what flavor to render.
   *
   * @return string flavor to render. 
   */
  public function getFlavor() {
     return $this->flavor;
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
    exit;
  }

  public function get_openfb_url() {
        return $this->webUrl;
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


}

?>
