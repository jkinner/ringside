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
require_once 'ringside/api/dao/Comments.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Comments
{

	/**
	 * Gets the comments with the given xid and aid.  Also, first and max are optional.
	 * 
	 * First will start the result set at that offset
	 * Max will limit the number of rows returned
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @param unknown_type $first
	 * @param unknown_type $max
	 * @return unknown
	 */
	public static function getComments($xid, $aid, $first = null, $max = null)
	{
		return Api_Dao_Comments::getCommentsByXidAndAid($xid, $aid, $first, $max)->toArray();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @param unknown_type $uid
	 * @param unknown_type $text
	 */
	public static function createComment($xid, $aid, $uid, $text)
	{
		return Api_Dao_Comments::createComment($xid, $aid, $uid, $text);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $cid
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function deleteComment($cid, $xid, $aid)
	{
		return Api_Dao_Comments::deleteComment($cid, $xid, $aid);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function getCommentCount($xid, $aid)
	{
		return Api_Dao_Comments::getCommentCount($xid, $aid);
	}
}

?>
