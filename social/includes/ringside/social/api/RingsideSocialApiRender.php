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

require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'ringside/social/RingsideSocialAppContext.php');
require_once( 'ringside/social/config/RingsideSocialConfig.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslParser.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslContext.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslFlavorContext.php' );
require_once( 'ringside/social/client/RingsideSocialClientInterface.php' );
require_once( 'ringside/api/clients/RingsideApiClientsRest.php' );

class RingsideSocialApiRender {

   const CANVASTYPE_IFRAME = 0;
   const CANVASTYPE_DSL = 1;
   const CANVASTYPE_OS = 2;
	
	private $appId;
   private $canvasUrl;
   private $flavor;
   private $path;
   private $params;

   private $iframe = null;
   private $redirect = null;
   private $error = null;
	private $raw = false;
	
   public function __construct( $appId, $canvasUrl, $flavor, $path, &$params ) {

       $this->appId = $appId;
      $this->canvasUrl = $canvasUrl;
      $this->flavor = $flavor;
      $this->path = $path;
      $this->params = &$params;
       
   }
   
   public function plugin( $app ) {
       
      $lastLevel = error_reporting( E_ERROR );
      $plugin = false;
      
      $class = 'RingsideApps'.ucfirst($app).'Plugin';
      $file = 'apps/'.strtolower($app).'/'.$class.'.php';
      if ( include_once( $file ) ) {
         if ( class_exists( $class ) ) {
            $plugin = new $class();
         } else { 
            error_log( "$class plugin not loaded." );         
         }
      } 
      
      error_reporting( $lastLevel );
      return $plugin;
      
   }
   
