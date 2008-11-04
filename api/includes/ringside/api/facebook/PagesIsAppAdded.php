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
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Pages.php");

/**
 * Does the page requested have this app installed?
 * 
 * @author Richard Friedman
 */
class PagesIsAppAdded extends Api_DefaultRest
{
	private $page_id;
	
	public function validateRequest()
	{
		$this->page_id = $this->getRequiredApiParam('page_id');
	}
	
	/**
	 * Execute the users.hasAppPermission method
	 */
	public function execute()
	{
		$hasApp = Api_Bo_Pages::hasApp($this->page_id, $this->getAppId());
		
		$response = array();
		$response ['result'] = $hasApp ? '1' : '0';
		
		return $response;
	}

}

?>
