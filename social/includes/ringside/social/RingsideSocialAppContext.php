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


class RingsideSocialAppContext {

   const CTX_API_KEY = 'api_key';
   const CTX_FLAVOR = 'flavor';
   const CTX_IFRAME = 'iframe';
   const CTX_INCANVAS = 'in_canvas';
   const CTX_ADDED = 'added';
   const CTX_USER = 'user';
   const CTX_NETWORK_ID = 'nid';
   const CTX_SOCIAL_SESSION = 'soc_session_key';
   const CTX_TIME = 'time';
   const CTX_IS_AJAX = 'is_ajax';
   const CTX_NETWORK_PRINCIPAL = 'nuser';
   const CTX_REQUEST_METHOD = 'request_method';
   const CTX_EXPIRES = 'expires';
   const CTX_PROFILE_UPDATE_TIME = 'profile_update_time';
   const CTX_SESSION_KEY = 'session_key';
   
   private $m_request;
   private $m_prefix;
   
   /**
    * Create the context class which represents the call to an
    * Application
    * 
    * @param $params mixed any existing params you want set. 
    * @param $prefix string prefix to use before each variable.
    */
   public function __construct( $params = null, $prefix = 'fb_sig' ) { 
      $this->m_request = array();
      if ( $params != null ) { 
         array_merge( $this->m_request, $params );
      }
      $this->m_prefix = $prefix;
   }
   
   public function setKey( $key, $value ) { 
      $this->m_request[$this->m_prefix . '_' . $key] = $value;      
   }
   
   public function unsetKey ( $key ) { 
      unset( $this->m_request[$this->m_prefix . '_' . $key] );
   }   
   
   public function setApiKey( $value ) { 
      $this->setKey( self::CTX_API_KEY, $value );
   }
      
   public function setFlavor( $value ) { 
      $this->setKey( self::CTX_FLAVOR, $value );
   }
   
   public function setIFrame( $value ) { 
      $this->setKey( self::CTX_IFRAME, $value );
   }
   
   public function setInCanvas( $value ) { 
      $this->setKey( self::CTX_INCANVAS, $value );
   }

   public function setIsAppAdded( $value ) { 
      $this->setKey( self::CTX_ADDED, $value );
   }
   
   public function setUser( $value ) { 
      $this->setKey( self::CTX_USER, $value );
   }
   
   public function setSessionKey( $value ) { 
      $this->setKey( self::CTX_SESSION_KEY , $value );
   }
   
   public function setProfileUpdateTime( $value ) { 
      $this->setKey( self::CTX_PROFILE_UPDATE_TIME, $value );
   }
   
   public function setExpires( $value ) { 
      $this->setKey( self::CTX_EXPIRES, $value );
   }
   
   public function setRequestMethod( $value ) { 
      $this->setKey( self::CTX_REQUEST_METHOD, $value );
   }

   public function setNetworkId( $value ) { 
      $this->setKey( self::CTX_NETWORK_ID, $value );
   }
   
   public function setPrincipalId( $value ) { 
      $this->setKey( self::CTX_NETWORK_PRINCIPAL, $value );
   }
   
   public function setSocialSessionKey( $value ) { 
      $this->setKey( self::CTX_SOCIAL_SESSION, $value );
   }
   
   public function setIsAjax( $value ) {
   	$this->setKey( self::CTX_IS_AJAX, $value);
   }
   
   public function setTime($value) {
   	$this->setKey( self::CTX_TIME , $value);
   }
   
   /**
    * Proper way to get the parameters back
    * the SIG is populated via this method as 
    * well. 
    *
    * @return array parameters of context to pass to application.
    */
   public function getParameters( $secretKey ) {
      unset( $this->m_request[$this->m_prefix] );
      $sig = RingsideSocialUtils::makeSig( $this->m_request, $secretKey, $this->m_prefix );
      $this->m_request[$this->m_prefix] = $sig;     
      return $this->m_request;
   }

}

?>