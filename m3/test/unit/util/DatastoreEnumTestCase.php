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

require_once ('PHPUnit/Framework.php');
require_once ('ringside/m3/util/DatastoreEnum.php');

class DatastoreEnumTestCase extends PHPUnit_Framework_TestCase
{
    public function testDatastoreEnum()
    {
        $e = M3_Util_DatastoreEnum::DB();
        $this->assertEquals(M3_Util_DatastoreEnum::_DB, $e->getValue());
        $this->assertTrue($e->isDB());

        $e = M3_Util_DatastoreEnum::create(M3_Util_DatastoreEnum::_DB);
        $this->assertEquals(M3_Util_DatastoreEnum::_DB, $e->getValue());
        $this->assertTrue($e->isDB());

        // create an invalid enum - for now, this will log an error and default to something valid
        $e = M3_Util_DatastoreEnum::create('invalid');
        $this->assertEquals(M3_Util_DatastoreEnum::_DB, $e->getValue());
        $this->assertTrue($e->isDB());

        $values = M3_Util_DatastoreEnum::getEnums();
        $this->assertEquals(1, count($values));
        $this->assertTrue($values[0] === M3_Util_DatastoreEnum::_DB);
    }
}

?>