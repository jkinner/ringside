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
require_once ('ringside/api/bo/Network.php');
require_once ('ringside/api/bo/Users.php');

/**
 * Deletes a Ringside network. Only the administrator
 * user is allowed to delete a network.
 *
 * @author Mike Schachter
 * @apiName AdminDeleteNetwork
 * @apiRequired nid The network ID of the network to delete.
 * @callMethod ringside.admin.deleteNetwork
 * @return boolean true if network was successfully deleted.
 */
class AdminDeleteNetwork extends Api_DefaultRest
{
	protected $m_nid;

	public function validateRequest()
	{
		//make sure calling application is a default application
		$this->checkDefaultApp();
		
		$this->m_nid = $this->getRequiredApiParam('nid');
	}

	public function execute()
	{
		$isAdmin = Api_Bo_Users::isUserAdmin($this->getUserId());
		if(! $isAdmin)
		{
			throw new Exception('Only the administrator can delete networks.');
		}
		
		$enid = $this->m_nid;
		Api_Bo_Network::deleteNetwork($enid);
		return true;
	}
}

?>
