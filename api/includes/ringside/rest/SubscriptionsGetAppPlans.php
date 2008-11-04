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
require_once ("ringside/api/bo/Payments.php");

/**
 * 
 */
/**
 * Get all payment plans associated with the given application id.
 * 
 * @author Brian R. Robinson, Ringside Networks - brobinson@ringsidenetworks.com
 * @apiName SubscriptionsGetAppPlans
 * @callMethod ringside.subscriptions.getAppPlans
 * @apiRequired aid - the ID of the application for which payment plans will be returned.
 * @return Array containing plan_id, aid, network_id, name, length, unit, price, description. 
 */
class SubscriptionsGetAppPlans extends Api_DefaultRest
{
	private $aid;

	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		$this->aid = $this->getApiParam('aid', $this->getAppId());
	}

	/**
	 * Reads payment plans from database.
	 *
	 * @return array of payment plans [ plan_id, name, price, description ]
	 */
	public function execute()
	{
		$response = array();
		
		$result = Api_Dao_Payments::getPlansByAid($this->aid);
		if(count($result) == 0)
		{
			$response['result'] = '';
		}else
		{
			$plans = array();
			foreach($result as $key=>$row)
			{
				$plan = array();
				$plan['plan_id'] = $row['id'];
				$plan['aid'] = $row['aid'];
				$plan['network_id'] = $row['network_id'];
				$plan['name'] = $row['name'];
				$plan['length'] = $row['length'];
				$plan['unit'] = $row['unit'];
				$plan['price'] = $row['price'];
				$plan['description'] = $row['description'];
				$plan['num_friends'] = $row['num_friends'];
				$plans[] = $plan;
			}
			$response['plans'] = $plans;
		}
		//    	error_log( 'SubscriptionsGetAppPlans response: ' . print_r( $response, true ) );
		return $response;
	}
}
?>
