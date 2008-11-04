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

//require_once( 'ringside/api/OpenFBAPIException.php' );
//require_once( 'ringside/api/AbstractRest.php' );
//require_once( 'ringside/api/RequestContext.php' );
//require_once( 'ringside/api/facebook/OpenFBConstants.php' );
require_once( 'ringside/api/config/RingsideApiConfig.php' );
//require_once( 'ringside/api/Session.php' );

//require_once( 'ringside/m3/event/DispatcherFactory.php' );


/**
 * Implements the OpenFB auth.getSession API.
 *
 * @author Richard Friedman
 */
class OpenFBServer  {
    public static function errorHandler($errno, $errstr, $file, $line, $stack) {
        error_log("Unexpected error at $file:$line: ".var_export($stack));
                
        throw new Exception(
          $errstr,
          $errno,
          $file,
          $line,
          $stack
        );  
    }
    
   /**
    * Process a Request.
    *
    *
    * There are a few different type's of method calls currently.
    * 1. Auth related methods, which all start with "auth.".  These
    * methods do not yet typically have a session key or in process of creating/validating/removing one.
    * Other inner/inter systems security methods can be here as well.   Note, you should NOT add a method
    * to authenticate a user, that should be done else where.  Use the inner-method of approveToken and lock
    * it down within a system.
    *
    * 2. Application method calls.  This is really the catchalll and executes the request handling mechanism.
    *
    * 3. Systems Management calls.   (coming soon).
    *
    */
   function execute( $request ) {

      ini_set( 'session.use_cookies', '0' );
      ini_set('session.save_handler', 'user');
      	
      session_set_save_handler(array('Session', 'open'),
         array('Session', 'close'),
         array('Session', 'read'),
         array('Session', 'write'),
         array('Session', 'destroy'),
         array('Session', 'gc')
      );
      session_cache_limiter( 'none' );
      set_error_handler(array('OpenFBServer', 'errorHandler'), E_ERROR);
      // There is a change dependending on magic quotes settings
      // that PHP will add in extra slashes, not good for us. 
      // This is removed as of PHP 6 as well. 
      if( get_magic_quotes_gpc() ) {  
         foreach ($request as $rname => $rval) {
            $request[$rname] = stripslashes($rval);
         }
      }
      
      $context = Api_RequestContext::createRequestContext( $request );

      if ( $context->getNetworkKey() == null ) {
          $keyService = Api_Bo_KeyService::create();
          $ids = $keyService->getIds($context->getApiKey());
          $domain_keys = $keyService->getKeyset($ids['domain_id'], $ids['domain_id']);
          if ( $domain_keys != null ) {
              $context->setNetworkKey($domain_keys['api_key']);
          }
      }
      
      //error_log( "method $method requested" );
      try {
         $response = $this->executeRequest( $context, $request );
         $this->send_response( $context->getMethod(), $response, $context->getFormat(), $context->getCallback());
      } catch (Exception $exception) {
         error_log("When executing {$context->getMethod()} request in OpenFBServer: ".$exception->getMessage());
         error_log($exception->getTraceAsString());
         $this->send_exception( $exception, $request, $context->getFormat(), $context->getCallback());
      }

    	 // TODO - This would hurt infinite session concepts, should we just bag this concept?
    	 // Should session cache be extended after each call?
    	 // Should it be validated against expires time in session?
    	 //    	 session_cache_expire ( 24 * 60 );

   }

