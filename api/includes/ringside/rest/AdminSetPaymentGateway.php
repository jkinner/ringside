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

require_once 'ringside/api/bo/Users.php';
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Payments.php");

class AdminSetPaymentGateway extends Api_DefaultRest
{
	private $type;
	private $subject;
	private $password;

	public function validateRequest()
	{
		//make sure calling application is a default application
		$this->checkDefaultApp();
		
		$this->type = $this->getApiParam('type');
		$this->subject = $this->getRequiredApiParam('subject');
		$this->password = $this->getRequiredApiParam('password');
	}

	/**
	 * Creates the user if it's not in the DB.  Throws an exception if the user already exists!
	 *
	 * @return Array[user][id]
	 * @throws Exception
	 */
	public function execute()
	{
		if(! Api_Bo_Users::isUserAdmin($this->getUserId()))
		{
			throw new OpenFBAPIException('Only the admin user may set a payment gateway.');
		}
		
		$response = array();
		
		$result = Api_Bo_Payments::getGateway();
		if(count($result) == 0)
		{
			Api_Bo_Payments::createGateway($this->type, $this->subject, $this->password);
			$response['gateway'] = array();
			$response['gateway']['type'] = $this->type;
			$response['gateway']['subject'] = $this->subject;
			$response['gateway']['password'] = $this->password;
		}else
		{
			throw new OpenFBAPIException('Payment gateway' . $this->type . ' already exists!  Only one payment gateway may be provisioned.');
		}
		
		return $response;
	}
}

?>
