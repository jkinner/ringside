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
 * Determines whether a user has paid for a subscription or not.
 *
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */
class SocialPayUserHasPaid extends Api_DefaultRest
{
	private $uid;
	private $aid;
	private $nid;
	private $plan;

	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		$this->uid = $this->getRequiredApiParam('uid');
		$this->aid = $this->getRequiredApiParam('aid');
		$this->nid = $this->getApiParam('nid', $this->getNetworkId());
		$this->plan = $this->getApiParam('plan');
	}

	public function execute()
	{
		$results = Api_Bo_Payments::getSubscriptionsByUidAndNidAndAid($this->uid, $this->nid, $this->aid);
		
		// If one or more plans were specified, make sure the user is subscribed to one of those plans
		if(isset($this->plan) && ! empty($this->plan))
		{
			return $this->planMatches($results);
		}

		// Check if the user's subscription has been paid for by a friend
		if(count($results) == 0)
		{
			$friendresults = Api_Bo_Payments::getFriendSubscriptionsByUidAndNidAndAid($this->uid, $this->nid, $this->aid);
			if(isset($this->plan) && ! empty($this->plan))
			{
				return $this->planMatches($friendresults);
			}
			return count($friendresults) == 0 ? false : true;
		}
		return count($results) == 0 ? false : true;
	}

	private function planMatches($results)
	{
		$plans = split(',', $this->plan);
		foreach($plans as $plan)
		{
			$trimmed = trim($plan);
			foreach($results as $subscription)
			{
				if(strcasecmp($subscription['RingsideSocialPayPlan']['name'], $trimmed) == 0)
				{
					return true;
				}
			}
		}
		return false;
	}
}
?>
