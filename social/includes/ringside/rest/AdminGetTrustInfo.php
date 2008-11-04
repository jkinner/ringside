<?php
 /*
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
  */

/**
 * Retrieves the known trust authorities.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
require_once('ringside/api/DefaultRest.php');
require_once('ringside/social/db/RingsideSocialDbTrust.php');

class AdminGetTrustInfo extends Api_DefaultRest {
	private $authorities = null;
	
	public function validateRequest( ) {
//	   error_log( "API PARAMS . " . var_export( $apiParams, true ) );
		
	   $tids = $this->getApiParam('tids' );
	   if ( ! $this->isEmpty($tids) ) {
			$this->authorities = explode(',', $tids );
		}
	}
	
	public function execute() {
		$result = array();
		
		$networks = RingsideSocialDbTrust::getTrustAuthorities($this->authorities);
		if ( empty ( $this->authorities )) {
		   foreach( $networks as $network ) {
		      $result['trust_auth'][] = array(
				'trust_key'		=> 	$network['trust_key'],
				'trust_name'	=> 	$network['trust_name'],
				'trust_auth_url'	=>	$network['trust_auth_url'],
				'trust_login_url'	=>	$network['trust_login_url'],
				'trust_canvas_url'	=>	$network['trust_canvas_url'],
				'trust_social_url'	=>	$network['trust_social_url'],
				'trust_postmap_url'	=>	$network['trust_postmap_url'],
		   		'trust_web_url'	=>	$network['trust_web_url']
		      );
		   }
		} else { 
      		foreach( $this->authorities as $key ) {
      		   $authority = $networks[$key];
      		   if ( !empty( $authority ) ) {      
      		      $result['trust_auth'][] = array(
      				'trust_key'		=> 	$authority['trust_key'],
      				'trust_name'	=> 	$authority['trust_name'],
      				'trust_auth_url'	=>	$authority['trust_auth_url'],
      				'trust_login_url'	=>	$authority['trust_login_url'],
      				'trust_canvas_url'	=>	$authority['trust_canvas_url'],
      				'trust_social_url'	=>	$authority['trust_social_url'],
      				'trust_postmap_url'	=>	$authority['trust_postmap_url'],
         			'trust_web_url'	=>	$authority['trust_web_url']
      		      );
      		   }
      		}
		}
		
		return $result;
	}
}
?>