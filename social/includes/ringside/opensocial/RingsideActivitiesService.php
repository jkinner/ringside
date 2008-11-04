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
require_once( 'ringside/social/config/RingsideSocialConfig.php');
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClientsRest.php' );

class RingsideActivitiesService {

	public function getActivities($ids, $token)
	{
		$client=$token->getAppClient();		
		
//		$allActivities = XmlStateFileFetcher::get()->getActivities();
		$activities = array();
//		foreach ( $ids as $id ) {
//			if (isset($allActivities[$id])) {
//				$activities[] = $allActivities[$id];
//			}
//		}
		// TODO: Sort them
		return new ResponseItem(null, null, $activities);
	}
	
	public function createActivity($personId, $activity, $token)
	{
		$client=$token->getAppClient();
		
		// TODO: Validate the activity and do any template expanding
		$activity->setUserId($personId);
		$activity->setPostedTime(time());

		$resp=$client->feed_publishActionOfUser($activity->getTitle(),$activity->getBody);
		if($resp==0)
			return new ResponseItem('internalError', "Your Activity failed to be published.", null);
		
		return new ResponseItem(null, null, array());
	}

}
?>