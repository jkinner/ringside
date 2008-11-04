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

require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/App.php");

/**
 * Set's the information in the users_app table
 */
class UsersSetApp extends Api_DefaultRest
{
	private $app_id;

	/**
	 * Validate Request.
	 */
	public function validateRequest()
	{
		$this->app_id = $this->getRequiredApiParam('app_id');
	}

	/**
	 * Execute the api call to get user app list.
	 */
	public function execute()
	{
		$allows_status_update  = $this->getBooleanValue($this->getApiParam('allows_status_update', null));
		$allows_create_listing = $this->getBooleanValue($this->getApiParam('allows_create_listing', null));
		$allows_photo_upload = $this->getBooleanValue($this->getApiParam('allows_photo_upload', null));
		$auth_information = $this->getBooleanValue($this->getApiParam('auth_information', null));
		$auth_profile = $this->getBooleanValue($this->getApiParam('auth_profile', null));
		$auth_leftnav = $this->getBooleanValue($this->getApiParam('auth_leftnav', null));
		$auth_newsfeeds = $this->getBooleanValue($this->getApiParam('auth_newsfeeds', null));
		$profile_col = $this->getBooleanValue($this->getApiParam('auth_newsfeeds', null));
		$profile_order = $this->getBooleanValue($this->getApiParam('auth_newsfeeds', null));
		$ret = Api_Bo_App::setUsersApp($this->app_id, $this->getUserId(), $allows_status_update, $allows_create_listing, 
			$allows_photo_upload, $auth_information, $auth_profile, $auth_leftnav, $auth_newsfeeds, $profile_col, $profile_order);
		
		$response['result'] = $ret > 0?'1':'0';
		
		return $response;
	}
	
	// Sanitize input from HTML checkboxes and convert to boolean
	private function getBooleanValue($any_kind_of_boolean_string)
	{
		return $any_kind_of_boolean_string == "yes" || $any_kind_of_boolean_string == "1" || $any_kind_of_boolean_string == "true" || $any_kind_of_boolean_string == true ? true : false;
	}
}

?>
