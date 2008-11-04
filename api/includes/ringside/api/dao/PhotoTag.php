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
require_once ('ringside/api/dao/records/RingsidePhotoTag.php');

/**
 * Represents a row in the OpenFB photo tag table.
 */
class Api_Dao_PhotoTag
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pids
	 * @return unknown
	 */
	public static function getPhotoTags($pids)
	{
		$inPart = implode(",", $pids);
		$q = Doctrine_Query::create();
		$q->select('pid, subject_id, xcoord, ycoord, created')
			->from('RingsidePhotoTag pt')
			->where("pid IN ($inPart)");
		return $q->execute();
	}
	
	/**
	 * Create a Photo Tag
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $subjectId
	 * @param unknown_type $text
	 * @param unknown_type $xcoord
	 * @param unknown_type $ycoord
	 * @param unknown_type $created
	 * @return ID on success, false on failure
	 */
	public static function createPhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, $created = null)
	{
		$tag = new RingsidePhotoTag();
		$tag->pid = $pid;
		$tag->subject_id = $subjectId;
		$tag->text = $text;
		$tag->xcoord = $xcoord;
		$tag->ycoord = $ycoord;
		$ret = $tag->trySave();

		if($ret)
		{
			return $tag->getIncremented();
		}
		
		return false;
	}
	
	/**
	 * Deletes the photo tag with the given id
	 *
	 * @param unknown_type $ptid
	 */
	public function deletePhotoTag($ptid)
	{
		$q = Doctrine_Query::create();
		$q->delete()->from('RingsidePhotoTag pt')->where("ptid=$ptid");
		$q->execute();
	}
}
?>
