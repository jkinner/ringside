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

require_once( 'ringside/social/dsl/RingsideSocialDslParser.php' );
require_once( 'MockApplication.php' );
require_once( 'MockClient.php' );
include_once( 'ringside/social/RingsideSocialUtils.php');
include_once( 'ringside/social/config/RingsideSocialConfig.php');

class fbCommentsTestCase extends PHPUnit_Framework_TestCase {

   /**
    * Builds an array of inputs that mirrors the parameters of the fb:comments tag (see FBML documentation for
    * fb:comments).
    *
    * @return the two inputs to the test method.
    */
	public static function providerTestFbCommentsBasic() {		
		//basic
   		$inputs1 = array( 'xid', 'true', 'true', 10, null, null, null, null, null );

   		//testing canpost
   		$inputs2 = array( 'xid', 'true', 'true', 10, null, null, 'false', null, null );

   		//testing showform
   		$inputs3 = array( 'xid', 'true', 'true', 10, null, null, 'true', null, null );
   		
   		//testing title
   		$inputs4 = array( 'xid', 'true', 'true', 10, null, null, null, null, 'Thoughts about U2' );
   		
	  	return array(
	  		array( null, self::buildCase( $inputs1 ), self::makeExpectedResultsDivs( $inputs1, null, 12345 ) ),
	  		array( null, self::buildCase( $inputs2 ), self::makeExpectedResultsDivs( $inputs2, null, 12345 ) ),
	  		array( null, self::buildCase( $inputs3 ), self::makeExpectedResultsDivs( $inputs3, null, 12345 ) ),
	  		array( null, self::buildCase( $inputs4 ), self::makeExpectedResultsDivs( $inputs4, null, 12345 ) )
	  	);
	}

   /**
    * Builds an array of inputs that mirrors the parameters of the fb:comments tag (see FBML documentation for
    * fb:comments).
    *
    * @return the two inputs to the test method.
    */
	public static function providerTestFbCommentsAdvanced() {   		
   		//testing candelete
   		$inputs5 = array( 'xid', 'true', 'true', 10, null, null, null, null, null );
   		$comments5 = array( array( 'uid'=>33, 'cid'=>3300, 'text'=>'message in a bottle', 'created'=>time() ) );
   		$mockResults5 = array( 'ringside.comments.get'=>$comments5 );
   		
   		//testing callbackurl
   		$inputs7 = array( 'xid', 'true', 'true', 2, 'http://apps.ringsidenetworks.com/myapp', null, 'true', null, null );
   		$comments7 = array( array( 'uid'=>44, 'cid'=>4400, 'text'=>'msg 1', 'created'=>time() ),
   			 array( 'uid'=>44, 'cid'=>4401, 'text'=>'msg 2', 'created'=>time() ),
   			 array( 'uid'=>44, 'cid'=>4402, 'text'=>'msg 3', 'created'=>time() ) );
   		$mockResults7 = array( 'ringside.comments.get'=>$comments7 );
   		
	  	return array(
	  		array( $mockResults5, self::buildCase( $inputs5 ), array( '>delete</a></div>' ), null ),
	  		array( $mockResults7, self::buildCase( $inputs7 ), array( '<input type="hidden" name="callbackurl" value="http://apps.ringsidenetworks.com/myapp"/>' ), null )
	  	);
	}
	
   /**
    * @dataProvider providerTestFbCommentsBasic
    */
   public function testFbCommentsBasic ( $mockMethodResults, $parseString, $expected  ) {
		$ma = new MockApplication();
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        $ma->applicationId = '12345';
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( $expected, trim($results), "$expected != $results" );
   }
	   
   /**
    * @dataProvider providerTestFbCommentsAdvanced
    */
   public function testFbCommentsAdvanced ( $mockMethodResults, $parseString, $expectedSubstrings, $notExpectedSubstrings ) {
		$ma = new MockApplication();
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        $ma->applicationId = '12345';
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );

        if( $expectedSubstrings != null ) {
	        foreach( $expectedSubstrings as $substring ) {
	        	$this->assertContains( $substring, $results );
	        }
        }
        
