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
require_once('ringside/test/RingsideWebTestUtils.php');
require_once('simpletest/web_tester.php');
require_once('RingsideWebTestConfig.php');
require_once('ringside/web/config/RingsideWebConfig.php');

class CanvasTestCase extends WebTestCase {
	public function testLoginRedirect() {
		$this->restart();
		$this->setMaximumRedirects(0);
		$this->get(RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot."/friends.php");
		$content = $this->_browser->getContent();
		if ( ! $this->assertResponse(array(302), "Expected a redirect: %s") ) {
			print($content);
		}
		$realheaders = RingsideWebTestUtils::parse_headers($this->_browser->getHeaders());
		$this->assertHeader('location', RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot."/login.php?next=".RingsideWebConfig::$webRoot."/friends.php", "%s: was " . $realheaders['location'][0]);
	}

	public function testDoLogin() {
		$baseurl = RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot."/login.php?next=".RingsideWebConfig::$webRoot."/friends.php";
		$this->restart();
		$this->setMaximumRedirects(0);
		$this->get($baseurl);
		$this->assertResponse(array(200));
		$this->assertFieldByName('next');
		$this->setFieldByName('email', 'joe@goringside.net');
		$this->setFieldByName('p', 'ringside');
		$this->clickSubmit("Login!");
		$content = $this->_browser->getContent();
		if ( ! $this->assertResponse(array(302)) ) {
			print ($content);
		}
		$realheaders = RingsideWebTestUtils::parse_headers($this->_browser->getHeaders());
		$this->assertHeader('location', RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot."/friends.php", "%s: was " . $realheaders['location'][0]);
	}
}
?>
