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
require_once( 'BaseDbTestCase.php' );
require_once( "ringside/api/dao/Network.php" );

class NetworkTestCase extends BaseDbTestCase
{
	public function testGetSetProperties()
	{
		$uid = 9999;
		$nid = 'key1';
		
		$nprops = array('name' => 'net1', 'auth_url' => 'someurl',
							 'login_url' => 'someurl2', 'canvas_url' => 'someurl3',
							 'web_url' => 'someurl4');
	
		Api_Dao_Network::createNetwork($uid, $nid, $nprops);
		
		$props = Api_Dao_Network::getNetworkProperties($uid, array($nid));
		$this->assertEquals(1, count($props));
		$this->assertEquals('net1', $props[0]['name']);
		$this->assertEquals($nid, $props[0]['key']);
				
		$newprops = array('name' => 'newname',
								'auth_url' => 'someurl',
								'login_url' => 'anotherurl',
								'canvas_url' => 'yaurl',
								'web_url' => 'yaurl2',
								'social_url' => 'yaurl3',
								'auth_class' => 'yaurl4',
								'postmap_url' => 'yaurl5');
				
		Api_Dao_Network::setNetworkProperties($uid, $nid, $newprops);
		
		$props = Api_Dao_Network::getNetworkProperties($uid, array($nid));
		$this->assertEquals(1, count($props));		
		$this->assertEquals($nid, $props[0]['key']);
		
		foreach ($newprops as $pname => $pval) {
			$this->assertEquals($pval, $props[0][$pname]);
		}
		
		Api_Dao_Network::deleteNetwork($nid);	
	}

	public function testCreateAndDelete()
	{
		$uid = 9999;
		$nprops = array('name' => 'somenetwork', 'auth_url' => 'someurl',
							 'login_url' => 'someurl2', 'canvas_url' => 'someurl3',
							 'web_url' => 'someurl4');
		// TODO: TESTING: If this test fails, it can cause subsequent test failures
		Api_Dao_Network::createNetwork($uid, 'somekey', $nprops);
		
		$nlist = Api_Dao_Network::getNetworkProperties($uid);
		$this->assertEquals(4, count($nlist));
		$testNetwork = null;
		foreach ( $nlist as $net )
		{
		    if ( $net['key'] == 'somekey' )
		    {
		        $testNetwork = $net;
		        break;
		    }
		}
		$this->assertNotNull($testNetwork, 'Expected to find a network with key somekey');
		$this->assertEquals('somekey', $testNetwork['key']);
		$this->assertEquals('somenetwork', $testNetwork['name']);
		$this->assertEquals('someurl', $testNetwork['auth_url']);
		$this->assertEquals('someurl2', $testNetwork['login_url']);
		$this->assertEquals('someurl3', $testNetwork['canvas_url']);
		$this->assertEquals('someurl4', $testNetwork['web_url']);
		
		Api_Dao_Network::deleteNetwork('somekey');
		
		$nlist = Api_Dao_Network::getNetworkProperties($uid);		
		$this->assertEquals(3, count($nlist));	
	}
}
?>
