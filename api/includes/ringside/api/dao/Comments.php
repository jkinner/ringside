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
require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideComment.php');

/**
 * The DB layer for everything COMMENT related.
 *
 * @author Richard Friedman
 * ringside/api/db/CommentsDb.php contains class ApiCommentsDb
 */
class Api_Dao_Comments
{

	/**
	 * Gets the comment count for the given xid and aid.
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 */
	public static function getCommentCount($xid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(cid) as cid_count')->from('RingsideComment c')->where("xid = '$xid' AND aid = $aid");
		
		$comments = $q->execute();
		return $comments[0]['cid_count'];
	}

	/**
	 * Gets the comments, but uses a limit offset query if first and count are passed in
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @param unknown_type $first
	 * @param unknown_type $count
	 * @return unknown
	 */
	public static function getCommentsByXidAndAid($xid, $aid, $first = null, $max = null)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideComment c')->where("xid = '$xid' AND aid = $aid")->orderby('cid DESC');
		
		if($first !== null)
		{
			$q->offset("$first");
		}
		
		if($max !== null)
		{
			$q->limit("$max");
		}
		
		return $q->execute();
	}

	/**
	 * Creates a Comment
	 *
	 * @param unknown_type $xid
	 * @param unknown_type $cid
	 * @param unknown_type $aid
	 * @param unknown_type $uid
	 * @param unknown_type $text
	 * @return unknown
	 */
	public static function createComment($xid, $aid, $uid, $text)
	{
		$comment = new RingsideComment();
		$comment->xid = $xid;
		$comment->aid = $aid;
		$comment->uid = $uid;
		$comment->text = $text;
		
		// Use trySave because it does not throw an exception upon failure, instead returns true on success fals on failure
		$ret = $comment->trySave();
		if($ret)
		{
			return $comment->getIncremented();
		}
		
		return false;
	}

	/**
	 * Deletes Comments
	 *
	 * @param unknown_type $cid
	 * @param unknown_type $xid
	 * @param unknown_type $aid
	 * @return Number of Rows Deleted
	 */
	public static function deleteComment($cid, $xid, $aid)
	{
		$q = new Doctrine_Query();
		
		return $q->delete('RingsideComment')->from('RingsideComment c')->where("cid=$cid AND xid='$xid' AND aid=$aid")->execute();
	}
}
?>
