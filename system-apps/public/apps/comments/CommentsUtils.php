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
require_once( 'ringside/api/clients/RingsideApiClientsRest.php');
require_once( 'ringside/api/clients/RingsideApiClientsConfig.php');
require_once( 'ringside/core/client/RingsideRestClient.php');
require_once( 'ringside/social/config/RingsideSocialConfig.php');

class CommentsUtils {
	
	function hasAddParams( ) {
		return ( !empty( $_GET['xid_action'] ) && $_GET['xid_action'] == 'post' 
			&& !empty( $_GET['xid'] ) && !empty( $_GET['text'] ) && !empty( $_GET['aid'] ) ) ? true : false;
	}
	
	function hasDeleteParams( ) {
		return ( !empty( $_GET['xid_action'] ) && $_GET['xid_action'] == 'delete' 
			&& !empty( $_GET['xid'] ) && !empty( $_GET['cid'] ) 
			&& !empty( $_GET['aid'] ) ) ? true : false;
	}
	
	function hasDisplayParams( ) {
		return ( !empty( $_GET['xid'] ) && !empty( $_GET['aid'] ) && empty( $_GET['xid_action'] ) ) ? true : false;
	}
	
	function selfReferred( ) {
	   
		if( strpos( $_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'] ) === false ) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function isAuthorized( ) {
		$params = array();
   		$params['xid'] = $_GET['xid'];
   		if ( !empty( $callbackurl ) )  {
   			$params['c_url'] = $_GET['callbackurl'];
   		}
   		if ( !empty( $returnurl ) )  {
   			$params['r_url'] = $_GET['returnurl'];
   		}
   		$params['aid'] = $_GET['aid'];
   		$params['sig'] = RingsideSocialUtils::makeSig( $params, RingsideSocialConfig::$secretKey );
//   		print 'secret: ' . RingsideSocialConfig::$secretKey . '<br />';
//   		print_r( $params );
//   		print '<br />received sig: ' . $_GET['sig'] . '<br />';
//   		print 'generated sig: ' . $params['sig'] . '<br />';
   		return ( $params['sig'] == $_GET['sig'] );
	}
}
?>
