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
require_once ('ringside/api/dao/records/RingsidePhoto.php');

/**
 * Represents a row in the OpenFB photo table.
 */
class Api_Dao_Photo
{
	/**
	 * Returns photos with the given Photo Ids
	 *
	 * @param unknown_type $pids
	 * @return unknown
	 */
	public static function getPhotosByPids($pids)
	{
		$inPart = implode(",", $pids);
		$q = Doctrine_Query::create();
		$q->select('pid, aid, owner, src, src_big, src_small, link, caption, created')->from('RingsidePhoto p')->where("pid IN ($inPart)");
		return $q->execute();
	}

	/**
	 * Returns photos in the given Album
	 *
	 * @param unknown_type $albumId
	 * @return unknown
	 */
	public static function getPhotosByAlbumId($albumId)
	{
		$q = Doctrine_Query::create();
		$q->select('pid, aid, owner, src, src_big, src_small, link, caption, created')->from('RingsidePhoto p')->where("aid = $albumId");
		return $q->execute();
	}

	/**
	 * Returns photos that are in the given Album with particular Photo Ids
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $albumId
	 * @return unknown
	 */
	public static function getPhotosByPidsAndAlbumId($pids, $albumId)
	{
		$inPart = implode(",", $pids);
		$q = Doctrine_Query::create();
		$q->select('pid, aid, owner, src, src_big, src_small, link, caption, created')->from('RingsidePhoto p')->where("aid = $albumId AND pid IN ($inPart)");
		return $q->execute();
	}
	
	/**
	 * Returns photos by Subject ID on a Photo Tag
	 *
	 * @param unknown_type $subjectId
	 * @return unknown
	 */
	public static function getPhotosBySubjectId($subjectId)
	{
		/*SELECT p.pid, p.aid, p.owner, p.src, p.src_big, p.src_small, p.link, p.caption, p.created
			FROM photo p LEFT JOIN photo_tag pt ON p.pid = pt.pid
			WHERE pt.subject_id = 1*/
		$q = Doctrine_Query::create();
		$q->select('p.pid, p.aid, p.owner, p.src, p.src_big, p.src_small, p.link, p.caption, p.created')
			->from('RingsidePhoto p LEFT JOIN p.RingsidePhotoTag pt ON p.pid = pt.pid')
			->where("pt.subject_id = $subjectId");
		return $q->execute();
	}

	/**
	 * Returns photos with the given Photo Ids and the Photo Tag Subject given
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $subjectId
	 * @return unknown
	 */
	public static function getPhotosByPidsAndSubjectId($pids, $subjectId)
	{
		$inPart = implode(",", $pids);
		$q = Doctrine_Query::create();
		$q->select('p.pid, p.aid, p.owner, p.src, p.src_big, p.src_small, p.link, p.caption, p.created')
			->from('RingsidePhoto p LEFT JOIN p.RingsidePhotoTag pt ON p.pid = pt.pid')
			->where("pt.subject_id = $subjectId AND pid IN ($inPart)");
		return $q->execute();
	}

	/**
	 * Returns photos with the given Album Id and the Photo Tag Subject given
	 *
	 * @param unknown_type $albumId
	 * @param unknown_type $subjectId
	 * @return unknown
	 */
	public static function getPhotosByAlbumIdAndSubjectId($albumId, $subjectId)
	{
		$q = Doctrine_Query::create();
		$q->select('p.pid, p.aid, p.owner, p.src, p.src_big, p.src_small, p.link, p.caption, p.created')
			->from('RingsidePhoto p LEFT JOIN p.RingsidePhotoTag pt ON p.pid = pt.pid')
			->where("pt.subject_id = $subjectId AND aid = $albumId");
		return $q->execute();
	}

	/**
	 * Returns photos with the given Photo Ids, Album Id, and the Photo Tag Subject given
	 *
	 * @param unknown_type $pids
	 * @param unknown_type $albumId
	 * @param unknown_type $subjectId
	 * @return unknown
	 */
	public static function getPhotosByPidsAndAlbumIdAndSubjectId($pids, $albumId, $subjectId)
	{
		$inPart = implode(",", $pids);
		$q = Doctrine_Query::create();
		$q->select('p.pid, p.aid, p.owner, p.src, p.src_big, p.src_small, p.link, p.caption, p.created')
			->from('RingsidePhoto p LEFT JOIN p.RingsidePhotoTag pt ON p.pid = pt.pid')
			->where("pt.subject_id = $subjectId AND aid = $albumId AND pid IN ($inPart)");
		return $q->execute();
	}

	/**
	 * Gets the number of photots by Album ID
	 *
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function getPhotoCountByAlbumId($aid)
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(pid) as pid_count')->from('RingsidePhoto p')->where("aid=$aid");
		$photos = $q->execute();
		return $photos[0]['pid_count'];
	}

	/**
	 * Gets the photo with the given id
	 *
	 * @param int $id
	 * @return Doctrine_Collection
	 */
	public static function getPhotoById($id)
	{
		$q = Doctrine_Query::create();
		$q->select('pid, aid, owner, src_small, src_big, src, link, caption, created')->from('RingsidePhoto p')->where("pid = $id");
		$photos = $q->execute();
		
		if(count($photos) == 1)
		{
			return $photos[0];
		}
		
		return null;
	}

	/**
	 * Insert this object into the database.
	 * @param unknown_type $dbCon The database connection to use to do the insert.
	 * @throws Exception if an error occurs inserting the object into the database.
	 */
	public static function createPhoto($aid, $caption, $link, $owner, $src, $src_big, $src_small)
	{
		$photo = new RingsidePhoto();
		$photo->aid = $aid;
		$photo->caption = $caption;
		$photo->link = $link;
		$photo->owner = $owner;
		$photo->src = $src;
		$photo->src_big = $src_big;
		$photo->src_small = $src_small;
		$ret = $photo->trySave();
		
		if($ret)
		{
			return $photo->getIncremented();
		}
		
		return false;
	}

	/**
	 * Delete this object from the database.
	 * @param unknown_type $dbCon The database connection to use to do the delete.
	 * 
	 * @throws Exception if an error occurs deleting the object from the database.
	 */
	public function deletePhoto($pid)
	{
		
		$q = Doctrine_Query::create();
		$q->delete()->from('RingsidePhoto')->where("pid=$pid");
		$q->execute();
	}
}
?>
