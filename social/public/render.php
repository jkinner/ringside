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

require_once( "LocalSettings.php");
require_once( "ringside/social/RingsideSocialServerRender.php");
require_once( 'ringside/api/Session.php' );

$override_from_query_string = array(
    // Allow this to be overridden for Site Connect sites
    'social_session_key'
);

// TODO: This requires the API and Social tiers to be co-located (or to have a shared session repository)
  session_set_save_handler(array('Session', 'open'),
     array('Session', 'close'),
     array('Session', 'read'),
     array('Session', 'write'),
     array('Session', 'destroy'),
     array('Session', 'gc')
  );
  session_cache_limiter( 'none' );

	$server = new RingsideSocialServerRender( );
	if(isset($_REQUEST['format']) && $_REQUEST['format']=='JSON'){
		// The widget requires JSON for cross domain support
		$json_response=null;
		$responseHTML = '';
		try {
			error_log("fbml render request=".$_REQUEST['fbml']);
			$fbmlReq=$_REQUEST['fbml'];
			$responseHTML=$server->execute( $_REQUEST );
		} catch (Exception $e){
			$json_response=json_encode(array('response'=>'error','widgetid'=>$_REQUEST['widgetid'],
				'message'=>$e->getMessage(),'code'=>$e->getCode(),	
				'file'=>$e->getFile(),'line'=>$e->getLine(),
				));
			error_log($e->getTraceAsString());
		}
		if(is_null($json_response)){
			if(array_key_exists('widgetid',$_REQUEST)){
				$response['widgetid']=$_REQUEST['widgetid'];
			}
			$response['response']="success";
			if($_REQUEST['method']=='app'){

				$decodedResponse=json_decode ( $responseHTML, true );

				if(!isset($decodedResponse) || strlen($decodedResponse) == 0)
				{
					$response['content']=$responseHTML;
				}else
				{
					$response['content']=$decodedResponse;
				}
			} else {
				$response['content']=$responseHTML;
			}
			$response['fbml']=$fbmlReq;
			$json_response=json_encode($response);
		}
		if(array_key_exists('callback',$_REQUEST)){
			// JSONP encode
			$json_response=$_REQUEST['callback']."(".$json_response.");";
		}
		error_log("JSON RESP:". $json_response);
		print($json_response);
	} else {
	    $matches = array();
	    if ( preg_match(',^/([^/]*)/([^/]*)/(.*)$,', $_SERVER['PATH_INFO'], $matches) ) {
	        error_log("Matched PATH_INFO ".join(',', $matches));
    	    $params['method'] = 'app';
//    	    $params['forceIFrame'] = 'true';
    	    $params['social_session_key'] = $matches[1];
    	    $params['api_key'] = $matches[2];
    	    $params['path'] = $matches[3];
    	    $params['resizeUrl'] = $_REQUEST['resizeUrl'];
	    } else {
	    	error_log("Failed to match PATH_INFO ".$_SERVER['PATH_INFO']);
	        $params = $_REQUEST;
	    }
	    foreach ( $override_from_query_string as $override ) {
	        if ( isset($_GET[$override]) ) {
	            $params[$override] = $_GET[$override];
	        } else if ( isset($_POST[$override]) ) {
	            $params[$override] = $_POST[$override];
	        }
	    }
		print($server->execute( $params ));
	}
?>
