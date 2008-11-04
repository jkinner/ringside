<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style>
table#pricing-plans th, td{
  background:#efefef;
  padding:3px;
}
</style>
</head>
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


require_once('ringside/web/RingsideWebUtils.php');
require_once('ringside/web/config/RingsideWebConfig.php');
require_once('ringside/api/clients/RingsideApiClients.php');
require_once('ringside/api/db/RingsideApiDbDatabase.php');

function getRequestParam($name, $default = NULL)
{
    if (isset($_REQUEST[$name])) {      
        return $_REQUEST[$name];
    }
    return $default;
}

function getCcTypeString() {
	$cctype = getRequestParam( 'cctype' );
    if( $cctype == '100' ) {
        return 'Visa';
    }
    else if( $cctype == '101' ) {
        return 'MasterCard';
    }
    else if( $cctype == '102' ) {
        return  'Amex';
    }
    else if( $cctype == '103' ) {
        return 'Discover';
    }
    else {
        return 'Other';
    }
}

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey );
$client->setLocalClient( true );
$uid = $client->require_login();

?>
