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
require_once('ringside/api/dao/records/RingsidePrincipal.php');
require_once('ringside/api/dao/records/RingsidePrincipalMap.php');
require_once('ringside/api/dao/tables/RingsidePrincipalTable.php');
require_once('ringside/api/dao/tables/RingsidePrincipalMapTable.php');

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsidePrincipalMapTableTestCase extends PHPUnit_Framework_TestCase
{
    public function testCreateMap()
    {
        $p = new RingsidePrincipal();
        $p->id = -1;	//strange but it works!
        $this->assertTrue($p->trySave());
        $pid = $p->getIncremented();
        
        $map = new RingsidePrincipalMap();
        $map->principal_id = $pid;
        $map->app_id = 123;
        $map->network_id = 'abcd';
        $map->uid = 1234;
        $map->save();
        $subject_map = Doctrine::getTable('RingsidePrincipalMap')->findOneBySubject(123, 'abcd', 1234);
        $this->assertNotNull($subject_map);
        $this->assertEquals($p->id, $subject_map->principal_id);
    }
}
?>