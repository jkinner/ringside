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
require_once ("ringside/api/bo/Photos.php");

/**
 * Photos.getTags API
 */
class PhotosGetTags extends Api_DefaultRest
{
	/** Array of pids */
	private $m_pids;

	public function validateRequest()
	{
		$buf = $this->getRequiredApiParam('pids');
		if(strcmp(trim($buf), "") == 0)
		{
			throw new OpenFBAPIException("No pids specified.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		$this->m_pids = explode(',', $buf);
	}

	/**
	 * Execute the Photos.getTags method
	 *
	 * @return The information about the retrieved albums.  This is returned in an associative aray:
	 *  array( 
	 *     'photo_tag' => array(
	 *        array( 'pid'=>1, 'subject'=>2, 'xcoord'=>12.3423, 'ycoord'=>13.5435, 'created'=>1543142324 ),
	 *        array( 'pid'=>2, 'subject'=>6, 'xcoord'=>12.3423, 'ycoord'=>13.5435, 'created'=>1543142324 ),
	 *        array( 'pid'=>3, 'subject'=>8, 'xcoord'=>12.3423, 'ycoord'=>13.5435, 'created'=>1543142324 )
	 *      )
	 * )
	 */
	public function execute()
	{
		$tags = Api_Bo_Photos::getPhotoTags($this->m_pids);
		
		$retVal = array();
		$retVal[FB_PHOTOS_PHOTO_TAG] = array();
		
		$i = 0;
		foreach($tags as $tag)
		{
			$retVal[FB_PHOTOS_PHOTO_TAG][$i] = array(FB_PHOTOS_PID => $tag['pid'], FB_PHOTOS_SUBJECT => $tag['subject_id'], FB_PHOTOS_XCOORD => $tag['xcoord'], FB_PHOTOS_YCOORD => $tag['ycoord'], FB_PHOTOS_CREATED => $tParsedTime = strtotime($tag['created'] . ' GMT'));
			$i ++;
		}
		
		return $retVal;
	}
}
?>
