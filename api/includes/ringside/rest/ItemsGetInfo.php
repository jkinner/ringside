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
require_once ('ringside/api/DefaultRest.php');
require_once ('ringside/api/bo/Items.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class ItemsGetInfo extends Api_DefaultRest
{
	private $app_id;
	private $iids;
	private $datatype;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		
		$this->iids = $this->getApiParam('iids');
		$this->datatype = $this->getApiParam('datatype', 0);
		$this->app_id = $this->getAppId();
		
		try
		{
			// This allows a managing app to get item info for an item owned by another app
			$this->checkDefaultApp();
			$this->app_id = $this->getApiParam('aid', $this->app_id);
		}catch(Exception $e)
		{
			// do nothing
		}
	}

	/**
	 * Gets the definition of a set of items.
	 *
	 * @param array $iids item identifiers for which to retrieve definitions
	 * @param array $datatype optional - the set of data types to filter 
	 * 
	 * public function items_getInfo($iids, $datatype = null)
	 */
	public function execute()
	{
		$items = Api_Bo_Items::getInfo($this->app_id, $this->iids, $this->datatype);
		
		$retVal = array();
		if(count($items) > 0)
		{
			$retVal['item'] = array();
			foreach($items as $item)
			{
				$retVal['item'][] = array('iid' => $item['item_id'], 'url' => $item['item_url'], 'refurl' => $item['item_refurl'], 'datatype' => $item['item_data_type']);
			}
		}
		
		return $retVal;
	}

}
?>
