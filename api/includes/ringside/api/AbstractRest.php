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

require_once ('ringside/api/db/RingsideApiDbDatabase.php');
require_once ('ringside/api/bo/App.php');
require_once ('ringside/api/OpenFBAPIException.php');
require_once( "ringside/api/ServiceFactory.php" );

abstract class Api_AbstractRest
{
   // Following need to move out to a SESSION wrapper.
   const SESSION_ID = 'session_id';
   const SESSION_API_KEY = 'api_key';
   const SESSION_APP_ID = 'app_id';
   const SESSION_USER_ID = 'uid';
   const SESSION_NETWORK_ID = 'network_key';
   const SESSION_EXPIRES = 'expires';
   const SESSION_EXPIRES_VALUE_NEVER = 'never';
   const SESSION_CALL_ID = 'call_id';
   const SESSION_INFINITE = 'infinite';

   // Auth related session values.
   const SESSION_APPROVED = 'approved';
   const SESSION_TYPE = 'type';
   const SESSION_TYPE_VALUE_AUTH = 'auth_token';
   const SESSION_TYPE_VALUE_SESS = 'session_key';

   /** The paramters for this API. */
   private $m_apiParams = array();

   /** Hold onto session object for this request */
   private $m_session = null;

   /** Hold onto the request context */
   private $m_context = null;

   /** Hold onto the SERVER object */
   private $m_server = null;

   /**
    * Constructor
    */
   public function __construct()
   {

   }

   public function getAppId()
   {
      return $this->getSessionValue(self::SESSION_APP_ID);
   }

   /**
    * Get the user id.
    *
    * @return unknown The user id.
    */
   public function getUserId()
   {
      return $this->getSessionValue(self::SESSION_USER_ID);
   }

   /**
    * Get the network id.
    *
    * @return unknown The user id.
    */
   public function getNetworkId()
   {
      $nid = $this->getSessionValue(self::SESSION_NETWORK_ID);
      if($nid == null)
      {
         $nid = $this->m_context->getNetworkKey();
      }
      return $nid;
   }

   /**
    * Lifecyle method to inject the context into the REST handler
    *
    * @param unknown_type $context
    */
   public function _setContext(Api_RequestContext &$context)
   {
      $this->m_context = &$context;
      $this->m_apiParams = &$context->getParameters();
   }

   public function &getContext()
   {
      return $this->m_context;
   }

   /**
    * Lifecyle method to inject session into this object.
    *
    * @param array $session
    */
   public function _setSession(&$session)
   {
      $this->m_session = &$session;
   }

   /**
    * Return the session object if needed.
    *
    * @return SESSION
    */
   public function &getSession()
   {
      return $this->m_session;
   }

   /**
    * Lifecyle initialization to setup the SERVER object.
    *
    * @param Api_Server $server
    */
   public function _setServer(OpenFBServer &$server)
   {
      $this->m_server = $server;
   }

   /**
    * Return access to the server object.
    *
    * @return Api_Server
    */
   public function &getServer()
   {
      return $this->m_server;
   }

   /**
    * Get the value of a session key for this request, null if session not in context or value not in context;
    *
    * @param string $key to look for
    * @return null if session[key] not available.  value otherwise.
    * @throws OpenFBAPIException if session is not in context
    */
   public function getSessionValue($key)
   {
      if(! isset($this->m_session))
      {
          return null;
//         throw new OpenFBAPIException(FB_ERROR_MSG_BUSTED_SESSION, FB_ERROR_CODE_UNKNOWN_ERROR);
      }else if(isset($this->m_session[$key]))
      {
         return $this->m_session[$key];
      }else
      {
         return null;
      }
   }

   /**
    * Set a value in session, if session is in context.
    *
    * @param string $key to set
    * @param mixed $value to set
    * @return old value if in session already.
    * @throws OpenFBAPIException is session not in context
    */
   public function setSessionValue($key, $value)
   {
      $oldValue = $this->getSessionValue($key);
      $this->m_session[$key] = $value;
      return $oldValue;
   }

   /**
    * Get the paramters for this API.
    * @return unknown The paramters for this API.
    */
   public function &getApiParams()
   {
      return $this->m_apiParams;
   }

   /**
    * Return a specific param key.
    *
    * @param unknown_type $key
    * @return unknown
    */
   public function getApiParam($key, $default = null)
   {
      $value = $default;
      if ( isset($this->m_apiParams[$key]) && !$this->isEmpty($this->m_apiParams[$key])) 
      {
         $value = $this->m_apiParams[$key];
      }
      return $value;
   }

   public function getRequiredApiParam($key)
   {
      $this->checkRequiredParam($key);
      return $this->getApiParam($key);
   }

   /**
    * Load the session from the session key.
    */
   abstract public function loadSession();

   /**
    * Specific point by which delegation can occur.
    */
   abstract public function delegateRequest();

