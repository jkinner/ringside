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
require_once ('ringside/api/dao/Comments.php');

class CommentsTestCase extends BaseDbTestCase
{
	public static function provideForInsertRows()
	{
		return array(array('rock', 1000, 4949, 'Message number ' . $m ++, true), array('rock', 1000, 4949, 'Message number ' . $m ++, true), array('paper', 1000, 4949, 'Message number ' . $m ++, true), array('paper', 1000, 4949, 'Message number ' . $m ++, true), array('paper', 1000, 4949, 'Message number ' . $m ++, true), array('scissors', 1000, 4949, 'Message number ' . $m ++, true), array('scissors', 1000, 4949, 'Message number ' . $m ++, true));
	}

	/**
	 * @dataProvider provideForInsertRows
	 */
	public function testInsertRows($xid, $aid, $uid, $text, $pass)
	{
		$response = Api_Dao_Comments::createComment($xid, $aid, $uid, $text);
		$this->assertTrue($response != false, "Test should have $pass, mysql error? ");
	}

	public static function provideForCidIncreases()
	{
		return array(array('cid_increases', 1000, 4949));
	}

	/**
	 * @dataProvider provideForCidIncreases
	 */
	public function testCidIncreases($xid, $aid, $uid)
	{
		$lastCid = 0;
		for($count = 0; $count < 10; $count ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "Great message eh : $count");
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			$this->assertGreaterThan($lastCid, $response);
			if($response)
				$lastCid = (string)$response;
		}
	
	}

	public static function providerForCountRows()
	{
		return array(array('count1-' . time(), 1200, 3421, 10));
	}

	/**
	 * @dataProvider providerForCountRows
	 */
	public function testCountRows($xid, $aid, $uid, $count)
	{
		$cids = array();
		for($i = 0; $i < $count; $i ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "going away you know : $i");
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			if($response)
				$cids[] = (string)$response;
		}
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals($count, $commentCount);
	}

	public static function providerForFetchRows()
	{
		return array(array('fetch1-' . time(), 1200, 3421, 10), array('fetch2-' . time(), 1200, 3421, 100));
	}

	/**
	 * @dataProvider providerForFetchRows
	 */
	public function testFetchRows($xid, $aid, $uid, $count)
	{
		$cids = array();
		for($i = 0; $i < $count; $i ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "fetching are we not? : $i");
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			if($response)
				$cids[] = (string)$response;
		}
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals($count, $commentCount);
		
		$fetchCids = Api_Dao_Comments::getCommentsByXidAndAid($xid, $aid, null, null);
		$this->assertEquals($count, count($fetchCids));
		
		$cidValues = array_values($cids);
		$fetchCidValues = array();
		foreach($fetchCids as $comment)
		{
			$cid = $comment['cid'];
			$this->assertContains("" . $cid, $cidValues, "findBy returned a CID not inserted in this run ($cid)");
			$fetchCidValues[] = $cid;
		}
		
		$fetchCidValues = array_values($fetchCidValues);
		foreach($cids as $cid)
		{
			$this->assertContains("" . $cid, $fetchCidValues, "findBy did not return a CID which was inserted in this run ($cid)");
		}
		
	// TODO add test for getting back in groups/pages

	}

	public static function providerForDeleteRows()
	{
		return array(array('delete-' . time(), 1200, 3421, 10));
	}

	/**
	 * @dataProvider providerForDeleteRows
	 */
	public function testDleteRows($xid, $aid, $uid, $count)
	{
		$cids = array();
		for($i = 0; $i < $count; $i ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "fetching are we not? : $i");
			
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			if($response)
				$cids[] = (string)$response;
		}
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals($count, $commentCount);
		
		foreach($cids as $key=>$cid)
		{
			$ret = Api_Dao_Comments::deleteComment($cid, $xid, $aid);
			$this->assertTrue($ret > 0, "failed to delete from database");
			
			$ret = Api_Dao_Comments::deleteComment($cid, $xid, $aid);
			
			$this->assertTrue($ret === 0, "Should have failed record already removed.");
		}
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals(0, $commentCount);
	
	}

	public static function providerFetchLastNRows()
	{
		return array(array('lastFetch1-' . time(), 1200, 3421, 100, 10));
	}

	/**
	 * @dataProvider providerFetchLastNRows
	 */
	public function testFetchLastNRows($xid, $aid, $uid, $count, $mostRecentCount)
	{
		$cids = array();
		for($i = 0; $i < $count; $i ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "fetching are we not? : $i");
			
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			if($i >= ($count - $mostRecentCount))
			{
				if($response)
					$cids[] = (string)$response;
			}
		}
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals($count, $commentCount);
		
		$fetchCids = Api_Dao_Comments::getCommentsByXidAndAid($xid, $aid, null, $mostRecentCount);
		$this->assertTrue($fetchCids !== null, "Test should have passed");
		$this->assertEquals($mostRecentCount, count($fetchCids));
		
		$cidValues = array_values($cids);
		$fetchCidValues = array();
		foreach($fetchCids as $comment)
		{
			$cid = $comment['cid'];
			$this->assertContains("" . $cid, $cidValues, "findBy returned a CID not inserted in this run ($cid)");
			$fetchCidValues[] = $cid;
		}
		
		$fetchCidValues = array_values($fetchCidValues);
		foreach($cids as $cid)
		{
			$this->assertContains("" . $cid, $fetchCidValues, "findBy did not return a CID which was inserted in this run ($cid)");
		}
	}

	public static function providerGetPage()
	{
		return array(array('getPage1-' . time(), 1200, 3421, 22, 0, 10, 10), array('getPage2-' . time(), 1200, 3421, 33, 10, 23, 23), array('getPage3-' . time(), 1200, 3421, 18, 12, 22, 6), array('getPage4-' . time(), 1200, 3421, 23, 25, 100, 0));
	}

	/**
	 * @dataProvider providerGetPage
	 */
	public function testGetPage($xid, $aid, $uid, $count, $first, $amount, $expected)
	{
		$cids = array();
		for($i = 0; $i < $count; $i ++)
		{
			$response = Api_Dao_Comments::createComment($xid, $aid, $uid, "fetching are we not? : $i");
			
			$this->assertTrue($response != false, "Test should have passed, mysql error? ");
			if($response)
				$cids[] = (string)$response;
		}
		
		$cids = array_reverse($cids);
		$cids = array_slice($cids, $first, $amount);
		
		$commentCount = Api_Dao_Comments::getCommentCount($xid, $aid);
		$this->assertEquals($count, $commentCount);
		
		$fetchCids = Api_Dao_Comments::getCommentsByXidAndAid($xid, $aid, $first, $amount);
		$this->assertTrue($fetchCids !== null, "Test should have passed");
		$this->assertEquals($expected, count($fetchCids));
		
		$cidValues = array_values($cids);
		$fetchCidValues = array();
		foreach($fetchCids as $comment)
		{
			$cid = $comment['cid'];
			$this->assertContains("" . $cid, $cidValues, "findBy returned a CID not inserted in this run ($cid)");
			$fetchCidValues[] = $cid;
		}
		
		$fetchCidValues = array_values($fetchCidValues);
		foreach($cids as $cid)
		{
			$this->assertContains("" . $cid, $fetchCidValues, "findBy did not return a CID which was inserted in this run ($cid)");
		}
	}
}

?>
