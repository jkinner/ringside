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

require_once( 'ringside/web/RingsideWebUtils.php');
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/session/RingsideWebSession.php' );
require_once( 'ringside/social/client/RingsideSocialClientLocal.php' );

/**
 * This is SOCIAL render integration page. 
 * This enables a third party site to point to this page and 
 * get back an application. 
 * 
 * To render through we need to know which network is making the request. 
 * NETWORK: 
 * AUTHENTICATED USER: 
 * 
 */

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );
$inSession = $social->inSession();

$pathInfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '' ;
$canvas = '';
$trailingSlash = false;

if ( !empty( $pathInfo ) ) {
   if ( $str[strlen($str)-1] == "/" ) { 
      $trailingSlash = true;
   }
   
   $pathInfo = ltrim( $pathInfo, "/" );
   
   $path_parts = explode( '/', trim($pathInfo), 2 );
   $canvas = $path_parts[0];
   if ( isset ($path_parts[1]) ) {
      $pathInfo = $path_parts[1];
   } else {
      $pathInfo = '';
   }

}
 
if ( $inSession === false ) {
   
   if ( !empty( $canvas ) && stristr( $canvas, "register") ) {
      $canvas = 'register';
      $pathInfo = '';
   } else {
      
      if ( !isset( $_REQUEST['next']) ) {
         if ( $canvas == "login" ) {  
            $_REQUEST['next']=$pathInfo;
         } else {
            if ( $trailingSlash === true ) { 
               $_REQUEST['next']="../$canvas/$pathInfo";               
            } else {
               $_REQUEST['next']="$canvas/$pathInfo";
            }
            
         }
      }
      
      $canvas = "login";
      $pathInfo = '';
         
   }
   
} else if ( strcasecmp($canvas, "logout" ) == 0  || strcasecmp($canvas, "logoff" ) == 0 ) {

   $social->clearSession();
   $webSession->clearSession();
   session_destroy();

   if ( $trailingSlash === true ) { 
      $canvas = "../login";
   } else {
      $canvas = "login";
   }
   RingsideWebUtils::redirect(RingsideWebConfig::$webRoot. '/ringside.php/'. $canvas);
   
} else if ( empty( $canvas ) || strlen( trim($canvas)) <  2 ) {
      $canvas = 'welcome';
      $pathInfo = '';
}
   
   try {
      $text = $social->render( 'canvas', null, $canvas, $pathInfo  );

   } catch ( Exception $exception ) {
      
      error_log( 'Getting application page failed. $exception' );
   
   }
   
   if ( $social->getRedirect() != null ) {
     	RingsideWebUtils::redirect($social->getRedirect()); 
   } else if ( $social->isRaw() ) {
     	echo $text;
   } else {
?><html>
<head>
<link rel="stylesheet" href="<?php echo RingsideWebConfig::$webRoot ?>/css.php/ringside.css" type="text/css" />
</head>
<body>

<div style="background-color: white; color: #222; width: 711px; padding: 4px"><?php
   if ( $social->getError() != null ) {

      if ( $_SERVER['REQUEST_METHOD'] =='POST' ) {
         $_POST['social.error'] = $social->getError();
      } else {
         $_GET['social.error'] = $social->getError();
      }

      echo $social->render( 'canvas', null, 'error', '' );
   } else {
      echo $text;
   }
?>

<div class="footer embed"><a style="float: left; display: block;" href="http://www.ringsideneteworks.com">Powered by Ringside Networks</a>
<?php
if ( $inSession ) {
 ?>
<a style="float: right; display: block;" href="<?php echo RingsideWebConfig::$webRoot ?>/ringside.php/logoff">(Logout)</a>
<?php
} else {
 ?>
<a style="float: right; display: block;" href="<?php echo RingsideWebConfig::$webRoot ?>/ringside.php/login">(Login)</a>
 <?php
}
  ?></div></div>
<?php
}
 ?>