   /**
    * Validate the session is correct and not expired.
    */
   abstract public function validateSession();

   /**
    * Validate the API Key ([TODO] and load basic APP data?).
    */
   abstract public function validateApiKey();

   /**
    * Validate the SIGNATURE by build MD5 checksum.
    */
   abstract public function validateSig();

   /**
    * Validate the version matches.
    */
   abstract public function validateVersion();

   /**
    * Validate the call_is being incremented on each and every call.
    */
   abstract public function validateCallId();

   /**
    * Validate the request has what it needs
    */
   abstract public function validateRequest();

   /**
    * Execute the REST api.
    */
   abstract public function execute();

   /**
    * Validate that the list of required parameters are defined in the api parameters passed in on
    * the constructor.
    *
    * @param array $requiredParams The list of required parameters
    *
    * @throws OpenFBAPIException if any of the required paramters are not set in the
    *                              api parameters passed in on the constructor.
    */
   public function checkRequiredParams($requiredParams)
   {
      foreach($requiredParams as $param)
      {
         $this->checkRequiredParam($param);
      }

   }

   /**
    * Validate a given parameter is avialable and not empty.
    *
    * @param string $parameter
    * @throws OpenFBAPIException if any of the required paramters are not set in the
    *                              api parameters passed in on the constructor.
    */
   public function checkRequiredParam($parameter)
   {
      if(! isset($this->m_apiParams[$parameter]) || $this->isEmpty($this->m_apiParams[$parameter]))
      {
         throw new OpenFBAPIException("The " . $parameter . " must be specified.", FB_ERROR_CODE_PARAMETER_MISSING);
      }

   }

   /**
    * Validate that at least one of the requiredParamSet options is set.
    *
    * @param array $requiredParamSet the set of parameters, of which at least one must be provided.
    * @return boolean whether at least one of the parameters is set; will only return true
    * @throws OpenFBApiException if none of the required parameters is set.
    */
   public function checkOneOfRequiredParams($requiredParamSet)
   {
       foreach ( $requiredParamSet as $param )
       {
           if ( isset($this->m_apiParams[$param]) && ! $this->isEmpty($this->m_apiParams[$param]))
           {
               return true;
           }
       }
       
       throw new OpenFBAPIException("At least one of '" . join("', '", $requiredParamSet) . "' must be specified.", FB_ERROR_CODE_PARAMETER_MISSING);
   }
   
   /**
    * If this is a cross app call, one app trying to execute something on another application
    * validate that the calling application is a DEFAULT enabled application as they should be the
    * only ones allowed to do this.
    *
    * @param integer $aid
    * @return true
    * @throws OpenFBApiException if the calling app is not allowed to make the call.
    */
   public function checkDefaultApp($aid = null)
   {
       // TODO: SECURITY: This disables security on app-to-app requests!
       return;
      /*
       * You can only cross check application information if
       * the calling application is a default application
       */
      $tad = $this->getAppId();
      
      error_log("Invoking API as $tad against application $aid");
      if(($aid == null) || ($aid != $tad))
      {
      	$appService = Api_ServiceFactory::create('AppService');
      	if ( null != $tad ) {
      	    // If a domain is calling this method, it should work
          	 $callingApp = $appService->getApp($tad);
             if (($callingApp == NULL) || (empty($callingApp)))
             {
                throw new OpenFBAPIException("Can not load calling application information ($aid,{ $tad })", FB_ERROR_CODE_UNKNOWN_ERROR);
             }
             $isDefault = $callingApp['isdefault'];
             if($isDefault == 0)
             {
                 error_log("Application $tad cannot get information on application $aid");
                throw new OpenFBAPIException(FB_ERROR_MSG_GRAPH_EXCEPTION, FB_ERROR_CODE_GRAPH_EXCEPTION);
             }
      	}
      }

      return true;
   }

   /**
    * Start the session object.
    * TODO: Think about where this really should live.
    *
    * @param id The ID of the product.
    */
   public function startSession($id = null)
   {
      if(! empty($id))
      {
         session_id($id);
      }
      session_start();
//      error_log("Session is as follows:");
//      error_log(var_export($_SESSION, true));
      $this->m_session = &$_SESSION;

   }

   /**
    * PHP empty function causes '0', 0, Array(), and FALSE to return true.  These really are not empty
    * to us, so instead we have our own isEmpty function that only returns true if the variable is
    * ""
    * null
    * !isset($var); such as var $var;  declared, but not value associated with it.
    *
    * @param mixed $var
    * @return bool
    */
   public static function isEmpty($var)
   {
      if(! isset($var) || is_null($var))
      {
         return true;
      }

      if(is_string($var) && strlen(rtrim($var)) == 0)
      {
         return true;
      }

      if(is_array($var) && count($var) == 0)
      {
         return true;
      }

      return false;
   }

}
?>
