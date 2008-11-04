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

class rsFeedsTestCase extends PHPUnit_Framework_TestCase {

   /**
    * Builds an array of inputs that mirrors the parameters of the rs:payment-plans tag
    *
    * @return the inputs to the test method.
    */
	public static function providerFeeds() {
		$entries1 = array();
        $entry = array( 'type'=>1, 'title'=>'Test entry #1', 'author_id'=> 100000, 'created'=>date( 'Y-m-d h:i:s' ) );
        $entries1[] = $entry;
        
        $case1 = '<rs:feed uid="100000" />';
        $mockResults1 = array( 'ringside.feed.get'=>$entries1 );
        $case1Expected = array( '<h3>Today</h3>', '<li>Test entry #1</li>' );
        
        $entries2 = array();
        $entry = array( 'type'=>1, 'title'=>'Test entry #2', 'author_id'=>100001, 'created'=>date( 'Y-m-d h:i:s' ) );
        $entries2[] = $entry;
        
        $today = mktime();
        $yesterday = $today - ( 60 * 60 * 24 );
        $entry = array( 'type'=>2, 'title'=>'Test entry #3', 'author_id'=>100001, 
            'created'=>date( 'Y-m-d h:i:s', $yesterday ) );
        $entries2[] = $entry;
        
        $entry = array( 'type'=>2, 'title'=>'Test entry #4', 'author_id'=> 100001, 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 5, 24, 1978, null ) ) );
        $entries2[] = $entry;
		
		//test dates - today, yesterday, and a specific date in the past
        $case2 = '<rs:feed uid="100001" />';
        $mockResults2 = array( 'ringside.feed.get'=>$entries2 );
        $case2Expected = array( '<h3>Today</h3>', '<li>Test entry #2</li>', 
            '<h3>Yesterday</h3>', '<li>Test entry #3</li>', 
            '<h3>May 24, 1978</h3>', '<li>Test entry #4</li>' );

        //test 0 results
        $case3 = '<rs:feed uid="100002" />';
        $case3Expected = array( '<ul>', '<li>There are no feed entries to display.</li>' );
        
        $entries4 = array();
        $entry = array( 'type'=>1, 'title'=>'Test entry #5', 'author_id'=>100001, 'created'=>date( 'Y-m-d h:i:s' ) );
        $entries4[] = $entry;
        $entry = array( 'type'=>1, 'title'=>'Test entry #6', 'author_id'=>100001, 'created'=>date( 'Y-m-d h:i:s' ) );
        $entries4[] = $entry;
        $entry = array( 'type'=>1, 'title'=>'Test entry #7', 'author_id'=>100001, 'created'=>date( 'Y-m-d h:i:s' ) );
        $entries4[] = $entry;
        $case4 = '<rs:feed uid="100001" />';
        $mockResults4 = array( 'ringside.feed.get'=>$entries4 );
        $case4Expected = array( '<h3>', '<h3>Today</h3>', '<li>Test entry #5</li>', 
            '<li>Test entry #6</li>', '<li>Test entry #7</li>' );
        
        $entries5 = array();
        $entry = array( 'type'=>2, 'title'=>'Test entry #8', 'author_id'=>100001, 
            'created'=>date( 'Y-m-d h:i:s', $yesterday ) );
        $entries5[] = $entry;
        $entry = array( 'type'=>2, 'title'=>'Test entry #9', 'author_id'=>100001, 
            'created'=>date( 'Y-m-d h:i:s', $yesterday ) );
        $entries5[] = $entry;
        $entry = array( 'type'=>2, 'title'=>'Test entry #10', 'author_id'=>100001, 
            'created'=>date( 'Y-m-d h:i:s', $yesterday ) );
        $entries5[] = $entry;
        $case5 = '<rs:feed uid="100001" />';
        $mockResults5 = array( 'ringside.feed.get'=>$entries5 );
        $case5Expected = array( '<h3>', '<h3>Yesterday</h3>', '<li>Test entry #8</li>', 
            '<li>Test entry #9</li>', '<li>Test entry #10</li>' );
        
        $entries6 = array();
        $entry = array( 'type'=>2, 'title'=>'Test entry #11', 'author_id'=> 100001, 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 5, 24, 1978, null ) ) );
        $entries6[] = $entry;
        $entry = array( 'type'=>2, 'title'=>'Test entry #12', 'author_id'=> 100001, 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 5, 24, 1978, null ) ) );
        $entries6[] = $entry;
        $entry = array( 'type'=>2, 'title'=>'Test entry #13', 'author_id'=> 100001, 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 5, 24, 1978, null ) ) );
        $entries6[] = $entry;
        $case6 = '<rs:feed uid="100001" />';
        $mockResults6 = array( 'ringside.feed.get'=>$entries6 );
        $case6Expected = array( '<h3>', '<h3>May 24, 1978</h3>', '<li>Test entry #11</li>', 
            '<li>Test entry #12</li>', '<li>Test entry #13</li>' );
        
        $entries7 = array();
        $entry = array( 'type'=>2, 'title'=>'{actor} did something interesting.', 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 1, 1, 2008, null ) ), 'templatized'=>true );
        $entries7[] = $entry;
        $case7 = '<rs:feed uid="100000" />';
        $mockResults7 = array( 'ringside.feed.get'=>$entries7, "facebook.users.getInfo"=>array( array( 'first_name'=>'Joe', "last_name"=>"Robinson",  ) ) );
        $case7Expected = array( '<h3>', '<h3>January 1, 2008</h3>', 
            '<li>Joe Robinson did something interesting.</li>' );
        
        $entries8 = array();
        $entry = array( 'type'=>2, 'title'=>'{actor} just finished reading {book}.', 'title_data'=>'{"book":"Of Mice And Men"}', 
            'created'=>date( 'Y-m-d h:i:s', mktime( null, null, null, 1, 2, 2008, null ) ), 'templatized'=>true );
        $entries8[] = $entry;
        $mockResults8 = array( 'ringside.feed.get'=>$entries8 );
        $case8Expected = array( '<h3>', '<h3>January 2, 2008</h3>', '<li>Joe Robinson just finished reading Of Mice And Men.</li>' );
        
        return array(
	  		array( 100000, $mockResults1, $case1, $case1Expected, null ),
            array( 100001, $mockResults2, $case2, $case2Expected, null ),
	  		array( 100002, array(), $case3, $case3Expected, null ),
            array( 100001, $mockResults4, $case4, $case4Expected, null ),
            array( 100001, $mockResults5, $case5, $case5Expected, null ),
            array( 100001, $mockResults6, $case6, $case6Expected, null ),
            array( 100000, $mockResults7, $case7, $case7Expected, null )
//            array( 100000, $mockResults8, $case8, $case8Expected, null )
	    );
	}

	/**
	 * @dataProvider providerFeeds
	 */
	public function testFeeds ( $mockUid, $mockMethodResults, $parseString, $expectedSubstrings, $notExpectedSubstrings ) {
		$ma = new MockApplication();
        $ma->uid = $mockUid;
		$ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
//        error_log( 'results: ' . var_export( $results, true ) );

        if( $expectedSubstrings != null ) {
	        foreach( $expectedSubstrings as $substring ) {
	        	$this->assertContains( $substring, $results, "[$substring] not found in [$results] but should have been" );
	        	$index = strpos( $results, $substring );
	        	$this->assertTrue( isset( $index ) );
	        	$secondHalf = substr( $results, $index + strlen( $substring ) );
//	        	error_log( 'secondHalf: ' . var_export( $secondHalf, true ) );
	        	$index = strpos( $secondHalf, $substring );
	        	$this->assertFalse( $index, "Found [$substring] a second time in [$results]" );
	        }
        }
        
        if( $notExpectedSubstrings != null ) {
	        foreach( $notExpectedSubstrings as $notsubstring ) {
	        	$this->assertNotContains( $notsubstring, $results, "[$notsubstring] found in [$results] but should not have been" );
	        }
        }
	}
}
?>
