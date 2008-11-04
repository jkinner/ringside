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

class PhpClientAdminTestCase extends BasePhpClientTestCase
{		
	public function testAdminGetPropertiesByCanvasName()
	{
		$flds = array("application_name","api_key","secret_key","callback_url");
		$resp = $this->fbClient->admin_getAppProperties(implode(",", $flds), null, "app1200", null);
		
		$this->assertEquals("Application 1200", $resp["application_name"]);		
		$this->assertEquals("application-1200", $resp["api_key"]);
		$this->assertEquals("application-1200", $resp["secret_key"]);
		$this->assertEquals("http://url.com/callback", $resp["callback_url"]);
	}
	
	public function testAdminGetPropertiesByApiKey()
	{
		$flds = array("application_name","api_key","secret_key","callback_url");
		$resp = $this->fbClient->admin_getAppProperties(implode(",", $flds), null, null, "application-1200");
		
		$this->assertEquals("Application 1200", $resp["application_name"]);		
		$this->assertEquals("application-1200", $resp["api_key"]);
		$this->assertEquals("application-1200", $resp["secret_key"]);
		$this->assertEquals("http://url.com/callback", $resp["callback_url"]);
	}
	
	public function testAdminCreateApp()
	{
		$name = "yet another new application";
		$resp = $this->fbClient->admin_createApp($name);		
		$this->assertEquals($name, $resp["app"]["name"]);
		$this->assertTrue(isset($resp["app"]["api_key"]));
		$this->assertTrue(isset($resp["app"]["secret"]));
	}
	
	public function testAdminGetAppList()
	{
		$resp = $this->fbClient->admin_getAppList();		
		$this->assertTrue(count($resp) != 0);
		//TODO: thorough testing
	}
}

?>
