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
require_once 'ringside/api/facebook/OpenFBConstants.php';
require_once 'ringside/api/OpenFBAPIException.php';
require_once 'ringside/api/DefaultRest.php';
require_once 'ringside/api/bo/Photos.php';
/**
 * Photos.getAlbums API
 */
class PhotosGetAlbums extends Api_DefaultRest
{
	/** The user id of the albums to get. */
	private $m_uid;
	/** Array of album ids */
	private $m_aids;

	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam('uid', '');
		$buf = $this->getApiParam('aids', '');
		if(strlen($buf) == 0)
		{
			$this->m_aids = array();
		}else
		{
			$this->m_aids = explode(',', $buf);
		}
		if(strcmp("", $this->m_uid) == 0)
		{
			if(count($this->m_aids) == 0)
			{
				throw new OpenFBAPIException("Both uid and aids were not specified.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
			}
		}
	}

	/**
	 * Execute the Photos.getAlbums method
	 *
	 * @return The information about the retrieved albums.  This is returned in an associative aray:
	 *  array( 
	 *    'album'=>array( 
	 *        array( 'aid'=>1,
	 *               'cover_pid'=>2, 
	 *               'owner'=>10001, 
	 *               'name'=>'album name',
	 *               'created'=>1245346234,
	 *               'modified'=>1253346234,
	 *               'description'=>'some description',
	 *               'location'=>'London,England',
	 *               'link'=>'http://www.ringside.com/album.php?aid=1&id=10001',
	 *               'size'=>2 ),
	 *        array( 'aid'=>2,
	 *               'cover_pid'=>2356, 
	 *               'owner'=>10001, 
	 *               'name'=>'another album name',
	 *               'created'=>1246546234,
	 *               'modified'=>1453332234,
	 *               'description'=>'another description',
	 *               'location'=>'Paris,France',
	 *               'link'=>'http://www.ringside.com/album.php?aid=2&id=10001',
	 *               'size'=>10 ),
	 *       )
	 *  )
	 */
	public function execute()
	{
		$albums = Api_Bo_Photos::getAlbums($this->m_aids, $this->m_uid);
		$retVal = array();
		$retVal[FB_PHOTOS_ALBUM] = array();
		$i = 0;
		foreach($albums as $album)
		{
			$aid = $album['aid'];
			$link = Api_Bo_Photos::createAlbumLink($aid, $album['owner']);
			$numPhotos = Api_Bo_Photos::getNumberOfPhotots($aid);
			$retVal[FB_PHOTOS_ALBUM][$i ++] = array(FB_PHOTOS_AID => $aid, FB_PHOTOS_COVER_PID => $album['cover_pid'], FB_PHOTOS_OWNER => $album['owner'], FB_PHOTOS_NAME => $album['name'], FB_PHOTOS_CREATED => strtotime($album['created'] . ' GMT'), FB_PHOTOS_MODIFIED => strtotime($album['modified'] . ' GMT'), FB_PHOTOS_DESCRIPTION => $album['description'], FB_PHOTOS_LOCATION => $album['location'], FB_PHOTOS_LINK => $link, FB_PHOTOS_SIZE => $numPhotos);
		}
		return $retVal;
	}

	/**
	 * Get the user id for which the albums are to be retrieved.
	 *
	 * @return unknown The user id for which the albums are to be retrieved.
	 */
	public function getAlbumUserId()
	{
		return $this->m_uid;
	}

	/**
	 * Get the album ids.
	 *
	 * @return unknown The album ids.
	 */
	public function getAids()
	{
		return $this->m_aids;
	}
}
?>
