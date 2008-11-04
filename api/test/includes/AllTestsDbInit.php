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

/*
 * This file is used to insert test data into the DB
 * from the command line, by changing to this directory
 * and running "php -f AllTestsDbInit.php".
 */

    $includesRoot = dirname(__FILE__);
	$apiRoot =  $includesRoot . "/../..";
	$ipath = get_include_path() . PATH_SEPARATOR . "$apiRoot/includes" .
				PATH_SEPARATOR . $includesRoot . PATH_SEPARATOR . 
				"$includesRoot/sql";
	echo "include_path='$ipath'";
	echo "\n\n";
	set_include_path($ipath);
	include_once('SuiteTestUtils.php');
	
   require_once 'sql/AllAPITests-teardown.sql';
   require_once 'sql/AllAPITests-setup.sql';
   
   echo "done.";
   echo "\n\n";

?>
