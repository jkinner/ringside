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
require_once ('ringside/api/dao/records/RingsideAlbum.php');
/**
 * Represents a row in the OpenFB album table.
 */
class Api_Dao_Album
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aids
	 * @return unknown
	 */
	public static function getAlbumsByAids($aids)
	{
		$inPart = implode(",", $aids);
		$q = Doctrine_Query::create();
		$q->select('aid , cover_pid, owner, name, created, modified, description, location')
			->from('RingsideAlbum n')
			->where("aid IN ($inPart)");
		return $q->execute();
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function getAlbumsByUid($uid)
	{
		$q = Doctrine_Query::create();
		$q->select('aid , cover_pid, owner, name, created, modified, description, location')->from('RingsideAlbum n')->where("owner = $uid");
		return $q->execute();
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aids
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function getAlbumsByAidsAndUid($aids, $uid)
	{
		$inPart = implode(",", $aids);
		$q = Doctrine_Query::create();
		$q->select('aid , cover_pid, owner, name, created, modified, description, location')->from('RingsideAlbum n')->where("owner = $uid AND aid IN ($inPart)");
		return $q->execute();
	}
	/**
	 * Creates an album
	 *
	 * @param unknown_type $cover_pid
	 * @param unknown_type $created
	 * @param unknown_type $description
	 * @param unknown_type $location
	 * @param unknown_type $modified
	 * @param unknown_type $name
	 * @param unknown_type $owner
	 */
	public static function createAlbum($cover_pid, $description, $location, $name, $owner)
	{
		$album = new RingsideAlbum();
		$album->cover_pid = $cover_pid;
		$album->description = $description;
		$album->location = $location;
		$album->name = $name;
		$album->owner = $owner;
		$ret = $album->trySave();
		
		if($ret)
		{
			return $album->getIncremented();
		}
		return false;
	}
	/**
	 * Deletes an Album
	 *
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function delete($aid)
	{
		$q = new Doctrine_Query();
		return $q->delete('RingsideAlbum')->from('RingsideAlbum a')->where("aid=$aid")->execute();
	}
}
?>
