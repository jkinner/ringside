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

// Need the local client configuration
include_once( 'LocalSettings.php');
require_once( 'ringside/api/clients/RingsideApiClientsRest.php');
require_once( 'ringside/social/config/RingsideSocialConfig.php');

/*
 * Only root files are loaded via web server, hence
 * you can count on that being the a root directory.
 * And therefore the parent directory as the root of
 * social and/or web
 */

class RingsideSocialUtils {
   const SOCIAL_ERROR_NO_SUCH_PAGE = "The page you requested was not found";
	const SOCIAL_ERROR_TRUST_EXCEPTION = "Exception creating trust relationship";
	const SOCIAL_ERROR_RENDER_EXCEPTION = "Exception rendering fbml!";
	 
	// TODO: Add this trust key to the DB
	const DEFAULT_TRUST_KEY = 'Ringside';
	const DEFAULT_NETWORK_KEY = 'Ringside_Network';
	
	
   /**
    * This shoudl be a system wide method or an option in PHP. 
    *
    * @param array $array
    * @param string $key
    * @return $array[$key] or null 
    */
   public static function get( $array, $key ) { 
      return isset( $array[$key] ) ? $array[$key] : null;
   }
   
   public static function get_request( $server, $params, &$headers, &$status ) {

      $post_string = http_build_query( $params, '', '&' );
      $result = null;

//      error_log("Posting social request $post_string");
      if (function_exists('curl_init')) {
         // Use CURL if installed...
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_URL, $server );
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_USERAGENT, 'Ringside.API Client (curl) ' . phpversion());
         curl_setopt($ch, CURLOPT_HEADER, true);
         $result = curl_exec($ch);
         $headersize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
         $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         error_log("CURL status for $server is $status");
         $http_headers = substr($result, 0, $headersize-1);
         $result = substr($result, $headersize);
         if ( $headers !== null ) {
         	$parsed_headers = RingsideSocialUtils::parse_headers($http_headers);
//         	error_log("Render headers are");
//         	error_log(var_export($parsed_headers, true));
         	foreach ( $parsed_headers as $http_header => $value ) {
         		$headers[$http_header] = $value;
         	}
         }
         curl_close($ch);
      } else {
         // Non-CURL based version...
         $context =
         array('http' =>
         array('method' => 'POST',
                          'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                      'User-Agent: OpenFB Client (non-curl) '.phpversion()."\r\n".
                                      'Content-length: ' . strlen($post_string),
                          'content' => $post_string));
         $contextid=stream_context_create($context);
         $sock=fopen($server, 'r', false, $contextid);
         if ($sock) {
            $result='';
            while (!feof($sock))
            $result.=fgets($sock, 4096);

            fclose($sock);
         }
      }

      return $result;

   }


   /**
    * Create a MD5 Signature for the request call.
    * TODO move this to a commons.
    *
    * @param array $request
    * @param string $secret
    * @return md5 hash result
    */
   public static function makeSig( $request, $secret, $namespace = null) {

   	$sig_params = array();
   	if( $namespace != null ) {
   		$prefix = $namespace . '_';
   		$prefix_len = strlen($prefix);
   		foreach ($request as $name => $val) {
   			if (strpos($name, $prefix) === 0) {
   				$sig_params[substr($name, $prefix_len)] = get_magic_quotes_gpc() ? stripslashes($val) : $val;
   			}
   		}
   	} else {
   		foreach ($request as $name => $val) {
   			$sig_params[$name] = get_magic_quotes_gpc() ? stripslashes($val) : $val;
   		}
   	}

      ksort($sig_params);

      $str='';
      foreach ($sig_params as $k=>$v) {
         if ( $k != 'sig' ){
            $str .= "$k=$v";
         }
      }
      $str .= $secret;
      $md5sig = md5($str);

      return $md5sig;
   }
   
   public static function getAdminClient( $snid = null ) {
      
         try {
            if ( $snid == null ) {
               $snid = RingsideSocialConfig::$apiKey;
            }
            
            // Configure where we get the URL for the REST SERVER from.
            $apiClient = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, null, null, $snid  );
//            $apiClient->auth_createAppSession( -1, RingsideSocialConfig::$apiKey, false  );
            return $apiClient;
         } catch ( Exception $exception ) {
            error_log( "Error creating session key " . $exception );
            error_log($exception->getTraceAsString());
            return null;
         }
      
   }
   
   /**
    * Get the session key between an application and the API server. 
    *
    * @param unknown_type $api_key
    * @param unknown_type $secret_key
    * @param RingsideSocialSession $socialSession
    * @return string session key for the API container
    */
   public static function getApiSessionKey ( $api_key, $secret_key, RingsideSocialSession $socialSession  ) {

      $uid = $socialSession->getUserId();
      $sessionKey = $socialSession->getApiSessionKey( $api_key );

      if ( $sessionKey != null ) {

         // Validate Session Key is still valid. 
         $apiClient = new RingsideApiClientsRest( $api_key, $secret_key, $sessionKey );
         $apiClient->setNetworkKey($socialSession->getNetwork());
         try {
            $apiClient->users_getLoggedInUser();
         } catch ( Exception $e ) {
//            error_log( "Session expired? " . $e->getMessage() ) ;
//            error_log($e->getTraceAsString());
            $sessionKey = null;
            $socialSession->unsetApiSessionKey($api_key);
         }
      }

      if ( $sessionKey == null && $uid != null ) {
      	
         // Need to simulate being app and auth, approve, get... which of course
         // TODO we need to re-think once we are working.
         // TODO catch some exceptions.
         try {
            // Configure where we get the URL for the REST SERVER from.
            $apiClient = new RingsideApiClientsRest($api_key, $secret_key, null, null, RingsideSocialConfig::$apiKey );
            // Once the client is authenticated with a session, the network key will be associated via the session
            $apiClient->setNetworkKey($socialSession->getNetwork());
            $auth_token = $apiClient->auth_createToken($socialSession->getExpiry()==null?true:false);
            $result = $apiClient->auth_approveToken( $uid );
            $result = $apiClient->auth_getSession($auth_token);
            if ( !empty( $apiClient->session_key ) ) {
               $sessionKey = trim($apiClient->session_key);
               $socialSession->addApiSessionKey( $api_key, $sessionKey );
            }
         } catch ( Exception $exception ) {
            error_log( "Error creating session key " . $exception );
         }
      }

      return $sessionKey;

   }

	public static function parse_headers($headers) {
		$header_array = array();
		$lines = split("\n", $headers);
		$header_name = '';
		$header_value = '';
		foreach ( $lines as $line ) {
			if ( preg_match("/^[ \t]/", $line) ) {
				if ( $header_name ) {
					$header_value .= $line;
				}
			} else {
				if ( $header_name && $header_value ) {
					$store_value = trim(preg_replace("/[ \t][ \t]*/", " ", $header_value));
					if ( isset($header_array[$header_name]) ) {
						if ( is_array($header_array[$header_name]) ) {
							$header_array[$header_name][] = $store_value;
						} else {
							$header_array[$header_name] = array( $header_array[$header_name], $store_value);
						}
					} else {
						$header_array[$header_name] = $store_value;
					}
				}
				$header_name = strtolower(substr($line, 0, strpos($line, ':') ));
				$header_value = substr($line, strpos($line, ':') + 1);
			}
		}
		if ( $header_name && $header_value ) {
			$header_array[$header_name][] = trim(preg_replace("/[ \t][ \t]*/", " ", $header_value));
		}
		return $header_array;
	}
}
 
?>
