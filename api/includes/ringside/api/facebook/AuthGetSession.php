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

require_once ("ringside/api/bo/App.php");
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/AuthRest.php");
/**
 * Implements auth.getSession API. 
 * 
 * @author Richard Friedman
 */
class AuthGetSession extends Api_AuthRest
{
	
	const RESPONSE_SESSION_KEY = 'session_key';
	const RESPONSE_EXPIRES = 'expires';
	const RESPONSE_USER_ID = 'uid';

	/**
	 * Validat the request.
	 */
	public function validateRequest()
	{
	}

	/**
	 * Turns and authorized interaction into a usable api session. 
	 * 
	 * @return session key, expires, uid.
	 */
	public function execute()
	{
		//       echo " Calling Auth Get Session today ";
		$response = array();
		
		if($this->getUserId() == null || $this->getSessionValue(self::SESSION_APPROVED) != true)
		{
			throw new OpenFBAPIException("Authorization token not approved.", FB_ERROR_CODE_UNKNOWN_ERROR);
		}
		
		$infinite = false;
		if($this->getSessionValue(self::SESSION_INFINITE) == 'true')
		{
			$infinite = true;
		}
		
		// Create session and return session id.
		session_regenerate_id(true);
		$sid = session_id();
		if($infinite === true)
		{
			Session::mark_infinite($sid);
		}
		
		$this->setSessionValue(self::SESSION_TYPE, self::SESSION_TYPE_VALUE_SESS);
		$this->setSessionValue(self::SESSION_CALL_ID, 0);
		if($infinite === true)
		{
			//			error_log("Authorized infinite session for ".$this->getUserId());
			$this->setSessionValue(self::SESSION_EXPIRES, self::SESSION_EXPIRES_VALUE_NEVER);
		}else
		{
			$this->setSessionValue(self::SESSION_EXPIRES, time() + 24 * 60 * 60);
		}
		
		$response[self::RESPONSE_SESSION_KEY] = $sid . '-ringside';
		$response[self::RESPONSE_EXPIRES] = $this->getSessionValue(self::SESSION_EXPIRES);
		$response[self::RESPONSE_USER_ID] = $this->getUserId();
		
		// Persist link to the session information
		$uas = Api_Bo_App::setUserAppSession($this->getAppId(), $this->getUserId(), $infinite, $sid);
		
		return $response;
	}
}

?>
