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

require_once ("ringside/api/AuthRest.php");
require_once ("ringside/api/bo/App.php");

/**
 * TODO doc and is session pushed to db?
 */
class AuthCreateAppSession extends Api_AuthRest
{
	const PARAM_USER_ID = 'uid';
	const PARAM_NETWORK_KEY = 'network_key';
	const PARAM_INFINITE = 'infinite';
	const PARAM_APP_API_KEY = 'app_api_key';
	
	const RESPONSE_SESSION_KEY = 'session_key';
	const RESPONSE_EXPIRES = 'expires';
	const RESPONSE_USER_ID = 'uid';
	
	private $m_uid;
	private $m_apiKey;
	private $m_networkKey;
	private $m_infinite = false;

	public function validateRequest()
	{
		$this->m_uid = $this->getRequiredApiParam(self::PARAM_USER_ID);
		$this->m_networkKey = $this->getContext()->getNetworkKey();
		$infinite = $this->getApiParam(self::PARAM_INFINITE, '');
		$this->m_infinite = (strcasecmp($infinite, 'true') === 0) ? true : false;
		$this->m_apiKey = $this->getApiParam(self::PARAM_APP_API_KEY, $this->getSessionValue(self::SESSION_API_KEY));
	
	}

	public function execute()
	{
		$appService = Api_ServiceFactory::create('AppService');
		$appId = $appService->getNativeIdByApiKey($this->m_apiKey);
		$this->setSessionValue(self::SESSION_APP_ID, $appId);
		$this->checkDefaultApp();
		
		$sid = $this->getSessionValue(self::SESSION_ID);
		
		$this->setSessionValue(self::SESSION_TYPE, self::SESSION_TYPE_VALUE_SESS);
		$this->setSessionValue(self::SESSION_APPROVED, true);
		if($this->m_infinite === true)
		{
			//         Session::mark_infinite( $sid );
			$this->setSessionValue(self::SESSION_INFINITE, 'true');
			$this->setSessionValue(self::SESSION_EXPIRES, self::SESSION_EXPIRES_VALUE_NEVER);
		}else
		{
			$this->setSessionValue(self::SESSION_INFINITE, 'false');
			$this->setSessionValue(self::SESSION_EXPIRES, time() + 5 * 60);
		}
		
		$this->setSessionValue(self::SESSION_CALL_ID, 0);
		
		$this->setSessionValue(self::SESSION_APP_ID, $appId);
		$this->setSessionValue(self::SESSION_API_KEY, $this->m_apiKey);
		$this->setSessionValue(self::SESSION_USER_ID, $this->m_uid);
		
		$this->setSessionValue(self::SESSION_NETWORK_ID, $this->m_networkKey);
		$this->m_nid = $this->m_networkKey;
		
		$response = array();
		$response[self::RESPONSE_SESSION_KEY] = $sid . '-ringside';
		$response[self::RESPONSE_EXPIRES] = $this->getSessionValue(self::SESSION_EXPIRES);
		$response[self::RESPONSE_USER_ID] = $this->getSessionValue(self::SESSION_USER_ID);
		
		return $response;
	}
}

?>
