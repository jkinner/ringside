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
require_once ("ringside/api/dao/Payments.php");
require_once ("Payment/Process.php");
require_once ("Validate/US.php");
require_once ("Validate.php");

/**
 * @author brob@ringsidenetworks.com
 */
class Api_Bo_Subscriptions
{

	public static function createAppSubscription($uid, $nid, $aid, $planid, $ccn, $cctype, $expdate, $firstname = null, $lastname = null, $email = null, $phone = null)
	{
		
		$result = self::subscribeViaGateway($uid, $nid, $aid, $planid, $ccn, $cctype, $expdate, $firstname, $lastname, $email, $phone);
		
		if($result->code == 'Ok')
		{
			$subscriptionid = $result->transactionId;
			
			$payments = new Api_Dao_Payments();
			$subscribeResult = $payments->createAppSubscription($uid, $nid, $aid, $planid, $subscriptionid);
			$response = array();
			$response['subscription'] = array('gateway_subscription_id' => $subscriptionid, 'uid' => $uid, 'aid' => $aid, 'nid' => $nid, 'planid' => $planid, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'phone' => $phone);
			
			return $response;
		}else
		{
			throw new OpenFBAPIException($result->messageCode . ': ' . $result->message);
		}
	}

	public static function createFriendsAppSubscription($uid, $nid, $aid, $planid, $ccn, $cctype, $expdate, $friends, $firstname = null, $lastname = null, $email = null, $phone = null)
	{
		$plan = Api_Dao_Payments::getPlanByPlanid($planid);
		$numfriends = $plan[0]['num_friends'];
		if(count($friends) > $numfriends)
		{
			throw new OpenFBAPIException('Too many friends selected for subscription plan.  Only ' . $numfriends . ' allowed.');
		}
		
		$result = self::subscribeViaGateway($uid, $nid, $aid, $planid, $ccn, $cctype, $expdate, $firstname, $lastname, $email, $phone);
		
		if($result->code == 'Ok')
		{
			$subscriptionid = $result->transactionId;
			$payments = new Api_Dao_Payments();
			$subscribeResult = $payments->createFriendsAppSubscription($uid, $nid, $aid, $planid, $subscriptionid, $friends);
			$response = array();
			$response['subscription'] = array('gateway_subscription_id' => $subscriptionid, 'uid' => $uid, 'aid' => $aid, 'nid' => $nid, 'planid' => $planid, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'phone' => $phone);
			
			return $response;
		}else
		{
			throw new OpenFBAPIException($result->messageCode . ': ' . $result->message);
		}
	}

	/**
	 * FIXME NID and AID are never used, but are in the parameter list
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $nid
	 * @param unknown_type $aid
	 * @param unknown_type $planid
	 * @param unknown_type $ccn
	 * @param unknown_type $cctype
	 * @param unknown_type $expdate
	 * @param unknown_type $firstname
	 * @param unknown_type $lastname
	 * @param unknown_type $email
	 * @param unknown_type $phone
	 * @return unknown
	 */
	private static function subscribeViaGateway($uid, $nid, $aid, $planid, $ccn, $cctype, $expdate, $firstname = null, $lastname = null, $email = null, $phone = null)
	{
        $response = array();
        $result = Api_Dao_Payments::getGateway();
        $response['gateway'] = $result;
//        error_log( 'response: ' . print_r( $response, true ) );
        
        $options = array('action' => 'subscribe');
        $type = $response['gateway'][0]['type'];
		$processor = Payment_Process::factory( $type, $options );
		if( ! PEAR::isError( $processor ) )
		{
			$processor->login = $response['gateway'][0]['subject'];
			$processor->password = $response['gateway'][0]['password'];
			$processor->amount = self::calculateAmount($planid);
			
			$card = Payment_Process_Type::factory('CreditCard');
			$card->type = $cctype;
			if(! isset($firstname) || ! isset($lastname))
			{
				throw new Exception('First name or last name is not set; can not process payment.');
			}
			$card->firstName = $firstname;
			$card->lastName = $lastname;
			$card->cardNumber = $ccn;
			$card->expDate = $expdate;
			$processor->setPayment($card);
			
			$processor->refId = 'refid-' . time();
			$processor->subscriptionName = 'sub-' . time();
			$processor->intervalLength = 1;
			$processor->intervalUnit = 'months';
			
			$dateArray = getdate();
			$dateStr = date('Y-m-d', $dateArray[0]);
			$processor->startDate = $dateStr;
			
			$processor->totalOccurrences = '9999';
			mt_srand(time());
			$rand = mt_rand();
			$processor->invoiceNumber = $uid . '-' . $rand;
			
			if(isset($email))
			{
				$processor->email = $email;
			}
			if(isset($phone))
			{
				$processor->phoneNumber = $phone;
			}
			$result = $processor->process();
			
			if(PEAR::isError($result))
			{
				throw new OpenFBAPIException($result->getMessage());
			}
			return $result;
		}
	}

	private static function calculateAmount($planid)
	{
		$plan = Api_Dao_Payments::getPlanByPlanid($planid);
		$price = $plan[0]['price'];
		return $price;
	}
}
?>