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
 * Get the current users Session for a given application.
 * This API requires execution by a DEFAULT application.
 *
 * @author Richard Friedman
 * @apiName UsersGetAppSession
 * @callMethod ringside.users.getAppSession
 * @apiOptional int AppId The application ID.
 * @error_code 95 Invalid Parameter
 * @error_code 900 No application exist for given parameters
 * @return Array containing infinite, session key
 */
class UsersGetAppSession extends Api_DefaultRest
{
	
	private $m_uid;
	private $m_aid;

	public function validateRequest()
	{
		$this->m_aid = $this->getRequiredApiParam('app_id');
		$this->m_uid = $this->getApiParam('user_id', $this->getUserId());
	}

	public function execute()
	{
		
		// only available for default applications.
		$this->checkDefaultApp();
		
		$uas = Api_Bo_App::getUserAppSession($this->m_uid, $this->m_aid);
		
		$response = array();
		if(count($uas) == 0)
		{
			$response['session'] = '';
		}else
		{
			$response['infinite'] = $uas[0]['infinite'] == 1? 'true' : 'false';
			$response['session_key'] = $uas[0]['session_key'];
		}
		
		return $response;
	}
}

?>
