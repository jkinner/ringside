<?php
 /*
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
  */

require_once('ringside/api/config/RingsideApiConfig.php');
require_once('ringside/api/dao/records/RingsideDeveloperApp.php');
require_once('ringside/api/dao/records/RingsideApp.php');
require_once('ringside/api/dao/records/RingsideUser.php');
require_once('ringside/api/dao/tables/RingsideAppTable.php');
require_once('ringside/api/dao/tables/RingsideUserTable.php');
require_once('ringside/api/dao/tables/RingsideUsersAppTable.php');
require_once('ringside/api/dao/records/RingsideUsersApp.php');

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsideAppTestCase extends PHPUnit_Framework_TestCase
{
    private function resetDb()
    {
        // TODO: Shouldn't have to delete the many-to-many join record
        $uas = Doctrine::getTable('RingsideUsersApp')->findByUserId(101001);
        foreach ( $uas as $ua )
        {
            $ua->delete();
        }

        $aks = Doctrine::getTable('RingsideAppKey')->findByAppId(10000);
        foreach ( $aks as $ak )
        {
            $ak->delete();
        }
        
        $a = Doctrine::getTable('RingsideApp')->find(10000);
        if ( null != $a )
        {
            $a->delete();
        }
        
        $u = Doctrine::getTable('RingsideUser')->find(101000);
        if ( null != $u )
        {
            $u->delete();
        }

        $u = Doctrine::getTable('RingsideUser')->find(101001);
        if ( null != $u )
        {
            $u->delete();
        }
        
    }

    public function setUp()
    {
        $this->resetDb();
    }
    
    public function tearDown()
    {
        $this->resetDb();
    }
    
    public function testCreateApp()
    {
        $app = new RingsideApp();
        $app->id = 10000;
        $u1 = new RingsideUser();
        $u1->id = 101000;
        $u1->username = 'test1@example.com';
        $u1->password = sha1('ringside');
        $app->developers[] = $u1;
        $app->save();
        
        $app_id = $app->id;
        $apptable = Doctrine::getTable('RingsideApp');
        $app = $apptable->find($app_id);
        $this->assertNotNull($app);
        $this->assertNotNull($app->developers);
        $d1 = $app->developers[0];
        $this->assertNotNull($d1);
        $this->assertEquals('test1@example.com', $d1->username);
        $this->assertEquals(sha1('ringside'), $d1->password);
    }

    public function testAddAppDeveloper()
    {
        $app = new RingsideApp();
        $app->id = 10000;
        $u1 = new RingsideUser();
        $u1->id = 101000;
        $u1->username = 'test1@example.com';
        $u1->password = sha1('ringside');
        $app->developers[] = $u1;
        $app->save();
        
        $app_id = $app->id;
        $apptable = Doctrine::getTable('RingsideApp');
        $app = $apptable->find($app_id);
        $this->assertNotNull($app);
        $this->assertNotNull($app->developers);
        $d1 = $app->developers[0];
        $this->assertNotNull($d1);
        $this->assertEquals('test1@example.com', $d1->username);
        $this->assertEquals(sha1('ringside'), $d1->password);

        $u2 = new RingsideUser();
        $u2->id = 101001;
        $u2->username = 'test2@example.com';
        $u2->password = sha1('ringside');
        $app->developers[] = $u2;
        $app->save();
        
        $app = $apptable->find($app_id);
        $u2 = null;
        foreach ( $app->developers as $d )
        {
            if ( $d->id == 101001 )
            {
                $u2 = $d;
            }
        }
        
        $this->assertTrue(null !== $u2, 'Expected user 101001 to be in the list of developers');
        $this->assertEquals('test2@example.com', $u2->username);
        $this->assertEquals(sha1('ringside'), $u2->password);
    }
    
    public function testAddAppUser()
    {
        $app = new RingsideApp();
        $app->id = 10000;
        $u1 = new RingsideUser();
        $u1->id = 101000;
        $u1->username = 'test1@example.com';
        $u1->password = sha1('ringside');
        $app->developers[] = $u1;
        $app->save();
        
        $app_id = $app->id;
        $apptable = Doctrine::getTable('RingsideApp');
        $app = $apptable->find($app_id);
        $this->assertNotNull($app);
        $this->assertNotNull($app->developers);
        $d1 = $app->developers[0];
        $this->assertNotNull($d1);
        $this->assertEquals('test1@example.com', $d1->username);
        $this->assertEquals(sha1('ringside'), $d1->password);

        $u2 = new RingsideUser();
        $u2->id = 101001;
        $u2->username = 'test2@example.com';
        $u2->password = sha1('ringside');
        $app->users[] = $u2;
        $app->save();
        
        $app = $apptable->find($app_id);
        $u2 = $app->users[0];
        $this->assertNotNull($u2);
        $this->assertEquals('test2@example.com', $u2->username);
        $this->assertEquals(sha1('ringside'), $u2->password);
    }
    
    public function testCreateAppWithKey()
    {
        $app = new RingsideApp();
        $app->id = 10000;
        $u1 = new RingsideUser();
        $u1->id = 101000;
        $u1->username = 'test1@example.com';
        $u1->password = sha1('ringside');
        $app->developers[] = $u1;
        $k = new RingsideAppKey();
        $k->network_id = 1234;
        $k->api_key = 'abcd';
        $k->secret = 'defg';
        $app->keys[] = $k;
        $app->save();
        
        $app_id = $app->id;
        $apptable = Doctrine::getTable('RingsideApp');
        $app = $apptable->find($app_id);
        $this->assertNotNull($app);
        $this->assertNotNull($app->developers);
        $d1 = $app->developers[0];
        $this->assertNotNull($d1);
        $this->assertEquals('test1@example.com', $d1->username);
        $this->assertEquals(sha1('ringside'), $d1->password);
        $k1 = $app->keys[0];
        $this->assertNotNull($k1);
        $this->assertEquals(1234, $k1->network_id);
        $this->assertEquals('abcd', $k1->api_key);
        $this->assertEquals('defg', $k1->secret);
    }
    
}
?>