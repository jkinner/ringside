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
 * Returns all the apps that are attached to this user
 * this information is stored here so this operation is push 
 * based. 
 * 
 * By default it will return for User/App part of session.  To use
 * in administrative mode calling application must be a default application.
 *
 * @apiName UsersGetAppList
 * @callMethod ringside.users.getAppList
 * @return Array 'apps'->list of applications ( $apps = $response['apps']; $apps[0]; )
 * 
 * @author Richard Friedman
 */
class UsersGetAppList extends Api_DefaultRest
{

	/**
	 * Validate request
	 */
	public function validateRequest()
	{
	}

	/**
	 * Execute the api call to get user app list.
	 */
	public function execute()
	{
		$list = Api_Bo_App::getApplicationListByUserId($this->getUserId());
		
		$ret = array();
		foreach($list as $app)
		{
			$a = $app['RingsideApp'];
			$out = array_merge($app, $a);
			unset($out['RingsideApp']);
			$ret[] = $out;
		}
		
		$response = array();
		if(! empty($ret))
		{
			$response['apps'] = $ret;
		}
		
		return $response;
	}
}

?>