   public function execute( RingsideSocialClientInterface $socialClient) {

      $coreApp = $this->canvasUrl!=null?$this->plugin( $this->canvasUrl ):false;
      $text = 'empty';
      $status = 200;
      
      $callback = '';

      // if this is not a core (aka system) app, then make a remote call to the remote app
      // otherwise, render the results of the system app via a local call
      if ( $coreApp === false ) {
          
         $text = null;
          
         try {

            $adminClient = RingsideSocialUtils::getAdminClient();
         	$result = $adminClient->admin_getAppProperties( "application_name,use_iframe,api_key,secret_key,callback_url,application_id" , $this->appId, $this->canvasUrl, null, $socialClient->getCurrentNetwork() );
         	$callback = isset( $result['callback_url'] )? $result['callback_url'] : '';
            $apiKey = isset( $result['api_key'] )? $result['api_key'] : '';
            $apiSecret = isset( $result['secret_key'] )? $result['secret_key'] : '';
            $canvasType = isset( $result['use_iframe'] )? $result['use_iframe'] : '';
            $applicationid = isset( $result['application_id'] )? $result['application_id'] : '';

            $networkSession = $socialClient->getNetworkSession();
            $principalId = $networkSession->getPrincipalId();
            $apiSessionKeyApp = RingsideSocialUtils::getApiSessionKey( $apiKey, $apiSecret, $socialClient->getNetworkSession() );
            $apiClientApplication = new RingsideApiClientsRest( $apiKey, $apiSecret, $apiSessionKeyApp, null, $socialClient->getCurrentNetwork() );
            
            $isAppAdded = false;
            if ( $socialClient->inSession() ) { 
               $isAppAdded = $apiClientApplication->users_isAppAdded();
            	$idmaps = $apiClientApplication->users_mapToPrincipal( array($socialClient->getCurrentUser()) );
            	$nuser = null;
            	if ( !empty ( $idmaps )
            	     && null != $socialClient->getCurrentUser() ) {
        	         foreach ( $idmaps as $idmap )
        	         {
        	             if ( $idmap['uid'] == $socialClient->getCurrentUser() )
        	             {
        	                 $nuser = $idmap['pid'];
        	             }
        	         }
            	}
            	
               // TODO: Move setting network user in network session into login.php and map.php?
               $networkSession->setPrincipalId($nuser);
         	}
            
            $headers = array();
            $fbmlText = $this->renderRemote( $callback , $apiKey, $apiSecret, $canvasType, $isAppAdded, $apiSessionKeyApp, $socialClient, $headers, $status );
//            error_log("Status for $callback is $status");
            if ( $fbmlText !== null && !empty($fbmlText)) { 
               if ( strncmp( $headers['content-type'], 'text/html', 9 ) === 0 ) {
                  $this->raw = false;
                  $text = $this->renderFbml( $fbmlText, $socialClient->getNetworkSession(), $apiClientApplication , $applicationid);
               	// Need $socialUrl
               	if ( include('LocalSettings.php') ) {
               	
                  	$extra_end_scripts = <<<EOF

<script type='text/javascript'><!--
if ( typeof Ajax != 'undefined' ) {
  Ajax.API_KEY='$apiKey';
  Ajax.RENDER_URL='$socialUrl/render.php';
  Ajax.PROXY_URL='$socialUrl/proxyjs.php';
}
//--></script>
EOF
;
							// These are ONLY emitted for FBML remote applications to support FBJS!
							$text .= $extra_end_scripts;
               	}
               } else if ( strncmp( $headers['content-type'], 'text/', 5 ) === 0 ) {
                  // Send all other text (text/xml, text/css, etc.) back raw
                  $this->raw = true;
                  $text = $fbmlText;
               } else {
               	error_log("No way to handle content type ".$headers['content-type']);
                  $this->error = RingsideSocialUtils::SOCIAL_ERROR_RENDER_EXCEPTION;
               }
            } else {
                if ( $status < 200 ) {
                    $text = "The application did not finish processing prior to the timeout.";
                } else if ( $status < 300 ) {
                    $text = "The application returned an HTTP status code of 200 but no content.";
                } else if ( $status < 400 ) {
                    $text = "The application returned too many redirects.";
                } else if ( $status < 500 ) {
                    $text = "The application is configured to point to an incorrect page.";
                } else if ( $status < 600 ) {
                    $text = "The application encountered an error during processing.";
                }
            }
            
         } catch ( Exception $exception ) {
            error_log( "Remote Render Exception : " . $exception->getMessage() );
            error_log($exception->getTraceAsString());
            $this->error = RingsideSocialUtils::SOCIAL_ERROR_NO_SUCH_PAGE;
         }

      } else {
         
         // making a request to a local system app
         try {

            $apiSessionKey = RingsideSocialUtils::getApiSessionKey( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $socialClient->getNetworkSession() );
            $apiClientSocial = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $apiSessionKey );

            $callback = "System Application " . $this->canvasUrl;
            $fbmlText = $this->renderLocal( RingsideSocialConfig::$apiKey,  RingsideSocialConfig::$secretKey, $apiSessionKey, $socialClient );
            if  ( isset ( $coreApp->canvas_type ) && $coreApp->canvas_type == RingsideAppsCommon::CANVASTYPE_IFRAME ) {
               $text = $fbmlText;
            } else {
               if ( $socialClient->inSession() ) {
                  $apiSessionKey = RingsideSocialUtils::getApiSessionKey( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $socialClient->getNetworkSession() );
                  $apiClientSocial = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $apiSessionKey );
               }
               $text = $this->renderFbml( $fbmlText, $socialClient->getNetworkSession(), $apiClientSocial, $socialClient->getCurrentUser() );
            }
            
         } catch ( Exception $exception ) {
            error_log( "Remote Local Exception : " . $exception->getMessage() );
            error_log($exception->getTraceAsString());
            $this->error = RingsideSocialUtils::SOCIAL_ERROR_NO_SUCH_PAGE;
         }
      }

      $response = array();
      if ( !empty( $text ) ) $response['content'] = $text;
      if ( $this->iframe != null ) $response['iframe'] = $this->iframe;
      if ( $this->redirect != null ) $response['redirect'] = $this->redirect;
      if ( $this->error != null ) $response['error'] = $this->error;
      $response['status'] = $status;
      if ( empty( $response) ) { 
         $response['error'] = "The URL $callback returned no data";         
      }
      
      $response['raw'] = $this->raw;

      return $response;

   }

   public function renderFbml( $fbmlText ,  $socialSession, $apiClient, $appId = null ) {
       
      if ( !empty ( $fbmlText )) {

         try {

            $flavor_context = new RingsideSocialDslFlavorContext(); 
            $context = new RingsideSocialDslContext( $apiClient, $socialSession, $flavor_context, $appId );
            $flavor_context->startFlavor($this->flavor);

            $parser = new RingsideSocialDslParser( $context );

            $text = $parser->parseString( $fbmlText );
//				error_log("After rendering:");
//				error_log($text);
				

            $flavor_context->endFlavor($this->flavor);
            $this->redirect = $parser->getRedirect();
            return $text;

         } catch ( Exception $exception ) {
            $this->error = $exception->getMessage();
            error_log(  "Exception thrown during parsing page ($exception)" );
         }
      }

      return null;
       
   }

   public function renderRemote($callbackUrl, $apiKey, $secretKey, $canvasType, $isAppAdded, $sessionKey, RingsideSocialClientInterface $socialClient, &$headers, &$status ) {

//      error_log( "renderRemote : enter ($callbackUrl) ($apiKey)  " );
      $response = null;

      if ( !empty ( $this->path ) ) {
//         error_log( "renderRemote : path set" );
         $callbackUrl .= $this->path;
      }

      // Create openFB request.
      $ctx = new RingsideSocialAppContext();
      $ctx->setFlavor( $this->flavor );
      if($canvasType==RingsideSocialApiRender::CANVASTYPE_IFRAME||$canvasType==RingsideSocialApiRender::CANVASTYPE_OS){
      	$ctx->setIFrame( 1 );
      } else {
      	$ctx->setIFrame( 0 );
      }
      $ctx->setInCanvas( 1 );
      $ctx->setTime( time() );
      if ( $socialClient->inSession() ) { 
      	// We don't know whether the app is added unless the user is logged in, so don't send that part of the context
      	$ctx->setIsAppAdded( $isAppAdded );
      	$ctx->setUser( $socialClient->getCurrentUser() );
         $ctx->setSessionKey( $sessionKey );
//      $ctx->setProfileUpdateTime();
         $ctx->setExpires( 0 );
         if ( $socialClient->getNetworkSession()->getPrincipalId() ) {
            $ctx->setPrincipalId( $socialClient->getNetworkSession()->getPrincipalId() );
         }
      }
      $ctx->setApiKey( $apiKey );
      $ctx->setRequestMethod( $_SERVER['REQUEST_METHOD'] );
      $ctx->setNetworkId( $socialClient->getCurrentNetwork() );
//      $ctx->setDeployedNetwork( RingsideSocialConfig::$apiKey );
//      $ctx->setHostNetwork(RingsideSocialConfig::$apiKey);
      $ctx->setSocialSessionKey(  $socialClient->getNetworkSession()->getSessionKey() );
      $deployed_ctx = new RingsideSocialAppContext(array(), RingsideSocialConfig::$apiKey);
//      $deployed_ctx->setRestUrl(RingsideApiClientsConfig::$serverUrl);
//      $deployed_ctx->setLoginUrl(RingsideApiClientsConfig::$webUrl.'/login.php');
//      $deployed_ctx->setCanvasUrl(RingsideApiClientsConfig::$webUrl.'/canvas.php');
//      $ctx->addNetwork($deployed_ctx);
      $cbReq = $ctx->getParameters( $secretKey );
      
//      error_log(var_export($cbReq, true));
      
      /*
       * Special case if we are to return an IFRAME, then the only thing we are returning is the
       * URL to ship out.  It is up to the returning application to place this inside some form of content
       * frame.
       */
      if ( $this->flavor == 'canvas' && $canvasType == RingsideSocialApiRender::CANVASTYPE_IFRAME ) {

         $callbackQuery = http_build_query(array_merge( $cbReq, $this->params ));

         // TODO iframe generationg is off should be more expressive and configurable.
         $this->iframe = "$callbackUrl?$callbackQuery";
//         error_log( "renderRemote: iframe : " . $this->iframe );
          
      } else if ( $this->flavor == 'canvas' && $canvasType == RingsideSocialApiRender::CANVASTYPE_OS ) {
		//Open Social Gadget description is the $callbackUrl
         $callbackQuery = http_build_query(array_merge( $cbReq, $this->params ));
         
         // We also need to define fbsig_owner_id if the param id is present
         if(array_key_exists('id',$this->params)){
         	$callbackQuery.'&fb_sig_owner_id='.$this->params['id'];
         } 
         
         //TODO These parm options should be configurable
         $callbackQuery=$callbackQuery.'&view=canvas&synd=ringside&nocache=1';//If you change this you must change container.js
                  
         $this->iframe = RingsideApiClientsConfig::$socialUrl ."/gadgets/ifr?url=".urlencode($callbackUrl)."&$callbackQuery";
		 if(isset($this->params['forceIFrame']) && $this->params['forceIFrame'] =='true'){
		 	$headers['content-type']='text/html';
		 	
		 	$response="<iframe width='100%' frameborder='0' src='".$this->iframe."' height='".$this->params['forceIFrameHeight']."'/>";
		 }
         //         error_log( "renderRemote: OS iframe : " . $this->iframe );
          
      }  else {
         $response = RingsideSocialUtils::get_request( $callbackUrl, array_merge( $cbReq, $this->params ), $headers, $status );
         if ( isset($headers['location']) ) {
         	$this->redirect = $headers['location'];
         }
      }

      return $response;
   }

   /**
    * Will call  a local or core application without going through a remote http call.
    * The remainder of the call semantics are attempted to hold up.
    *
    * @param unknown_type $apiKey
    * @param unknown_type $apiSecret
    * @param unknown_type $socialClient
    * @return unknown
    */
   public function renderLocal($apiKey, $apiSecret, $sessionKey, $socialClient ) {

      // These are the contextual parameters passed to an application.
      // Create openFB request.
      $ctx = new RingsideSocialAppContext();
      $ctx->setFlavor( $this->flavor );
      $ctx->setIFrame( 0 );
      $ctx->setInCanvas( 1 );
      $ctx->setTime( time() );
      $ctx->setIsAppAdded( 1 );
      if ( $socialClient->inSession() ) { 
         $ctx->setUser( $socialClient->getCurrentUser() );
         $ctx->setSessionKey( $sessionKey );
         //      $ctx->setProfileUpdateTime();
         $ctx->setExpires( 0 );
         if ( $socialClient->getNetworkSession()->getPrincipalId() ) {
            $ctx->setPrincipalId( $socialClient->getNetworkSession()->getPrincipalId() );
         }
      }
      $ctx->setApiKey( $apiKey );
      $ctx->setRequestMethod( $_SERVER['REQUEST_METHOD'] );
      $ctx->setNetworkId( RingsideSocialConfig::$apiKey );
      $ctx->setSocialSessionKey(  $socialClient->getNetworkSession()->getSessionKey() );
      $cbReq = $ctx->getParameters( $apiSecret );
      
      foreach ( $cbReq as $k=>$v ) $this->params[$k]=$v;

      $text = '';
      ob_start();
      try {
      	if ( $this->path == '/' || $this->path == '' ) {
      		$this->path = 'index.php';
      	}
      	$loaded = $this->_load( 'apps.'.$this->canvasUrl, $this->path  );
      	
         if ( $loaded === false ) {
            $this->error = "No such application is available.";
            ob_end_clean();
         } else {
            $text = ob_get_clean();
            if ( empty ($text ) ){
               $this->error = "The application rendered an empty page.";
            }
         }
      } catch( Exception $exception) {
         ob_end_clean();
         $this->error = "Exception processing request.";
         error_log( "Exception processing request : $exception" );
      }


      foreach ( $cbReq as $k=>$v ) unset( $this->params[$k] );

      return $text;

   }
   
   private function _load($package, $file) {
      // lastLevel = error_reporting( E_ERROR );
      
      // save the first parameters after the ? from the path parameter into the $_GET variable
      // we only need to do this for the first assignment because everything afterwards
      // is somehow being handled by PHP
      // EXAMPLE: your URL looks like: render.php?method=app&app=adminHome&path=index.php?communities&partial
        // this code saves that communities variable inside of the $_GET
      if (strpos($file, '?') !== false) {
	      $vars = explode("?",$file);      
	      $assignment = explode("=",$vars[1]);
	      if($assignment[1])
	        $_GET[$assignment[0]] = $assignment[1];
	      else
	        $_GET[$assignment[0]] = "";
      	// remove any $_REQUEST vars so the file can be opened with the appropriate file name
      	$file = ereg_replace("\?.*$",'',$file);
      }
      
      $package = str_replace( '.', '/', $package );
      $old_request = &$_REQUEST;
      $_REQUEST = array_merge($_GET, $_POST);
      $result = include ( $package . '/' . $file );
      $_REQUEST = &$old_request;
      if ( $result === false ) {
         error_log( $package . '/' . $file . " not loaded " );
      }

//      error_reporting( $lastLevel );
      return $result;
   }
}


?>
