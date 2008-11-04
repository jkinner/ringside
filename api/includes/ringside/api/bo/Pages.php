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
require_once 'ringside/api/dao/Pages.php';
require_once 'ringside/api/dao/Friends.php';
require_once 'ringside/api/OpenFBAPIException.php';
/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Pages
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $loggedInUser
	 * @param unknown_type $uid
	 * @param unknown_type $pageId
	 * @return unknown
	 */
	public static function isFan($loggedInUser, $uid, $pageId)
	{
		if(Api_Dao_Friends::friendCheck($loggedInUser, $uid) === false)
		{
			throw new OpenFBAPIException(FB_ERROR_MSG_ISFAN_NOTFRIENDS, FB_ERROR_CODE_ISFAN_NOTFRIENDS);
		}
		return Api_Dao_Pages::isFan($pageId, $uid);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $pageId
	 * @return unknown
	 */
	public static function isAdmin($uid, $pageId)
	{
		return Api_Dao_Pages::isAdmin($pageId, $uid);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pageId
	 * @param unknown_type $appId
	 * @return unknown
	 */
	public static function hasApp($pageId, $appId)
	{
		return Api_Dao_Pages::hasApp($pageId, $appId);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $fields
	 * @param unknown_type $loggedInUser
	 * @param unknown_type $uid
	 * @param unknown_type $pageIds
	 * @return unknown
	 */
	public static function getPages($fields, $loggedInUser, $uid, $pageIds)
	{
		// The ID of the user. Defaults to the logged in user if the session_key is valid, 
		// and no page_ids are passed. Used to get the pages a given user is a fan of. 
		if(null == $pageIds)
		{
			if(null == $uid)
			{
				return Api_Dao_Pages::getPagesByUid($loggedInUser, $fields);
			}else
			{
				return Api_Dao_Pages::getPagesByUid($uid, $fields);
			}
		}else
		{
			if(null == $uid)
			{
				return Api_Dao_Pages::getPagesByIds($pageIds, $fields);
			}else
			{
				return Api_Dao_Pages::getPagesByUidAndPageIds($uid, $fields, $pageIds);
			}
		}
	}
	/**
	 * This method gets the full page info and then uses the fields and appId to create a new data structure to pass back to the caller.
	 * Meant to be used by the API layer as someone else probably just wants the Doctrine Data Structure and can get that by calling
	 * getPages from this BO.
	 *
	 * @param unknown_type $fields
	 * @param unknown_type $loggedInUser
	 * @param unknown_type $uid
	 * @param unknown_type $pageIds
	 * @return unknown
	 */
	public static function getPagesInfo($fields, $loggedInUser, $uid, $pageIds, $appId)
	{
		// Get the pages based on the parameters
		$pages = self::getPages($fields, $loggedInUser, $uid, $pageIds);
		// Figure out what common fields we need and what fields the user is asking for
		$commonFieldNames = array('name', 'has_added_app', 'page_id', 'pic_big', 'pic_small', 'pic_square', 'pic_large', 'type', 'pic');
		$commonFields = array_intersect($fields, $commonFieldNames);
		$pageFields = array_diff($fields, $commonFieldNames);
		$response = array();
		if(count($pages) > 0)
		{
			$response['page'] = array();
			// Loop throught the pages collecting the data the user asked for
			foreach($pages as $page)
			{
				$pageResult = array();
				// First, get the obvious ones, the common fields
				$pageResult['page_id'] = $page->page_id;
				if(in_array('name', $commonFields))
				{
					$pageResult['name'] = $page->name;
				}
				if(in_array('type', $commonFields))
				{
					$pageResult['type'] = $page->type;
				}
				if(in_array('pic_big', $commonFields))
				{
					$pageResult['pic_big'] = $page->pic_url;
				}
				if(in_array('pic_small', $commonFields))
				{
					$pageResult['pic_small'] = $page->pic_url;
				}
				if(in_array('pic_square', $commonFields))
				{
					$pageResult['pic_square'] = $page->pic_url;
				}
				if(in_array('pic_large', $commonFields))
				{
					$pageResult['pic_large'] = $page->pic_url;
				}
				if(in_array('pic', $commonFields))
				{
					$pageResult['pic'] = $page->pic_url;
				}
				// Now find out if this app was added to this particular page
				if(in_array('has_added_app', $commonFields))
				{
					$hasApp = Api_Dao_Pages::hasApp($page->page_id, $appId);
					if($hasApp)
					{
						$pageResult['has_added_app'] = '1';
					}else
					{
						$pageResult['has_added_app'] = '0';
					}
				}
				// Now get all the dynamic fields the user asked for
				$found = array();
				foreach($page->RingsidePagesInfo as $pi)
				{
					if(in_array($pi->name, $pageFields))
					{
						if($pi->json_encoded == 0)
						{
							$pageResult[$pi->name] = $pi->value;
						}else
						{
							$pageResult[$pi->name] = json_decode($pi->value, true);
						}
						
						$found[] = $pi->name;
					}
				}

				$left = array_diff($pageFields, $found);
				foreach($left as $a)
				{
					$pageResult[$a] = '';
				}
				// Add this page to our response
				$response['page'][] = $pageResult;
			}
		}
		return $response;
	}
}
?>
