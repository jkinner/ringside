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
require_once ('ringside/api/dao/records/RingsideFeed.php');
require_once ('ringside/api/dao/records/RingsideFriend.php');

/**
 * Feed DAO
 */
class Api_Dao_Feed
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $type
	 * @param unknown_type $templatized
	 * @param unknown_type $title
	 * @param unknown_type $titleData
	 * @param unknown_type $body
	 * @param unknown_type $bodyData
	 * @param unknown_type $bodyGeneral
	 * @param unknown_type $author
	 * @param unknown_type $image1
	 * @param unknown_type $image1Link
	 * @param unknown_type $image2
	 * @param unknown_type $image2Link
	 * @param unknown_type $image3
	 * @param unknown_type $image3Link
	 * @param unknown_type $image4
	 * @param unknown_type $image4Link
	 * @param unknown_type $actor
	 * @param unknown_type $targets
	 * @param unknown_type $priority
	 * @return unknown
	 */
	public function createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author = null, $image1 = null, $image1Link = null, $image2 = null, $image2Link = null, $image3 = null, $image3Link = null, $image4 = null, $image4Link = null, $actor = null, $targets = null, $priority = 0)
	{
		$feed = new RingsideFeed();
		$feed->type = $type;
		$feed->templatized = $templatized;
		$feed->title = $title;
		$feed->title_data = $titleData;
		$feed->body = $body;
		$feed->body_data = $bodyData;
		$feed->body_general = $bodyGeneral;
		$feed->author_id = $author;
		$feed->image_1 = $image1;
		$feed->image_1_link = $image1Link;
		$feed->image_2 = $image2;
		$feed->image_2_link = $image2Link;
		$feed->image_3 = $image3;
		$feed->image_3_link = $image3Link;
		$feed->image_4 = $image4;
		$feed->image_4_link = $image4Link;
		$feed->actor_id = $actor;
		$feed->target_ids = $targets;
		$feed->priority = $priority;
		
		return $feed->trySave();
	}
	
	public static function getFeedEntries( $uid = null, $actorid = null, $friends = false, $actions = true, $stories = true ) {
		$q = Doctrine_Query::create();
		if( isset( $actorid ) && !empty( $actorid ) ) {
        	$_whereClause = "feed.actor_id = $actorid";
        }
        else if( isset( $uid ) && !empty( $uid ) ) {
        	if( $friends ) {
        		$_whereClause = "friend.from_id = '$uid' and feed.author_id = friend.to_id";
        	}
        	else {
                if( !empty( $_whereClause ) ) {
                    $_whereClause .= ' and ';
                }
                $_whereClause = "feed.author_id = '$uid'";
        	}
		}
		if( !$actions || !$stories ) {
			$_type = ( empty( $actions ) ) ? '1' : '2';
    		$_whereClause .= ' and feed.type = ' . $_type;
		}
//		error_log( 'where clause: ' . $_whereClause );
		$q->select( self::getSelect( $friends ) )->from( self::getFrom( $uid, $actorid, $friends ) )->where( $_whereClause )->orderby( 'feed.created desc' );
        return $q->execute()->toArray( true );
	}
    
    private function getSelect( $friends ) {
        $_selectClause = 'feed.*';
//        error_log( 'select clause: ' . $_selectClause );
        return $_selectClause;
    }
	
	private function getFrom( $uid, $actorid, $friends ) {
        $_fromClause = 'RingsideFeed feed';
        if( isset( $uid ) && !empty( $uid ) ) {
            if( $friends ) {
                $_fromClause .= ', RingsideFriend friend';
            }
        }
//        error_log( 'from clause: ' . $_fromClause );
        return $_fromClause;
	}
}
?>
