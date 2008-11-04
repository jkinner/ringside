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
	$thisdir = dirname(__FILE__);

	if ( sizeof($_SERVER['argv']) < 2 ) {
   	 die("Usage: ".basename(__FILE__)." class_name[.php] file_name\n");
   }

	
   $file = $_SERVER['argv'][2];

   if ( strstr( $file, basename(__FILE__)) ) {
   	die("If in Eclipse, you must select the test case PHP file as the resource before running this script.\n"); 
   }
   
   $file_in_thisdir = strpos($file, $thisdir);
   if ( $file_in_thisdir === false || $file_in_thisdir !== 0 ) {
		die("This script MUST be run on a test file in the Ringside project structure ($file does not appear inside $thisdir)\n");
	}
   	
   $first_slash_after_pos = strpos($file, '/', strlen($thisdir)+1);
   $final_length = strlen($file)-strlen($thisdir)-$first_slash_after_pos;
   $dir = './'.substr($file,strlen($thisdir)+1,$final_length);

   $matches = array();
   $preg_query = ','.$thisdir.'/([^/]*)/test/([^/]*),';
   preg_match($preg_query,$file,$matches);
   $dir = $thisdir.'/'.$matches[1];
   $test_type =$matches[2];
   
   preg_match($preg_query, $file, $matches);
   echo "Running $test_type test $file in directory $dir\n";
	set_include_path(
		$dir.PATH_SEPARATOR .
		$dir.'/includes'.PATH_SEPARATOR.
		$dir.'/test/'.$test_type.PATH_SEPARATOR .
		$dir.'/test/includes'.PATH_SEPARATOR .
		$dir.'/test/'.$test_type.'/conf'.PATH_SEPARATOR .
		$dir.'/../api/clients/php'.PATH_SEPARATOR .
		$dir.'/../social/clients/php'.PATH_SEPARATOR .
		// For configuration...
		$dir.'/../web/includes'.PATH_SEPARATOR .
		$dir.'/../social/includes'.PATH_SEPARATOR .
		// TODO: This should not have to be here; part of refactoring into common project
		$dir.'/../api/includes'.PATH_SEPARATOR .
		$dir.'/../api/test/includes'.PATH_SEPARATOR .
		$dir.'/../api/test/functional'.PATH_SEPARATOR .
		// End refactoring
		$dir.'/../m3/clients/php'.PATH_SEPARATOR .
		$dir.'/../m3/includes'.PATH_SEPARATOR .
		get_include_path()
	);
	echo "Include path is ".get_include_path()."\n";
	
   include 'PHPUnit/TextUI/Command.php';

   
?>
