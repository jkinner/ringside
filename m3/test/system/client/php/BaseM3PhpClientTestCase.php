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

require_once('ringside/m3/client/RestClient.php');
require_once('ringside/m3/util/Settings.php');

class BaseM3PhpClientTestCase extends PHPUnit_Framework_TestCase
{
	private $client;	

	public function setUp()
	{	
		parent::setUp();
		$this->initClient();
	}
		
	protected function initClient()
	{
		unset($this->client);
		$this->client = new M3_Client_RestClient(M3_Util_Settings::getRestServerUrl(), M3_Util_Settings::getM3SecretKey(), null);
		$this->assertTrue(isset($this->client));
	}
	
	public function testInit()
	{
		$this->assertNotNull($this->client);
	}
	
	/**
	 * Returns the client that tests can use.
	 * 
	 * @return M3_Client_RestClient the client that is available for tests
	 */
	protected function getClient()
	{
	    return $this->client;
	}
}
?>
