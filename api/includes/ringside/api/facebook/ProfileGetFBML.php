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
 * Get a users profile FBML for a given application.  Note, 
 * this information is stored here so this operation is push 
 * based. 
 * 
 * By default it will return for User/App part of session.  To use
 * in administrative mode calling application must be a default application.
 *
 * @apiName ProfileGetFBML
 * @callMethod profile.getFBML
 * @apiOptional aid - the ID of the application for which fbml will be retrieved
 * @apiOptional uid - Override userid to get FBML
 * @return Array 'result'=>your profile fbml
 * 
 * @author Richard Friedman
 * @author Mike Schachter
 */
class ProfileGetFBML extends Api_DefaultRest
{
	/** If we are acting on a different UID */
	private $m_uid;
	private $m_aid;

	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam('uid', $this->getUserId());
		$this->m_aid = $this->getApiParam('aid', $this->getAppId());
		if(empty($this->m_aid))
			$this->m_aid = $this->getAppId();
	}

	/**
	 * Gets the fbml for the user
	 *
	 * @return Array
	 */
	public function execute()
	{
		$this->checkDefaultApp($this->m_aid);
		
		$retVal = array();
		$fbml = Api_Bo_App::getFBML($this->m_uid, $this->m_aid);
		if($fbml == "")
		{
			$retVal['result'] = "";
		}else
		{
			$retVal['result'] = "<![CDATA[" . $fbml . "]]>";
		}
		return $retVal;
	}
}

?>
