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
require_once ('BaseDbTestCase.php');
require_once ("ringside/api/dao/Feed.php");
require_once ("ringside/api/bo/Feed.php");
require_once ("RsOpenFBDbTestUtils.php");

class FeedTestCase extends BaseDbTestCase
{
	public function testDao()
	{
		$title = '{actor} reviewed the book {book}';
		$body = '{book} has received a rating of {num_stars} from the users of BookApplication';
		$titleData = '{"book":"Of Mice and Men"}';
		$bodyData = '{"book":"<a href{{=}}\"http:\/\/www.someurl.com\/OfMiceAndMen\">Of Mice and Men<\/a>","num_stars":5}';
		$bodyGeneral = '<fb:name uid{{=}}"1234" firstnameonly{{=}}true /> said "This book changed my life."';
		$image1 = "http://localhost/somimage.gif";
		$image1Link = "http://test.this.please.sir";
		$image2 = "http://localhost/somimage.gif";
		$image3 = "http://localhost/somimage.gif";
		$image3Link = "http://test.this.please.sir";
		$image4 = "http://localhost/somimage.gif";
		$actor = 12345;
		$targets = '234,345,456';
		$priority = 50;
		$image2Link = null;
		$image4Link = null;
		$created = null;
		
		// Test just Title
		// public function testInsertRows( $type, $isTemplate, $author , $setParams, $pass )
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 0;
		$author = '100';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, null, null, null, null, $author, null, null, null, null, null, null, null, null, null, null, null, $created);
		$this->assertTrue($ret, "Title Test should of returned false!");
		
		// Test title + body
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_STORY, 0, '101', $withBody, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_STORY;
		$templatized = 0;
		$author = '101';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, null, $body, null, null, $author, null, null, null, null, null, null, null, null, null, null, null, $created);
		$this->assertTrue($ret, "Body Test should of returned false!");
		
		// Test title + body + data
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '102', $withData, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 0;
		$author = '102';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, null, null, null, null, null, null, null, null, null, null, null, $created);
		$this->assertTrue($ret, "Data Test should of returned true!");
		
		// test title + body + data + image1
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '103', $withImage1, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 1;
		$author = '103';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, null, null, null, null, null, null, null, null, null, $created);
		$this->assertTrue($ret, "Image 1 Test should of returned true!");
		
		// test title + body + data + image1 + images
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '104', $withImages, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 1;
		$author = '104';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, null, null, null, $created);
		$this->assertTrue($ret, "Images Test should of returned true!");
		
		// test title + body + data + image1 + images + actor
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '105', $withActor, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 1;
		$author = '105';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, null, null, $created);
		$this->assertTrue($ret, "Actor Test should of returned true!");
		
		// test title + body + data + image1 + images + actor + targets
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '106', $withTargets, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 1;
		$author = '106';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, null, $created);
		$this->assertTrue($ret, "Targets Test should of returned true!");
		
		// test title + body + data + image1 + images + actor + targets + priority
		//array(Api_Dao_Feed::RS_FBDB_FEED_TYPE_ACTION, 1, '107', $withPriority, true)
		$type = Api_Bo_Feed::RS_FBDB_FEED_TYPE_ACTION;
		$templatized = 1;
		$author = '107';
		$ret = Api_Dao_Feed::createFeed($type, $templatized, $title, $titleData, $body, $bodyData, $bodyGeneral, $author, $image1, $image1Link, $image2, $image2Link, $image3, $image3Link, $image4, $image4Link, $actor, $targets, $priority, $created);
		$this->assertTrue($ret, "Priority 
		Test should of returned true!");
	}
	
	
	
/*
$title = array();
$title ['title'] = '{actor} reviewed the book {book}';

$withBody = $title;
$withBody ['body'] = '{book} has received a rating of {num_stars} from the users of BookApplication';

$withData = $withBody;
$withData ['titleData'] = '{"book":"Of Mice and Men"}';
$withData ['bodyData'] = '{"book":"<a href{{=}}\"http:\/\/www.someurl.com\/OfMiceAndMen\">Of Mice and Men<\/a>","num_stars":5}';
$withData ['bodyGeneral'] = '<fb:name uid{{=}}"1234" firstnameonly{{=}}true /> said "This book changed my life."';

$withImage1 = $withData;
$withImage1 ['image1'] = "http://localhost/somimage.gif";
$withImage1 ['image1Link'] = "http://test.this.please.sir";

$withImages = $withImage1;
$withImages ['image2'] = "http://localhost/somimage.gif";
$withImages ['image3'] = "http://localhost/somimage.gif";
$withImages ['image3Link'] = "http://test.this.please.sir";
$withImages ['image4'] = "http://localhost/somimage.gif";

$withActor = $withImages;
$withActor ['actor'] = 12345;

$withTargets = $withActor;
$withTargets ['targets'] = '234,345,456';

$withPriority = $withTargets;
$withPriority ['priority'] = 50;
*/
}
?>
