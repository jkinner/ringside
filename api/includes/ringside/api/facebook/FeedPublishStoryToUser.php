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

require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Feed.php");

/**
 * Publisht the story to user, in reality this and publish action of
 * are identical.  They currently write to same db table, since the
 * fields are identical.  The difference is in how the front end chooses
 * to interact and display the mechanism of it. 
 *
 * @author Richard Friedman
 */
class FeedPublishStoryToUser extends Api_DefaultRest
{
	
	private $feed;
	
	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		
		$this->checkRequiredParam( 'title' );
		
		if( $this->getSessionValue('test') != null )
		{
			$expectedParams = array('title', 'body', 'image_1', 'image_1_link', 'image_2', 'image_2_link', 'image_3', 'image_3_link', 'image_4', 'image_4_link');
			foreach(array_keys($this->getApiParams()) as $key)
				if(! in_array($key, $expectedParams))
					throw new Exception(FB_ERROR_MSG_INCORRECT_SIGNATURE . " parameter($key)", FB_ERROR_CODE_INCORRECT_SIGNATURE);
		}
	
	}
	
	/**
	 * @return Result can be 0 or 1.
	 */
	public function execute()
	{
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_STORY;
		$templatized = Api_Bo_Feed::RS_FBDB_FEED_NOT_TEMPLATE;
		$author = $this->getUserId();
		$title = $this->getApiParam('title');
		$body = $this->getApiParam('body');
		$image1 = $this->getApiParam('image_1');
		$image1Link = $this->getApiParam('image_1_link');
		$image2 = $this->getApiParam('image_2');
		$image2Link = $this->getApiParam('image_2_link');
		$image3 = $this->getApiParam('image_3');
		$image3Link = $this->getApiParam('image_3_link');
		$image4 = $this->getApiParam('image_4');
		$image4Link = $this->getApiParam('image_4_link');
		$titleData = $this->getApiParam('title_data');
		$bodyData = $this->getApiParam('body_data');
		$bodyGeneral = $this->getApiParam('body_general');
		$priority = $this->getApiParam('priority');
		$actor = $this->getApiParam('actor_id');
		$targets = $this->getApiParam('targets');
		
		if(null == $priority || strlen($priority) == 0)
		{
			$priority = 0;
		}
		
		$ret = Api_Bo_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, $priority);
		
		$response = array();
		if($ret)
		{
			$response ['result'] = '1';
		}else
		{
			$response ['result'] = '0';
		}
		return $response;
	}
}

?>
