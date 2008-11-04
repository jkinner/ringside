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

class Api_RequestContext {
   
   const REQUEST_METHOD = 'method';
   const REQUEST_FORMAT = 'format';
   const REQUEST_CALLBACK = 'callback';
   const REQUEST_SIG = 'sig';
   const REQUEST_AUTH_TOKEN = 'auth_token';
   const REQUEST_VERSION = 'v';
   const REQUEST_SESSION_KEY = 'session_key';
   const REQUEST_CALL_ID = 'call_id';
   const REQUEST_API_KEY = 'api_key';
   const REQUEST_NETWORK_KEY = 'network_key';
   const REQUEST_PRIVATE_KEY = 'private_key';

   private $preserved;
   private $context;
   private $params; 
      
   /**
    * Static method to create context.  This method could become
    * a singletone, but right now acts a bit like a factory.
    *
    * @param array $request
    * @return Api_RequestContext 
    */
   public static function &createRequestContext( $request ) {
      $context = new Api_RequestContext();
      $context->loadContext( $request );
      return $context;
   }
   
   /**
    * Ensures context has all settings approriately set as well
    * differentiate between passed in parameters and the context.
    *
    * @param array $request
    */
   protected function loadContext( $request ) 
   { 
      
      $this->preserved = $request;
      $this->context = $request;
      $this->params = $request;

      $this->initParameter(self::REQUEST_METHOD);
      $this->initParameter(self::REQUEST_FORMAT);
      $this->initParameter(self::REQUEST_CALLBACK);
      $this->initParameter(self::REQUEST_SIG);
      $this->initParameter(self::REQUEST_AUTH_TOKEN);
      $this->initParameter(self::REQUEST_VERSION);
      $this->initParameter(self::REQUEST_SESSION_KEY);
      $this->initParameter(self::REQUEST_CALL_ID);
      $this->initParameter(self::REQUEST_API_KEY);
      $this->initParameter(self::REQUEST_NETWORK_KEY);
      $this->initParameter(self::REQUEST_PRIVATE_KEY);
            
   }
   
   /**
    * Makes sure context parmeters are set, even if its 
    * to null.  Also removes context parameters from the
    * params.
    *
    * @param string $parameter
    */
   private function initParameter( $parameter ) 
   {
      if ( isset( $this->context[$parameter] ) ) 
      { 
         unset( $this->params[ $parameter ] );         
      }
      else 
      {
         $this->context[$parameter] = null; 
      }
   }
   
   public function setPrivateKey( $privateKey ){
       $this->context[ self::REQUEST_PRIVATE_KEY ] = $privateKey;       
   }
   
   public function getPrivateKey() { 
      return $this->context[self::REQUEST_PRIVATE_KEY];
   }
   
    
   public function getMethod(){
      return $this->context[self::REQUEST_METHOD];
   }
    
   public function getFormat() { 
      return $this->context[self::REQUEST_FORMAT];
   }
   
   public function getCallback() { 
      return $this->context[self::REQUEST_CALLBACK];
   }
   
   public function getSig() { 
      return $this->context[self::REQUEST_SIG];
   }
   
   public function getApiKey() { 
      return $this->context[self::REQUEST_API_KEY];
   }
   
   public function getAuthToken() { 
      return $this->context[self::REQUEST_AUTH_TOKEN];
   }
   
   public function getVersion() { 
      return $this->context[self::REQUEST_VERSION];
   }
   
   public function getSessionKey() { 
      return $this->context[self::REQUEST_SESSION_KEY];
   }
   
   public function getCallId() { 
      return $this->context[self::REQUEST_CALL_ID];
   }
   
   public function getNetworkKey() { 
      return $this->context[self::REQUEST_NETWORK_KEY];
   }
   
   public function setNetworkKey( $networkKey ){
       $this->context[ self::REQUEST_NETWORK_KEY ] = $networkKey;       
   }
   
   public function getParameter( $parameter ) { 
      return isset( $this->params[ $parameter ] ) ? $this->params[ $parameter ] : null; 
   }

   public function &getParameters() { 
      return $this->params;
   }
   
   public function &getInitialRequest() { 
      return $this->preserved;
   }
}
?>