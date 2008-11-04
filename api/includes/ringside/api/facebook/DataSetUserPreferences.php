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
 * Sets user preferences.
 *
 * Parameters supported
 * @param values a json_encoded array of ID=>VALUE pairs
 * @param replace true/false indicator if this should update existing values or replace existing values.
 *
 * @author Richard Friedman
 */
class DataSetUserPreferences extends Api_DefaultRest
{
	private $m_prefs;
	private $m_replace = false;

	public function validateRequest()
	{
		$replace = $this->getApiParam('replace');
		if(! empty($replace))
		{
			if($replace == 'true')
			{
				$this->m_replace = true;
			}else if($replace == 'false')
			{
				$this->m_replace = false;
			}else
			{
				throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
			}
		}
		
		$this->checkRequiredParam('values');
		$prefernces = json_decode($this->getApiParam('values'), true);
		if(empty($prefernces) || ! is_array($prefernces))
		{
			throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$this->m_prefs = array();
		foreach($prefernces as $count=>$prefIdValue)
		{
			if(! isset($prefIdValue['pref_id']) || ! isset($prefIdValue['value']))
			{
				throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
			}
			
			$prefValue = $prefIdValue['value'];
			$prefId = $prefIdValue['pref_id'];
			if(strlen($prefValue) > 128)
			{
				throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
			}
			
			if($prefId < 0 || $prefId > 200)
			{
				throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
			}
			
			$this->m_prefs[$prefId] = $prefValue;
		}
	}

	public function execute()
	{
		$prefs = Api_Bo_App::getApplicationPreferences($this->getAppId(), $this->getUserId());
		
		if($this->m_replace == true)
		{
			$prefs = array();
		}
		
		foreach($this->m_prefs as $prefId=>$prefValue)
		{
			if(empty($prefValue))
			{
				if(isset($prefs[$prefId]))
				{
					unset($prefs[$prefId]);
				}
			}else
			{
				$prefs[$prefId] = $prefValue;
			}
		
		}
		
		Api_Bo_App::saveAppPrefs($this->getAppId(), $this->getUserId(), $prefs);
		
		return array();
	}
}

?>
