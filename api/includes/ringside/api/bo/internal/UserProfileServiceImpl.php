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

include_once 'ringside/api/bo/UserProfileService.php';
include_once 'ringside/api/dao/Profile.php';

class Api_Bo_UserProfileServiceImpl extends Api_Bo_UserProfileService
{	
	public function isSupported($propertyName)
	{
		//will change when we're smarter about open social
		return Api_Dao_Profile::hasProperty($propertyName);
	}
	
	public function getProfiles(array $uids, array $domainKeys, array $propertyNames)
	{
		if (count($uids) != count($domainKeys)) {
			throw new Exception('[UserProfileServiceImpl] each uid must be associated with a domain id.');
		}
		
		$plist = array();
		
		for ($k = 0; $k < count($uids); $k++) {		
			$uid = $uids[$k];
			$did = $domainKeys[$k];
			
			$p = Api_Dao_Profile::getProfile($uid, $did, $propertyNames);
			if ($p == NULL) {				
				throw new Exception("[UserProfileServiceImpl] could not find profile for uid=$uid, domainId=$did.");
			}
			$plist[] = $p;
		}
		return $plist;
	}
	
	public function updateProfile($uid, $newProperties)
	{
	
	}
}


?>