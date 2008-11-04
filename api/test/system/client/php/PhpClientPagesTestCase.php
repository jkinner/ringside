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

require_once("BasePhpClientTestCase.php");

class PhpClientPagesTestCase extends BasePhpClientTestCase
{		
	public function testPagesGetInfo()
	{
		//TODO: "overview" should be "company_overview"
		$res = $this->fbClient->pages_getInfo("17700",
					"name,has_added_app,page_id,pic_big,pic_small,pic_square,pic_large,type,pic,founded,mission,website,company_overview,products",
					null, null);
		
		$pi = $res[0];
		$this->assertEquals($pi["page_id"], 17700);
		$this->assertEquals($pi["founded"], "Early last year");
		$this->assertEquals($pi["mission"], "A long mission with a nice vision");
		$this->assertEquals($pi["website"], "http://mycrazystore.com/");
		$this->assertEquals($pi["company_overview"], "Crazy guys with crazy store");
		$this->assertEquals(trim($pi["products"]), "A b C and D");
		$this->assertEquals($pi["has_added_app"], 1);
		$this->assertEquals($pi["name"], "CRAZYONLINE");
		$this->assertEquals($pi["type"], "ONLINE_STORE");
		$this->assertEquals($pi["pic_big"], "http://www.picit.com/image1.jpg");
		$this->assertEquals($pi["pic_small"], "http://www.picit.com/image1.jpg");
		$this->assertEquals($pi["pic_square"], "http://www.picit.com/image1.jpg");
		$this->assertEquals($pi["pic_large"], "http://www.picit.com/image1.jpg");
      $this->assertEquals($pi["pic"], "http://www.picit.com/image1.jpg");		
	}
	
	public function testPagesIsFan()
	{
		$res = $this->fbClient->pages_isFan("17700",17002);
		$this->assertEquals($res, 1);
		
		$failed = false;
		try {
			//can't find fans that aren't friends of 17001
			$res = $this->fbClient->pages_isFan("17700",17005);
		} catch (Exception $e) {
			$failed = true;
		}
		$this->assertTrue($failed);
	}
	
	public function testPagesIsAdmin()
	{
		$res = $this->fbClient->pages_isAdmin("17700");
		$this->assertEquals($res, 1);
		
		$this->initClient(17002);
		$res = $this->fbClient->pages_isAdmin("17700");
		$this->assertEquals($res, 0);
	}
	
	public function testPagesIsAppAdded()
	{
		$res = $this->fbClient->call_method('facebook.pages.isAppAdded', array("page_id" => 17700));
		$this->assertEquals($res, 1);
	}
}

?>
