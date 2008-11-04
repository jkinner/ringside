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
require_once 'ringside/api/dao/UsersApp.php';
require_once 'ringside/api/OpenFBAPIException.php';
require_once 'ringside/api/dao/Feed.php';
require_once 'ringside/api/dao/Friends.php';
require_once 'ringside/api/dao/App.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Feed
{
	const RS_FBDB_FEED_TYPE_STORY = '1';
	const RS_FBDB_FEED_TYPE_ACTION = '2';
	const RS_FBDB_FEED_TEMPLATE_WITH = '1';
	const RS_FBDB_FEED_NOT_TEMPLATE = '0';

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $appId
	 * @param unknown_type $type
	 * @param unknown_type $templatized
	 * @param unknown_type $author
	 * @param unknown_type $title
	 * @param unknown_type $body
	 * @param unknown_type $image1
	 * @param unknown_type $image1Link
	 * @param unknown_type $image2
	 * @param unknown_type $image2Link
	 * @param unknown_type $image3
	 * @param unknown_type $image3Link
	 * @param unknown_type $image4
	 * @param unknown_type $image4Link
	 * @param unknown_type $titleData
	 * @param unknown_type $bodyData
	 * @param unknown_type $bodyGeneral
	 * @param unknown_type $priority
	 * @param unknown_type $targets
	 * @param unknown_type $actor
	 * @return unknown
	 */
	public static function createTemplatizedFeed($uid, $appId, $type, $templatized, $author, $title, $body, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $titleData, $bodyData, $bodyGeneral, $priority, $targets, $actor)
	{
		if(null == $priority || strlen($priority) == 0)
		{
			$priority = 0;
		}
		if($actor == null)
		{
			$actor = $uid;
		}else
		{
			// The ACTOR must be (a) friend (b) have app
			if(! Api_Dao_Friends::friendCheck($uid, $actor))
			{
				throw new Exception(FB_ERROR_MSG_ACTOR_USER_NOT_FRIENDS, FB_ERROR_CODE_REQUIRES_PERMISSION);
			}
			if(! Api_Dao_UsersApp::isUsersApp($appId, $actor))
			{
				throw new Exception(FB_ERROR_MSG_ACTOR_USER_DONT_SHAREAPPS, FB_ERROR_CODE_REQUIRES_PERMISSION);
			}
		}

		if($targets != null)
		{
			// All targets must be frinds of acting user. 
			$friends = Api_Dao_Friends::friendsGetFriends($actor);
			$targets = explode(",", $targets);
			$intersect = array_intersect($friends, $targets);
			if(count($targets) != count($intersect))
			{
				throw new Exception(FB_ERROR_MSG_TARGETS_NOT_FRIENDS, FB_ERROR_CODE_REQUIRES_PERMISSION);
			}
		}
		
		// Validate title data, actor token must exist.
		$titleTokens = self::get_tokens($title);
//		error_log( 'title tokens: ' . var_export( $titleTokens, true ) );
		$countTest = 1;
		if(! in_array("actor", $titleTokens))
		{
			throw new Exception(FB_ERROR_MSG_FEED_MISSING_ACTOR, FB_ERROR_CODE_FEED_TITLE_PARAMS);
		}
		if(in_array("target", $titleTokens))
		{
			if($targets == null)
				throw new Exception(FB_ERROR_MSG_FEED_MISSING_TARGETS, FB_ERROR_CODE_FEED_TITLE_PARAMS);
			$countTest = 2;
		}
		
		if(count($titleTokens) > $countTest)
		{
			if($titleData == null || empty($titleData))
			{
				throw new Exception(FB_ERROR_MSG_FEED_TITLE_JSON_EMPTY, FB_ERROR_CODE_FEED_TITLE_JSON);
			}
			
			// check all tokens in string are in data
			$titleDataTokens = json_decode($titleData, true);
//			error_log( 'title data tokens: ' . var_export( $titleDataTokens, true ) );
			if(array_key_exists('actor', $titleDataTokens) || array_key_exists('target', $titleDataTokens))
			{
				throw new Exception(FB_ERROR_MSG_FEED_TITLE_JSON_INVALID, FB_ERROR_CODE_FEED_TITLE_JSON);
			}
			
			foreach($titleTokens as $token)
			{
//				error_log( 'title token: ' . $token );
				if($token != 'target' && $token != 'actor' && ! array_key_exists($token, $titleDataTokens))
				{
					throw new Exception(FB_ERROR_MSG_FEED_MISSING_PARAMS, FB_ERROR_CODE_FEED_TITLE_PARAMS);
				}
			}
		}else
		{
			$titleData = null;
		}
		
		// Validate body data
		$bodyTokens = self::get_tokens($body);
		$countTest = 0;
		if(in_array("actor", $bodyTokens))
			$countTest ++;
		if(in_array("target", $bodyTokens))
		{
			if($targets == null)
				throw new Exception(FB_ERROR_MSG_FEED_BODY_MISSING_TARGETS, FB_ERROR_CODE_FEED_BODY_PARAMS);
			$countTest ++;
		}
		if(count($bodyTokens) > $countTest)
		{
			
			if($bodyData == null || empty($bodyData))
			{
				throw new Exception(FB_ERROR_MSG_FEED_BODY_JSON_EMPTY, FB_ERROR_CODE_FEED_BODY_JSON);
			}
			
			$bodyDataTokens = json_decode($bodyData, true);
			if(array_key_exists('actor', $bodyDataTokens) || array_key_exists('target', $bodyDataTokens))
			{
				throw new Exception(FB_ERROR_MSG_FEED_BODY_JSON_INVALID, FB_ERROR_CODE_FEED_BODY_JSON);
			}
			
			foreach($bodyTokens as $token)
			{
				if($token != 'target' && $token != 'actor' && ! array_key_exists($token, $bodyDataTokens))
				{
					throw new Exception(FB_ERROR_MSG_FEED_BODY_MISSING_PARAMS, FB_ERROR_CODE_FEED_BODY_PARAMS);
				}
			}
		
		}else
		{
			$bodyData = null;
		}
		
		return Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, $priority);
	}

	public static function createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, $priority)
	{
		return Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, $priority);
	}
	
    public static function getFeedEntries( $uid, $actorid, $friends, $actions, $stories ) {
    	return Api_Dao_Feed::getFeedEntries( $uid, $actorid, $friends, $actions, $stories );
    }
	/**
	 * Tokenizes text
	 *
	 * @param unknown_type $txt
	 * @return unknown
	 */
	private static function get_tokens($txt)
	{
		$matches = array();
		preg_match_all('|{(.*)}|U', $txt, $matches, PREG_PATTERN_ORDER);
		return array_unique($matches[1]);
	}
}

?>
