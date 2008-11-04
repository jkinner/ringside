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
 * Is this page 
 * 
 * @author Richard Friedman
 */
class PagesGetInfo extends Api_DefaultRest
{
	private $fields;
	private $type;
	private $pageIds;
	private $uid;
	
	public function validateRequest()
	{
		$fields = $this->getRequiredApiParam('fields');
		$this->fields = explode(",", $fields);
		
		$pageIds = $this->getApiParam('page_ids', null);
		if($pageIds != null && ! empty($pageIds))
		{
			$this->pageIds = explode(",", $pageIds);
		}else
		{
			$this->pageIds = null;
		}
		
		$this->uid = $this->getApiParam('uid', null);
		$this->type = $this->getApiParam('type', null);
	}
	
	/**
	 * Execute test returing result:value.
	 * @return 0/1 in ['result']=
	 */
	public function execute()
	{
		return Api_Bo_Pages::getPagesInfo($this->fields, $this->getUserId(), $this->uid, $this->pageIds, $this->getAppId());
	}

}

?>
