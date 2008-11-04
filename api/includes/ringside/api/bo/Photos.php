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
require_once ('ringside/api/OpenFBAPIException.php');
require_once ('ringside/api/dao/Photo.php');
require_once ('ringside/api/dao/PhotoTag.php');
require_once ('ringside/api/dao/Album.php');

/**
 * @author mlugert@ringsidenetworks.com
 * Contains logic for
 * Photos
 * PhotoTags
 */
class Api_Bo_Photos
{
    /**
     * @param $pastTimestamp all photos added after this time are counted (defaults to all time)
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return int total number of photos in the system 
     */
    public static function getTotalCountOfPhotos($pastTimestamp = null)
    {
        $q = Doctrine_Query::create();
        $q->select('count(p.pid) count')
          ->from('RingsidePhoto p');
        if ($pastTimestamp != null)
        {
            $q->where('created > ?', $pastTimestamp);
        }
        $_results = $q->execute();
        return $_results[0]['count'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $pids
     * @return unknown
     */
    public static function getPhotoTags($pids)
    {
        return Api_Dao_PhotoTag::getPhotoTags($pids)->toArray();
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $pid
     * @param unknown_type $subjectId
     * @param unknown_type $text
     * @param unknown_type $xcoord
     * @param unknown_type $ycoord
     * @param unknown_type $created
     * @return unknown
     */
    public static function createPhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, $created = null)
    {
        return Api_Dao_PhotoTag::createPhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, $created);
    }

    /**
     * Returns the number of photos for the album ID
     *
     * @param unknown_type $aid
     */
    public static function getNumberOfPhotots($aid)
    {
        return Api_Dao_Photo::getPhotoCountByAlbumId($aid);
    }

    /**
     * One or more of the following fields is required:
     * Photo ID
     * Album ID
     * Subject ID
     *
     * This function figures out which combo should be used to query the DB and then
     * calls the correct DAO function.
     *
     * @param unknown_type $pids
     * @param unknown_type $album_id
     * @param unknown_type $subject_id
     */
    public static function getPhotos($pids = array(), $albumId = null, $subjectId = null)
    {
        $pidCount = count($pids);
        if($pidCount > 0 && $albumId === null && $subjectId === null)
        {
            return Api_Dao_Photo::getPhotosByPids($pids)->toArray();
        }else if($pidCount == 0 && $albumId !== null && $subjectId === null)
        {
            return Api_Dao_Photo::getPhotosByAlbumId($albumId)->toArray();
        }else if($pidCount == 0 && $albumId === null && $subjectId !== null)
        {
            return Api_Dao_Photo::getPhotosBySubjectId($subjectId)->toArray();
        }else if($pidCount > 0 && $albumId !== null && $subjectId === null)
        {
            return Api_Dao_Photo::getPhotosByPidsAndAlbumId($pids, $albumId)->toArray();
        }else if($pidCount > 0 && $albumId === null && $subjectId !== null)
        {
            return Api_Dao_Photo::getPhotosByPidsAndSubjectId($pids, $subjectId)->toArray();
        }else if($pidCount == 0 && $albumId !== null && $subjectId !== null)
        {
            return Api_Dao_Photo::getPhotosByAlbumIdAndSubjectId($albumId, $subjectId)->toArray();
        }else if($pidCount > 0 && $albumId !== null && $subjectId !== null)
        {
            return Api_Dao_Photo::getPhotosByPidsAndAlbumIdAndSubjectId($pids, $albumId, $subjectId)->toArray();
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $aids
     * @param unknown_type $uid
     * @return unknown
     */
    public static function getAlbums($aids, $uid)
    {
        if(strcmp("", $uid) == 0)
        {
            if(count($aids) == 0)
            {
                throw new Exception('Both uid and aids were not specified.', FB_ERROR_CODE_INCORRECT_SIGNATURE);
            }else
            {
                return Api_Dao_Album::getAlbumsByAids($aids)->toArray();
            }
        }else
        {
            if(count($aids) != 0)
            {
                return Api_Dao_Album::getAlbumsByAidsAndUid($aids, $uid)->toArray();
            }else
            {
                return Api_Dao_Album::getAlbumsByUid($uid)->toArray();
            }
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $cover_pid
     * @param unknown_type $created
     * @param unknown_type $description
     * @param unknown_type $location
     * @param unknown_type $modified
     * @param unknown_type $name
     * @param unknown_type $owner
     * @return unknown
     */
    public static function createAlbum($cover_pid, $description, $location, $name, $owner)
    {
        return Api_Dao_Album::createAlbum($cover_pid, $description, $location, $name, $owner);
    }

    /**
     * Create a photo album link
     *
     * @param unknown_type $albumId The album id.
     * @param unknown_type $userId The user id of the album owner.
     * @return unknown The http link to the album.
     */
    public static function createAlbumLink($albumId, $userId)
    {
        return "http://www.ringside.com/album.php?aid=" . $albumId . "&id=" . $userId;

    }
}

?>
