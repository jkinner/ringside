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
require_once 'ringside/api/dao/Payments.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Payments
{

	public static function getGateway()
	{
		return Api_Dao_Payments::getGateway();
	}
	
	public static function createGateway($type, $subject, $password)
	{
		return Api_Dao_Payments::createGateway($type, $subject, $password);
	}
	
	public static function getSubscriptionsByUidAndNidAndAid($uid, $nid, $aid)
	{
		return Api_Dao_Payments::getSubscriptionsByUidAndNidAndAid($uid, $nid, $aid);
	}
	public static function getFriendSubscriptionsByUidAndNidAndAid($friendid, $friendnid, $aid)
	{
		return Api_Dao_Payments::getFriendSubscriptionsByUidAndNidAndAid($friendid, $friendnid, $aid);
	}
	
	public static function createPlan($aid, $planName, $price, $numFriends, $nid, $description, $length = 1, $unit = 'months')
	{
		return Api_Dao_Payments::createPlan($aid, $planName, $price, $numFriends, $nid, $description, $length, $unit);
	}
	
	public function deletePlan($planId)
	{
		return Api_Dao_Payments::deletePlan($planId);
	}
	
	public static function getPlansByAid($aid)
	{
		return Api_Dao_Payments::getPlansByAid($aid);
	}
	
	public static function getSubscriptionsByUidNid($uid, $nid)
	{
		return Api_Dao_Payments::getSubscriptionsByUidNid($uid, $nid);
	}
}

?>
