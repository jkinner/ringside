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

require_once('ringside/api/db/RingsideApiDbDatabase.php');
include_once("ringside/api/config/RingsideApiConfig.php");

define( 'RS_TRUST_AUTHORITIES_TABLE', 'rs_trust_authorities' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_KEY', 'trust_key' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_NAME', 'trust_name' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_AUTH_URL', 'trust_auth_url' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_LOGIN_URL', 'trust_login_url' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_CANVAS_URL', 'trust_canvas_url' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_WEB_URL', 'trust_web_url' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_SOCIAL_URL', 'trust_social_url' );
define( 'RS_TRUST_AUTHORITIES_COL_TRUST_POSTMAP_URL', 'trust_postmap_url' );

/**
 * Interface to the configured networks and trust authorities.
 * 
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsideSocialDbTrust 
{
	/**
	 * Constructs a principal object
	 *
	 * @param int $uid
	 * @param string $network_key
	 * @param string $user_name
	 * @return unknown
	 */
	public static function getTrustAuthorities($tids = null)
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();

		$sql = 'SELECT * FROM ' . RS_TRUST_AUTHORITIES_TABLE;
		if ( !empty($tids) ) {
			$tid_list = array();
			foreach ( $tids as $tid ) {
				$tid_list[] = "'".mysql_real_escape_string($tid)."'"; 
			}
			$sql .= ' WHERE '. RS_TRUST_AUTHORITIES_COL_TRUST_KEY .' in (' . implode(',', $tid_list) . ')';
		} 

		$result = mysql_query( $sql, $dbCon );
		if ( mysql_errno($dbCon) > 0 ) {
			throw new Exception( mysql_error(), mysql_errno() );
		}

		$results = array();
		while ( $row = mysql_fetch_assoc( $result ) ) {
		   $results[$row[RS_TRUST_AUTHORITIES_COL_TRUST_KEY]] = $row;
		}
		
		return $results;
	}
}
?>
