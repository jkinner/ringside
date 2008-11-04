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

require_once ("ringside/api/facebook/OpenFBConstants.php");
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ('ringside/api/bo/Photos.php');

/**
 * Photos.get API
 */
class PhotosGet extends Api_DefaultRest
{
	/** The id of user that a photo may be tagged with. */
	private $m_subjectId;
	
	/** The album id */
	private $m_aid;
	
	/** The picture ids. */
	private $m_pids;

	public function validateRequest()
	{
		
		$subjectId = $this->getApiParam('subj_id');
		$aid = $this->getApiParam('aid');
		$pids = $this->getApiParam('pids');
		if(empty($subjectId) && empty($aid) && empty($pids))
		{
			throw new OpenFBAPIException("One of subj_id, aid, pids must be specified.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
		}
		
		if($subjectId != null && (strlen(trim($subjectId)) > 0))
		{
			$this->m_subjectId = $subjectId;
		}
		
		if($aid != null && (strlen(trim($aid)) > 0))
		{
			$this->m_aid = $aid;
		}
		
		if($pids != null && (strlen(trim($pids)) > 0))
		{
			$buf = $pids;
			$this->m_pids = explode(',', $buf);
			if(count($this->m_pids) == 1 && strcmp("", trim($this->m_pids[0])) == 0)
			{
				throw new OpenFBAPIException("If pids are specified there must be more at least one.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
			}
		}
	}

	/**
	 * Execute the Photos.get method
	 *
	 * @return The information about the retrieved albums.  This is returned in an associative aray:
	 *  array( 
	 *    'photo'=>array( 
	 *        array( 'pid'=>1
	 *               'aid'=>2,
	 *               'owner'=>10001, 
	 *               'src'=>'http://www.ringside.com/231243.jpg',
	 *               'src_big'=>'http://www.ringside.com/231243_345.jpg',
	 *               'src_small'=>'http://www.ringside.com/231243_764.jpg',
	 *               'link'=>'http://www.ringside.com/photo.php?pid=1&id=10001',
	 *               'caption'=>'some caption',
	 *               'created'=>1245346234 ),
	 *        array( 'pid'=>2
	 *               'aid'=>2,
	 *               'owner'=>10001, 
	 *               'src'=>'http://www.ringside.com/2312432.jpg',
	 *               'src_big'=>'http://www.ringside.com/2312432_345.jpg',
	 *               'src_small'=>'http://www.ringside.com/2312432_764.jpg',
	 *               'link'=>'http://www.ringside.com/photo.php?pid=2&id=10001',
	 *               'caption'=>'some caption',
	 *               'created'=>1245346234 ),
	 *       )
	 *  )
	 */
	public function execute()
	{
		$photos = Api_Bo_Photos::getPhotos($this->m_pids, $this->m_aid, $this->m_subjectId);
		
		$retVal = array();
		if(count($photos) > 0)
		{
			$retVal[FB_PHOTOS_PHOTO] = array();
			
			$i = 0;
			foreach($photos as $row)
			{
				$retVal[FB_PHOTOS_PHOTO][$i ++] = array(FB_PHOTOS_PID => $row['pid'], FB_PHOTOS_AID => $row['aid'], FB_PHOTOS_OWNER => $row['owner'], FB_PHOTOS_SRC => $row['src'], FB_PHOTOS_SRC_BIG => $row['src_big'], FB_PHOTOS_SRC_SMALL => $row['src_small'], FB_PHOTOS_LINK => $row['link'], FB_PHOTOS_CAPTION => $row['caption'], FB_PHOTOS_CREATED => strtotime($row['created'] . ' GMT'));
			}
		}
		
		return $retVal;
	}

	/**
	 * Get the user id for which the photos are tagged.
	 *
	 * @return unknown The user id for which the photos are tagged.
	 */
	public function getSubjectId()
	{
		return $this->m_subjectId;
	}

	/**
	 * Get the album id.
	 *
	 * @return unknown The album id.
	 */
	public function getAid()
	{
		return $this->m_aid;
	}

	/**
	 * Get the photo ids.
	 *
	 * @return unknown The photo ids.
	 */
	public function getPids()
	{
		return $this->m_pids;
	}

}
?>
