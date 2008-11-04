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
include_once( 'ringside/social/RingsideSocialUtils.php');
include_once( 'ringside/social/config/RingsideSocialConfig.php');

class fbCommentsHandler {

   private $title;
   
   function doStartTag($application, &$parentHandler, $args ) {
  		echo '<div class="comments">';
   		return 'fb:title';
   }

   function doBody( $application, $parentHandler, $args ) {
		echo '    <div class="comments_title">' . ( isset( $this->title ) ? $this->title : 'Comments' ) . '</div>';
   }
   
   function doEndTag( $application, $parentHandler, $args ) {
   		$this->emitDivs( $application, $parentHandler, $args );
   }
   
   /**
    * Emits form and comments as divs.
    */
   public function emitDivs( $application, $parentHandler, $args ) {
   		$xid = $args['xid'];
   		$canpost = isset( $args['canpost'] ) ? $args['canpost'] : "false";
   		$candelete = isset( $args['candelete']) ? $args['candelete'] : "false";
   		$numposts = ( isset( $args['numposts'] ) ) ? $args['numposts'] : 10;
   		$uid = $application->getCurrentUser();
   		$aid = isset( $args['aid'] ) ? $args['aid'] : $application->getApplicationId();
   		
   		$callbackurl = isset( $args['callbackurl'] ) ? $args['callbackurl'] : '';
   		$returnurl = isset( $args['returnurl'] ) ? $args['returnurl'] : '';
   		$showform = isset( $args['showform'] ) ? $args['showform'] : 'false';

   		$client = $application->getClient();
   		$comments = $client->comments_get( $xid, null, null, $aid );
   		
   		$params = array();
   		$params['xid'] = $xid;
   		if ( !empty( $callbackurl ) )  {
   			$params['c_url'] = $callbackurl;
   		}
   		if ( !empty( $returnurl ) )  {
   			$params['r_url'] = $returnurl;
   		}
   		$params['aid'] = $aid;
   		$params['sig'] = RingsideSocialUtils::makeSig( $params, RingsideSocialConfig::$secretKey );
   		
   		//number of comments
   		$theString = "";
   		if( !isset( $comments ) || empty( $comments ) ) {
   			$theString .= '    <div class="comments_numposts">There are no posts yet.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   			$theString .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$theString .= '&r_url=' . $callbackurl;
	   			}
	   			$theString .= '">Write Something</a>';
   				$theString .= '</div>';
   			}
   		}
   		else if( sizeof( $comments ) === 1 ) {
   			$theString .= '    <div class="comments_numposts">Displaying the only post.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   			$theString .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$theString .= '&r_url=' . $callbackurl;
	   			}
	   			$theString .= '">Write Something</a>';
   				$theString .= '</div>';
   			}
   		}
   		else if( sizeof( $comments ) > 0 && sizeof( $comments ) < $numposts ) {
   			$theString .= '    <div class="comments_numposts">Displaying all ' . sizeof( $comments ) . ' posts.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   			$theString .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$theString .= '&r_url=' . $callbackurl;
	   			}
	   			$theString .= '">Write Something</a>';
   				$theString .= '</div>';
   			}
   		}
   		else {
   			$theString .= '    <div class="comments_numposts">Displaying ' . $numposts . ' of ' . sizeof( $comments ) . '.</div>';
   			$theString .= '<div class="comments_top_links">';
   			if( $canpost == 'true' && $showform == 'false' ) {
   				$theString .= '<a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$theString .= '&r_url=' . $callbackurl;
	   			}
   				$theString .= '">Write Something</a>&nbsp;&nbsp;';
   			}
   			$theString .= '<a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '">See All</a>';
   			$theString .= '</div>';
   		}
   		
   		//showform
   		if( $showform == 'true' ) {
   			$theString .= '	<div class="comments_post_form">';
	   		$theString .= '	<form name="form1" id="form1" method="get" action="' . RingsideSocialConfig::$webRoot . '/wall.php">';	   		
	   		$theString .= '		<input type="hidden" name="xid" value="' . $xid . '"/>';
	   		$theString .= '		<input type="hidden" name="xid_action" value="post"/>';
	   		$theString .= '		<input type="hidden" name="aid" value="' . $aid . '"/>';
	   		$theString .= '		<input type="hidden" name="sig" value="' . $params['sig'] . '"/>';
	   		if( !empty( $callbackurl ) ) {
		   		$theString .= '		<input type="hidden" name="callbackurl" value="' . $callbackurl . '"/>';
	   		}
	   		$theString .= '  	<div class="comments_text_box"><textarea class="comments_text_area" name="text" cols="80"></textarea></div>';
	   		$theString .= '     	<br/>';
	   		$theString .= '     	<div class="comments_submit_button"><input type="submit" name="Submit" value="Post" /></div>';
	   		$theString .= '	</form>';
	   		$theString .= '	</div>';
   		}
           		
   		//comments
   		$currentCount = 0;
   		if( isset( $comments ) && !empty( $comments ) ) {
   			foreach( $comments as $comment ) {
   				$params['xid_action'] = 'delete';
   				$params['cid'] = $comment['cid'];
	   			$paramString = http_build_query( $params, '', '&' );
	   			if( $currentCount < $numposts ) {
	   				$theString .= '	<div class="comment">';
	   				
	   				$name = $client->users_getInfo( $comment['uid'], "first_name,pic" );
	   				$theString .= '		<div class="comment_author_pic"><image src="' . $name[0]['pic'] . '" width="50"/></div>';
	   				$theString .= '		<div class="comment_author">' . $name[0]['first_name'] . ' wrote</div>';

	   				$time = $comment['created'];
			   		$theString .= '		<div class="comment_time">at ' . $time . '</div>';
			   		$theString .= '		<div class="comment_text">' . $comment['text'] . '</div>';
			   		$theString .= '		<div class="comment_links"><a href="#">message</a>';
			   		if( isset( $candelete ) && ( $candelete == 'true' ) ) {
			   			$theString .= '  -  <a href="' . RingsideSocialConfig::$webRoot . '/wall.php?'.$paramString.'">delete</a></div>';
			   		}
			   		$theString .= '	</div>';
			   		$currentCount++;
	   			}
	   		}
   		}
   		
   		$theString .= '</div>';
   		echo $theString;
   }

   private function handleShowForm( $showform, &$expected, $xid, $aid ) {
   	
	   if( $showform == 'true' ) {
		   $expected .= '	<div class="comments_post_form">';
		   $expected .= '	<form name="form1" id="form1" method="get" action="/ringside/wall.php">';
		   $expected .= '		<input type="hidden" name="xid" value="' . $xid . '"/>';
		   $expected .= '		<input type="hidden" name="xid_action" value="post"/>';
		   $expected .= '		<input type="hidden" name="aid" value="' . $aid . '"/>';
		   $expected .= '  	<div class="comments_text_box"><textarea class="comments_text_area" name="text" cols="80"></textarea></div>';
		   $expected .= '     	<br/>';
		   $expected .= '     	<div class="comments_submit_button"><input type="submit" name="Submit" value="Post" /></div>';
		   $expected .= '	</form>';
		   $expected .= '	</div>';
		}
   }
   
   function setTitle( $title ) {
   		if( !isset( $this->title ) ) {
   			$this->title = $title;
   		}
   }
   
	function getType()
   	{
   		return 'block';   	
   	}
}

?>
