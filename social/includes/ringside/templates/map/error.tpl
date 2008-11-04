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
 * Implements the UI for performing identity mapping.
 *
 * Parameters expected to be passed in 
 * 
 * method = bindmap  (represents action of mapping)
 * next = 'URL to be followed through once mapping is complete'
 * sid = "100000" - User id of the user on the network they called from.  
 * snid = 'Social Network ID' - The ID of the network which user came through 
 * api_key = "APIKEY of application requesting identity mapping"
 * sig = "" 
 * social_session_key = "Represents session between network and user. " 
 * session_key = "41f52ee85f4709fe2c743c85a8931974-ringside" 
 * canvas = "true|false" - is this a canvas application
 * 
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
 
require_once('ringside/social/config/RingsideSocialConfig.php');
?>

<html>
<head>
	<title>ringside :: user identity</title>
	<link rel="stylesheet" type="text/css" href="<?php echo RingsideSocialConfig::$webRoot ?>/css.php/ringside.css" />
</head>
<body>
<div class="ringside">
<div class="canvas" style="min-height: 200px">
<h2>Identity Mapping Error</h2>
<p>
An error has occurred when processing your identity mapping. You may need to contact the operator, or click your browser's back button.
</p>
</div>
</body>
</html>