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
require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideSocialPayGateway.php');
require_once ('ringside/api/dao/records/RingsideSocialPayPlan.php');
require_once ('ringside/api/dao/records/RingsideSocialPaySubscription.php');
require_once ('ringside/api/dao/records/RingsideSocialPaySubscriptionsFriend.php');

/**
 * The DB layer for everything Payment Plan related.
 *
 * @author Brian R. Robinson
 */

class Api_Dao_Payments
{
	public static function getPlanByPlanid( $planid )
	{
		$q = Doctrine_Query::create();

		$q->select('price, num_friends')->from('RingsideSocialPayPlan')->where('id = ' . $planid);

		$gateways = $q->execute();
		
		return $gateways->toArray();
	}

	public static function getPlansByAid( $aid )
	{
		$q = Doctrine_Query::create();

		$q->select('id, name, price, description, num_friends')->from('RingsideSocialPayPlan')->where('aid = ' . $aid);

		$gateways = $q->execute();
		
		return $gateways->toArray();
	}

	public static function createPlan($aid, $planName, $price, $numFriends, $nid, $description, $length = 1, $unit = 'months')
	{
		$plan = new RingsideSocialPayPlan();
		$plan->aid = $aid;
		$plan->name = $planName;
		$plan->price = $price;
		$plan->length = $length;
		$plan->unit = $unit;
		$plan->num_friends = $numFriends;
		$plan->network_id = $nid;
		$plan->description = $description;

		// Use trySave because it does not throw an exception upon failure, instead returns true on success fals on failure
		$ret = $plan->trySave();
		if($ret)
		{
			return $plan->getIncremented();
		}

		return false;
	}

	public static function deletePlan($planId)
	{
		$q = new Doctrine_Query();

		return $q->delete('RingsideSocialPayPlan')->from('RingsideSocialPayPlan p')->where("id = $planId")->execute();
	}

	public static function createAppSubscription($uid, $nid, $aid, $planid, $subscriptionid)
	{
		$subscription = new RingsideSocialPaySubscription();
		$subscription->uid = $uid;
		$subscription->network_id = $nid;
		$subscription->aid = $aid;
		$subscription->plan_id = $planid;
		$subscription->gateway_subscription_id = $subscriptionid;

		// Use trySave because it does not throw an exception upon failure, instead returns true on success fals on failure
		$ret = $subscription->trySave();
		if($ret)
		{
			return $subscription->getIncremented();
		}

		return false;
	}

	/**
	 * Creates a application subscription for the specified user and their friends.
	 *
	 * @param integer $uid
	 * @param string $nid
	 * @param integer $aid
	 * @param integer $planid
	 * @param string $subscriptionid
	 * @param array $friends An array of friends, each of which is represented by an array with keys 'uid' and 'nid'.
	 * @return the id of the subscription created.
	 */
	public static function createFriendsAppSubscription($uid, $nid, $aid, $planid, $subscriptionid, $friends)
	{
		$appSubscriptionId = self::createAppSubscription($uid, $nid, $aid, $planid, $subscriptionid);
		foreach($friends as $friend)
		{
			$friendSubscription = new RingsideSocialPaySubscriptionsFriend();
			$friendSubscription->subscription_id = $appSubscriptionId;
			$friendSubscription->friend_id = $friend['uid'];
			$friendSubscription->friend_network_id = $friend['nid'];

			$ret = $friendSubscription->trySave();
			if($ret)
			{
				continue;
			}else
			{
				return false; //TODO roll back the ones that succeeded
			}
		}
		return $appSubscriptionId;
	}

	public static function getSubscriptionsByUidNid( $uid, $nid )
	{
		$q = Doctrine_Query::create();
		$q->select('*')->from('RingsideSocialPaySubscription s')->where("uid = $uid and network_id = $nid");
		$subscriptions = $q->execute();
		
		return $subscriptions->toArray();
	}

	public static function getSubscriptionsByUidAndNidAndAid($uid, $nid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideSocialPaySubscription s INNER JOIN s.RingsideSocialPayPlan pp ON s.plan_id = pp.id')->where("s.aid = $aid and s.network_id = '$nid' and s.uid = $uid");
		$subscriptions = $q->execute();
		return $subscriptions->toArray( true );
	}

	public static function getFriendSubscriptionsByUidAndNidAndAid($friendid, $friendnid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideSocialPaySubscription s INNER JOIN s.RingsideSocialPayPlan pp ON s.plan_id = pp.id INNER JOIN s.RingsideSocialPaySubscriptionsFriend f ON s.id = f.subscription_id')->where("f.friend_id = $friendid and f.friend_network_id = '$friendnid' and s.aid = $aid");
		
		$subscriptions = $q->execute();
		return $subscriptions->toArray( true );
	}

	public static function getGateway()
	{
		$q = Doctrine_Query::create();
		$q->select('type, subject, password')->from('RingsideSocialPayGateway g');
		$gateways = $q->execute();
		
		return $gateways->toArray();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $type
	 * @param unknown_type $subject
	 * @param unknown_type $password
	 * @return unknown
	 */
	public static function createGateway($type, $subject, $password)
	{
		$gateway = new RingsideSocialPayGateway();
		$gateway->type = $type;
		$gateway->subject = $subject;
		$gateway->password = $password;

		// Use trySave because it does not throw an exception upon failure, instead returns true on success fals on failure
		$ret = $gateway->trySave();
		if($ret)
		{
			return $gateway->getIncremented();
		}

		return false;
	}
}
?>
