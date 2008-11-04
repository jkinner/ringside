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

/**
 * The error application assists in formatting back out error messages.
 * The current implementation is simple but can be expanded to understand network and user.
 */

define( "ERROR_DEFAULT", 'Unknown Error occured.');
define( "ERROR_MESSAGE", 'If you would like to report this error please submit <a href="report.php">error report</a>.');

$error = isset($_GET['social.error']) ?$_GET['social.error'] : null;
if ( $error == null ) {
   $error = isset($_POST['social.error']) ?$_POST['social.error'] : null;
}
if ( $error == null ) {
   $error = isset($_REQUEST['social.error']) ?$_REQUEST['social.error'] : null;
}
if ( $error == null ) {
   $error = ERROR_DEFAULT;
}

$file = 'apps/error/canvas.php';
$result = include ( $file  );

?>
