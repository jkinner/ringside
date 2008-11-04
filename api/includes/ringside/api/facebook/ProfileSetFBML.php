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

require_once ('ringside/api/OpenFBAPIException.php');
require_once ('ringside/api/DefaultRest.php');
require_once ('ringside/api/bo/App.php');

/**
 * Users.getLoggedInUser API
 */
class ProfileSetFBML extends Api_DefaultRest
{
	
	/** If we are acting on a different UID */
	private $m_uid;
	private $m_fbml;
	private $m_hasFBML;

	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam('uid', $this->getUserId());
		$this->m_fbml = $this->getApiParam('profile', '');
		if(strlen($this->m_fbml) == 0)
		{
			//try the deprecated parameter "markup"
			$this->m_fbml = $this->getApiParam('markup', '');
		}
		$this->m_hasFBML = ($this->getSessionValue('fbml') != null) ? true : false;
	}

	public function execute()
	{
		$response = array();
		
		$ret = Api_Bo_App::setFBML($this->m_uid, $this->getAppId(), $this->m_fbml);
		
		$response['result'] = $ret !== false?'1':'0';
		
		return $response;
	}
}

?>
