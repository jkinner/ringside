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

class PhpClientNotificationsTestCase extends BasePhpClientTestCase
{		
	public function testNotificationsGet()
	{
		$arr = $this->fbClient->notifications_get();
		
		$mess = $arr["messages"];
		$uread = $mess["unread"];
		$this->assertEquals($uread, "0");
		$mr = $mess["most_recent"];
		$this->assertEquals($mr, "0");
		
		$pokes = $arr["pokes"];
		$uread = $pokes["unread"];
		$this->assertEquals($uread, "2");
		$mr = $pokes["most_recent"];
		$this->assertEquals($mr, "17003");
		
		$shares = $arr["shares"];
		$uread = $shares["unread"];
		$this->assertEquals($uread, "2");
		$mr = $shares["most_recent"];
		$this->assertEquals($mr, "17202");
		
		$evites = $arr["event_invites"];
		$e = $evites[0];
		$this->assertEquals($e, "17400");		
	}
	
	public function testNotificationsSend()
	{
		$this->fbClient->notifications_send("17002,17003", "goats!");
		
		$this->initClient(17002);
		$arr = $this->fbClient->notifications_get();		
		$m = $arr["messages"];
		$this->assertEquals($m["unread"], 1);
		
		$this->initClient(17003);
		$arr = $this->fbClient->notifications_get();		
		$m = $arr["messages"];
		$this->assertEquals($m["unread"], 1);
				
	}
	
	public function testNotificationsSendEmail()
	{
		//TODO: this test case doesn't seem to be correct...
		$this->fbClient->notifications_sendEmail("17003", "no subject", "this is the message body", "");
		
		$this->initClient(17003);
		$arr = $this->fbClient->notifications_get();
		$m = $arr["messages"];
		$this->assertEquals($m["unread"], 1);
	}
}
?>
