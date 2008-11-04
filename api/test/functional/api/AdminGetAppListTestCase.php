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
require_once( 'BaseAPITestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/rest/AdminGetAppList.php" );

class AdminGetAppListTestCase extends BaseAPITestCase
{
	
	public function testExecuteWithoutApiKeyParameter()
	{	
        $_apiCall = $this->initRest( new AdminGetAppList(), null);
        $_results_without_specifying_domain_key = $_apiCall->execute();
        $this->assertTrue(is_array($_results_without_specifying_domain_key));
        $this->assertArrayHasKey('apps', $_results_without_specifying_domain_key);
        $this->assertEquals(1, count($_results_without_specifying_domain_key), "there should be an app key");
        $this->assertGreaterThan(0, count($_results_without_specifying_domain_key['apps']), "there should be at least one deployed application");
        $_app = $_results_without_specifying_domain_key['apps'][0];
        $this->assertArrayHasKey("id", $_app);
        $this->assertArrayHasKey("callback_url", $_app);

	}
	
	public function testExecuteWithApiKeyParameter()
	{	        
        $_apiCall = $this->initRest( new AdminGetAppList("Ringside"), null);
        $_results_specifying_domain_key = $_apiCall->execute();
        
        $this->assertTrue(is_array($_results_specifying_domain_key));
        $this->assertArrayHasKey('apps', $_results_specifying_domain_key);
        $this->assertEquals(1, count($_results_specifying_domain_key), "there should be an app key");
        $this->assertGreaterThan(0, count($_results_specifying_domain_key['apps']), "there should be at least one deployed application");
        $_app = $_results_specifying_domain_key['apps'][0];
        $this->assertArrayHasKey("id", $_app);
        $this->assertArrayHasKey("callback_url", $_app);
	}

}
?>
