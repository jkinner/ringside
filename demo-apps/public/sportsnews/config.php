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

// app settings
class Config
{
	public static $api_key = 'sportsnewskey';
	public static $secret  = 'sportsnewssecret';
	
	// Configure Database
	public static $db_ip = 'localhost:3306';    
	public static $db_name = 'ringside_db';       
	public static $db_user = 'root';
	public static $db_pass = 'ringside';
	
	public static $ringsideWeb = "web";
	public static $ringsideApi = "api";
}

$GLOBALS['facebook_config']['debug']=0;

# This is used for the client to give back appropriate URLS.
# $webUrl = The Web page url to return to an application 
Config::$ringsideWeb = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/web";

# $serverUrl = The URL including rest endpoint for the REST SERVER to talk to. 
# if this is not a full install or packaged install you might need to change $webRoot to the location
# of the API server. 
Config::$ringsideApi = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/restserver.php";
