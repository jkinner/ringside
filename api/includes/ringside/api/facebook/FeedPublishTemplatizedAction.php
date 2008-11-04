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
	 * Publish a templatized action, which is of the same kind
	 * as publishing a normal action but through a template which
	 * allows for some of aggregation.   If X friends publish the same
	 * message it can now deduce this and display the aggregate in users
	 * view.   It uses the same schema for stories, actions and templatized actions. 
	 *
	 * @author Richard Friedman
	 */
	class FeedPublishTemplatizedAction extends Api_DefaultRest
	{
		private $m_appID;
		
		/**
		 * Validate Request
		 */
		public function validateRequest()
		{

		   $this->checkRequiredParam( 'title_template' );

		   if( $this->getSessionValue('test') != null )
		   {
		      $expectedParams = array('title_template', 'title_data', 'body_template', 'body_data', 'body_general', 'page_actor_id', 'target_ids', 'image_1', 'image_1_link', 'image_2', 'image_2_link', 'image_3', 'image_3_link', 'image_4', 'image_4_link');
		      foreach(array_keys($this->getApiParams()) as $key)
		      {
		         if(! in_array($key, $expectedParams))
		         {
		            throw new Exception(FB_ERROR_MSG_INCORRECT_SIGNATURE . " parameter($key)", FB_ERROR_CODE_INCORRECT_SIGNATURE);
		         }
		      }
		   }
		}

		
		/**
		 * @return Result can be 0 or 1.
		 */
		public function execute()
		{
			$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
			$templatized = Api_Bo_Feed::RS_FBDB_FEED_TEMPLATE_WITH;
			$author = $this->getUserId();
			$title = $this->getApiParam('title_template');
			$body = $this->getApiParam('body_template');
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
			$targets = $this->getApiParam('target_ids');
			$actor = $this->getApiParam('page_actor_id');

//            error_log( 'title data: ' . $titleData );
//            error_log( 'body title: ' . $bodyTitle );
//            error_log( 'body data: ' . $bodyData );
//            error_log( 'body general: ' . $bodyGeneral );
//            error_log( 'page actor id: ' . $actor );
			
			$ret = Api_Bo_Feed::createTemplatizedFeed($this->getUserId(), $this->getAppId(),$type, $templatized, $author, $title, $body, $image1, $image1Link, $image2, $image2Link, 
				$image3, $image3Link, $image4, $image4Link, $titleData, $bodyData, $bodyGeneral, $priority, $targets,
				$actor);
					
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