        if( $notExpectedSubstrings != null ) {
	        foreach( $notExpectedSubstrings as $notsubstring ) {
	        	$this->assertNotContains( $notsubstring, $results );
	        }
        }
   }

   public function testFbCommentsNumposts () {
   		$numposts = 2;
   		$inputs = array( 'xid', 'true', 'true', $numposts, null, null, null, null, null );
   		$comments = array( array( 'uid'=>44, 'cid'=>4400, 'text'=>'msg 1', 'created'=>time() ),
   			 array( 'uid'=>44, 'cid'=>4401, 'text'=>'msg 2', 'created'=>time() ),
   			 array( 'uid'=>44, 'cid'=>4402, 'text'=>'msg 3', 'created'=>time() ) );
   		$mockResults = array( 'ringside.comments.get'=>$comments );
   	
   		$ma = new MockApplication();
        $ma->client = new MockClient();
        $ma->client->method = $mockResults;
        $ma->applicationId = '12345';
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( self::buildCase( $inputs ) );

        $count = substr_count( $results, '<div class="comment">' );
        $this->assertTrue( $count == 2 );
   }
   
   public static function buildCase( $inputs ) {
   		$xid = $inputs[0];
   		$canpost = $inputs[1];
   		$candelete = $inputs[2];
   		$numposts = $inputs[3];
   		$callbackurl = $inputs[4];
   		$returnurl = $inputs[5];
   		$showform = $inputs[6];
   		$uid = $inputs[7];
   		$title = $inputs[8];
   		
   		$case = '<fb:comments xid="' . $xid . '" ';
   		$case .= '	canpost="' . $canpost . '"';
   		$case .= '	candelete="' . $candelete . '"';
   		$case .= '	numposts="' . $numposts . '"';
   		if( isset( $callbackurl ) && !empty( $callbackurl ) ) {
	   		$case .= '	callbackurl="' . $callbackurl . '"';   			
   		}
   		if( isset( $returnurl ) && !empty( $returnurl ) ) {
   			$case .= '	returnurl="' . $returnurl . '"';
   		}
   		if( isset( $showform ) && !empty( $showform ) ) {
	   		$case .= '	showform="' . $showform . '"';
   		}
   		if( isset( $uid ) && !empty( $uid ) ) {
   			$case .= '	send_notification_uid="' . $uid . '"';
   		}

   		$case .= '>';
   		
   		if( isset( $title ) && !empty( $title ) ) {
	   		$case .= '<fb:title>' . $title . '</fb:title>';
   		}
   		
   		$case .= '</fb:comments>';
   		return $case;
   }
   
   /**
    * Builds the expected results, emitting divs.
    *
    * @param $inputs Array containing fb:comments parameters.
    * @param $comments Array of mock comments
    * @return string Expected results
    */
   public static function makeExpectedResultsDivs( $inputs, $comments, $aid ) {
   		$xid = $inputs[0];
   		$canpost = $inputs[1];
   		$candelete = $inputs[2];
   		$numposts = $inputs[3];
   		$callbackurl = $inputs[4];
   		$returnurl = $inputs[5];
   		$showform = ( isset( $inputs[6] ) ? $inputs[6] : 'false' );
   		$uid = $inputs[7];
   		$title = $inputs[8];

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
   		
  		$expected = '<div class="comments">';
  		
   		//title
   		if(!isset( $title ) || empty( $title )) {
	   		$expected .= '    <div class="comments_title">Comments</div>';
   		}
   		else $expected .= '    <div class="comments_title">' . $title . '</div>';
   		
   		//number of comments
   		if( !isset( $comments ) || empty( $comments ) ) {
   			$expected .= '    <div class="comments_numposts">There are no posts yet.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   			$expected .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$expected .= '&r_url=' . $callbackurl;
	   			}
	   			$expected .= '">Write Something</a>';
				$expected .= '</div>';
   			}
   		}
   		else if( sizeof( $comments ) === 1 ) {
   			$expected .= '    <div class="comments_numposts">Displaying the only post.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   			$expected .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$expected .= '&r_url=' . $callbackurl;
	   			}
	   			$expected .= '">Write Something</a>';
   				$expected .= '</div>';
   			}
   		}
   		else if( sizeof( $comments ) > 0 && sizeof( $comments ) < $numposts ) {
   			$expected .= '    <div class="comments_numposts">Displaying all ' . sizeof( $comments ) . ' posts.</div>';
   			if( $canpost == 'true' && $showform == 'false' ) {
	   				   			$expected .= '<div class="comments_top_links"><a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$expected .= '&r_url=' . $callbackurl;
	   			}
	   			$expected .= '">Write Something</a>';
   				$expected .= '</div>';
   			}
   		}
   		else {
   			$expected .= '    <div class="comments_numposts">Displaying ' . $numposts . ' of ' . sizeof( $comments ) . '.</div>';
   			$expected .= '<div class="comments_top_links">';
   			if( $canpost == 'true' && $showform == 'false' ) {
   				$expected .= '<a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '&sig=' . $params['sig'];
	   			if( !empty( $callbackurl ) && isset( $callbackurl ) ) {
	   				$expected .= '&r_url=' . $callbackurl;
	   			}
   				
   				$expected .= '">Write Something</a>&nbsp;&nbsp;';
   			}
   			$expected .= '<a href="' . RingsideSocialConfig::$webRoot . '/wall.php?xid=' . $xid . '&aid=' . $aid . '">See All</a>';
   			$expected .= '</div>';
   		}
   		
   		self::handleShowForm( $showform, $expected, $xid, $aid, $callbackurl, $params['sig'] );
   		
   		//comments
   		$currentCount = 0;
   		if( isset( $comments ) && !empty( $comments ) ) {
	   		foreach( $comments as $comment ) {
   				$params['xid_action'] = 'delete';
   				$params['cid'] = $comment['cid'];
	   			$paramString = http_build_query( $params, '', '&' );
	   			if( $currentCount < $numposts ) {
	   				$expected .= '	<div class="comment">';
	   				$expected .= '		<div class="comment_author">' . $uid . ' wrote</div>';
			   		$time = $comment['created'];
			   		$expected .= '		<div class="comment_time">at ' . $time . '</div>';
			   		$expected .= '		<div class="comment_text">' . $comment['text'] . '</div>';
			   		$expected .= '		<div class="comment_links"><a href="#">message</a>';
			   		if( isset( $candelete ) && ( $candelete == 'true' ) ) {
			   			$expected .= '  -  <a href="' . RingsideSocialConfig::$webRoot . '/wall.php?'.$paramString.'">delete</a></div>';
			   		}
			   		$expected .= '	</div>';
			   		$currentCount++;
	   			}
	   		}
   		}
   		
   		$expected .= '</div>';
   		return $expected;
   }

   public static function handleShowForm( $showform, &$expected, $xid, $aid, $callbackurl, $sig ) {
   	
	   //showform
	   if( $showform == 'true' ) {
		   $expected .= '	<div class="comments_post_form">';
		   $expected .= '	<form name="form1" id="form1" method="get" action="' . RingsideSocialConfig::$webRoot . '/wall.php">';
		   $expected .= '		<input type="hidden" name="xid" value="' . $xid . '"/>';
		   $expected .= '		<input type="hidden" name="xid_action" value="post"/>';
		   $expected .= '		<input type="hidden" name="aid" value="' . $aid . '"/>';
	   	   $expected .= '		<input type="hidden" name="sig" value="' . $sig . '"/>';
		   if( !empty( $callbackurl ) ) {
		   		$expected .= '		<input type="hidden" name="callbackurl" value="' . $callbackurl . '"/>';
	   		}
		   $expected .= '  	<div class="comments_text_box"><textarea class="comments_text_area" name="text" cols="80"></textarea></div>';
		   $expected .= '     	<br/>';
		   $expected .= '     	<div class="comments_submit_button"><input type="submit" name="Submit" value="Post" /></div>';
		   $expected .= '	</form>';
		   $expected .= '	</div>';
		}
   }
   
}
?>
