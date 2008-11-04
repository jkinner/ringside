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

class PhpClientEventsTestCase extends BasePhpClientTestCase
{		
	public function testEventsGet()
	{
		//$this->fbClient->printResponse = true;
		$arr = $this->fbClient->events_get(null, null, null, null, null);
		$e = $arr[0];
		
		$this->assertEquals($e["eid"], "17400");
		$this->assertEquals($e["name"], "SOME_EVENT");
		$this->assertEquals($e["nid"], "0");
		$this->assertEquals($e["host"], "athome");
		$this->assertEquals($e["group_type"], "party");
		$this->assertEquals($e["group_subtype"], "hardy");
		$this->assertEquals($e["creator"], "17001");
		
		$this->assertTrue(isset($e["update_time"]));
		$this->assertEquals($e["location"], "on the bank");
		
		$v = $e["venue"];
		$this->assertEquals($v["city"], "mars");
		$this->assertEquals($v["state"], "ok");
		$this->assertEquals($v["country"], "us");
				
		$this->fbClient->printResponse = false;
	}
	
	public function testEventsGetMembers()
	{
		//$this->fbClient->printResponse = true;
		$arr = $this->fbClient->events_getMembers(17400);
		
		$att = $arr["attending"][0];
		$this->assertEquals($att, "17004");
		
		$att = $arr["unsure"][0];
		$this->assertEquals($att, "17002");
		
		$att = $arr["declined"][0];
		$this->assertEquals($att, "17003");
		
		$att = $arr["not_replied"][0];
		$this->assertEquals($att, "17001");		
		
		$this->fbClient->printResponse = false;
	}
	
}
?>