   /**
    * Execute the request and return the hash results, this
    * was separated soley for the purpose of running test cases.
    *
    * @param unknown_type $requestParams
    */
   function executeRequest( Api_RequestContext &$context ) {

      $response = array();
      
      if ( $context->getMethod() == null ) {
         throw new OpenFBAPIException( 'Incorrect Signature, Missing METHOD.', FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }
      
      // Call the object/method.
      $api_name = explode( '.', $context->getMethod() );
      $api_pkg = $api_name[1];
      $api_class = ucfirst($api_name[1]) . ucfirst($api_name[2]);

      $lasterrorlevel = error_reporting(E_ERROR);
      if ( ! include_once( $api_name[0].'/rest/'.$api_class.'.php' ) ) {
      	// TODO: Move these to match the packaging standard
      	// Default OpenFBServer API implementations are still here
      	
      	include_once( 'ringside/api/facebook/' . $api_class . '.php' );
      }
      error_reporting($lasterrorlevel);

      if ( ! $api_class ) {
      	throw new Exception("Class $api_class could not be loaded");
      }

      $faf = new $api_class();

      // Set the server object.
      $faf->_setServer( $this );
      
      // set the context
      $faf->_setContext( $context );
      
      // Load the session and setup the session.
      $faf->loadSession ( );      
      
      // Execute delegation?
      $faf->delegateRequest();
      
      // Validation steps
      $faf->validateSession();
      $faf->validateApiKey();
      $faf->validateSig( );
      $faf->validateVersion( );
      $faf->validateCallId( );
      $faf->validateRequest( );
      
      // let's invoke the API that is being requested - collect stat around the call
      $tuple = new M3_Event_Tuple( $faf->getNetworkId(), $faf->getAppId(), $faf->getUserId() );
      $dispatcher = M3_Event_DispatcherFactory::createApiResponseTimeTupleDispatcher($context->getMethod(), $tuple);
      $dispatcher->startTimer();
      
      $response = $faf->execute( );
      
      $dispatcher->stopTimer(); // emit event
      
      return $response;
   }
    
   function send_exception( $exception, $request, $format, $callback ) {

      $response = array( 'error_code'=>$exception->getCode(),
    	   				 'error_msg'=>$exception->getMessage());
    	                 //'error_trace'=>$exception->getTraceAsString() );

      $args = array();
      foreach ( $request as $key=>$value ) {
         $args[] = array ( "key"=>$key, "value"=>$value );
      }
      $response['request_args'] = array("arg"=>$args);

      $this->send_response( 'error', $response, $format, $callback );
   }
   
   /**
    * Prescribed mechanism for sending/formatting REST response.
    * XML Response needs work for custom XML returns.
    *
    * @param unknown_type $method
    * @param unknown_type $response
    */
   function send_response ( $method, $response, $format, $callback )  {
       
      // based on format respond back
      if ( $format == 'json' ) {
      
      	if ( $callback != null )  print "$callback(";
      
      	$crest = $response;
         if (is_array($response)) $crest = current($response);
         
         if ((count($response) == 1) && !is_array($crest)) {
          	print "\"$crest\"";
         } else {         	
         	self::printJSON($response);
         }
         
         if ( $callback != null )  print ");";
          
      } else if ( $format == 'php' ) {
         print_r( $response );
      } else if ( $format == 'html' ) {
         echo 'Method Response : <b> '.str_replace( '.', '_', $method).'</b><br />';
         if ( count($response) == 1 && !is_array( current($response) ) ) {
            echo current($response);
         } else {
            array_walk( $response, array('OpenFBServer','printHTML'), '' );
         }
         echo 'End of response <b>'.str_replace( '.', '_', $method).'</b><br />';
      } else if ( $callback != null ) {
      	echo $callback . "('" . addslashes('<?xml version="1.0" encoding="UTF-8"?>');
         $api_name = explode( '.', $method );
         $methodResponse = ((sizeof($api_name) == 3)?$api_name[1].'_'.$api_name[2]:$method).'_response';
                  
         echo addslashes( '<'.$methodResponse.'>' );
          
         if ( count($response) == 1 && !is_array( current($response) ) ) {
            echo addslashes(current($response));
         } else {
            array_walk( $response, array('OpenFBServer','printXML'), '' );
         }
          
         echo addslashes( '</' . $methodResponse . '>');
      } else {
         $api_name = explode( '.', $method );
         $methodResponse = ((sizeof($api_name) == 3)?$api_name[1].'_'.$api_name[2]:$method).'_response';
         echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
         
         $crest = $response;
         if (is_array($response)) $crest = current($response);
         
         if ( count($response) == 1 && !is_array($crest) ) {         
            $result = $crest;
         	echo "<$methodResponse>$result</$methodResponse>";
         } else if ( count($response) == 1 ) {
            $result = current($response);
            $list = '';
            if ( isset( $result[0]) ){
               $list = " list=\"true\"";         
            }
         	echo "<$methodResponse" . "$list>";
            array_walk( $response, array('OpenFBServer','printXML'), '' );
         	echo "</$methodResponse>";
         } else {
         	echo "<$methodResponse>";
            array_walk( $response, array('OpenFBServer','printXML'), '' );
         	echo "</$methodResponse>";
         }
      }
   }
   
   function printJSON($arr)
   {
   	if (!is_array($arr)) {
   		if (!is_int($arr)) print "\"";
   		print $arr;
   		if (!is_int($arr)) print "\"";
   		return; 
   	}
    	$pbrak = true;
    	if ((count($arr) == 1) && !self::is_assoc(current($arr))) $pbrak = false;
    	if ($pbrak) print "{";
    	
    	$first = true;
    	foreach ($arr as $key => $val) {
        	if (!$first) print ",";
        	else $first = false;
        	    	
        	if (!is_array($val)) {
        		print "\"$key\":";
        		if (!is_int($val)) print "\"";
        		print $val;
        		if (!is_int($val)) print "\"";
        	} else {    		    		    		
        		if (self::is_assoc($val)) {
        			print "\"$key\":";
        			self::printJSON($val);
        		} else {
        			$first2 = true;
        			print "[";
        			foreach ($val as $val2) {
        				if (!$first2) print ",";
        				else $first2 = false;
        				self::printJSON($val2);
        			}    			
        			print "]";
        		}
        	}	
    	}	
    	if ($pbrak) print "}";
	}
	
	//brute force method to check whether
   //an array is associativae
   function is_assoc($arr)
   {
    	foreach ($arr as $key => $val) {
    		if (!is_int($key)) return true;
    	}
    	return false;
   }
    	
   
   
   function printHTML( $value, $key, $parent_key) {
      if ( is_int( $key ) ) {
         $key  = $parent_key ;
      }

      if ( empty( $value ) ) {
         echo "Key is Empty : <b>$key</b><br>";
      } else if ( is_array( $value ) ) {
         if ( !is_int( key( $value ) ) ) {
            echo "$key is array : <br /><table border='1'><tr><td>";
            array_walk($value, array('OpenFBServer','printHTML'), $key );
            echo "</td></tr></table> end of $key array<br />";
         } else {
            array_walk($value, array('OpenFBServer','printHTML'), $key );
         }
      } else {
         echo "$key : $value<br/>";
      }
   }
    
   /**
    * Helper function to print array as XML.
    * [TODO] Support arrays of arrays.
    * TODO Support encoding for callback
    *
    * @param unknown_type $value
    * @param unknown_type $key
    */
   static function printXML( $value, $key, $parent_key  ) {
      if ( is_int( $key ) ) {
         $key  = $parent_key ;
      }

      if ( is_array( $value ) ) {
         if ( !is_int( key( $value ) ) ) {
            $list = '';
            if ( count($value) == 1 ) {
               $current = current($value);
               if ( is_array($current) && isset( $current[0] ) ) {
                 $list = " list=\"true\"";
               }
            }
            echo "<$key"."$list>". "\n";
            array_walk($value, array('OpenFBServer','printXML'), $key  );
            echo  "</$key>". "\n";
         } else {
            array_walk($value, array('OpenFBServer','printXML'), $key );
         }
      } else if ( strlen($value) == 0  ) {
         echo   "<$key />". "\n";
      } else {
         //echo "<$key>" . htmlentities($value) . "</$key>". "\n";
         echo "<$key>" . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . "</$key>". "\n";
      }
   }

}

?>