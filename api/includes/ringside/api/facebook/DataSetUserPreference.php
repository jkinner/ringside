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
require_once ("ringside/api/bo/App.php");

/**
 * Data Set User Preferences
 * 
 * @author Richard Friedman
 */
class DataSetUserPreference extends Api_DefaultRest
{
	private $m_value;
	private $m_id;

	public function validateRequest()
	{
		$this->m_value = $this->getApiParam('value', null);
		$this->m_id = $this->getRequiredApiParam('pref_id');
		
		if(strlen($this->m_value) > 128)
		{
			throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING . " value ({$this->m_value})", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		if($this->m_id < 0 || $this->m_id > 200)
		{
			throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING . " pref_id ", FB_ERROR_CODE_PARAMETER_MISSING);
		}
	}

	public function execute()
	{
		$prefs = Api_Bo_App::getApplicationPreferences($this->getAppId(), $this->getUserId());
		
		if(empty($this->m_value))
		{
			if(isset($prefs[$this->m_id]))
			{
				unset($prefs[$this->m_id]);
			}
		}else
		{
			$prefs[$this->m_id] = $this->m_value;
		}
		
		Api_Bo_App::saveAppPrefs($this->getAppId(), $this->getUserId(), $prefs);
		
		return array();
	}
}

?>
