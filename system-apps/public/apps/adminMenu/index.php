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
require_once('ringside/social/client/RingsideSocialClientLocal.php');
require_once('ringside/web/config/RingsideWebConfig.php');
require_once('ringside/web/session/RingsideWebSession.php' );
$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );
?>
<div id="widebar-header">
    <div id="global-links">
        <ul>
            <li><a href="<?php echo RingsideWebConfig::$webRoot ?>/index.php">Home</a></li>
            <li><?php
if ( $social->inSession() ) {
    ?><a href="<?php echo RingsideWebConfig::$webRoot ?>/logoff.php">Logout</a><?php
} else {
    ?><a href="<?php echo RingsideWebConfig::$webRoot ?>/login.php">Login</a><?php
} ?>
            </li>
        </ul>
    </div>
    <div id="nav-top">
        <ul>
            <li><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php">Home</a></li>
            <li><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php?apps">Applications</a></li>
            <li><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php?communities">Communities</a></li>
        </ul>
    </div>
</div>  
