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

/**
 * User credentials for users where Ringside manages the login.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsideCredential extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('credentials');
        $this->hasColumn('id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->hasColumn('user_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
        $this->hasColumn('password', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
    }

    public function setUp()
    {
        $this->hasOne('RingsideUser as user', array('local' => 'user_id', 'foreign' => 'id'));
        $this->actAs('Timestampable', array('created' =>  array('name'    =>  'created',
                                                                'type'    =>  'timestamp',
                                                                'format'  =>  'Y-m-d H:i:s',
                                                                'disabled' => false,
                                                                'options' =>  array()),
                                            'updated' =>  array('name'    =>  'modified',
                                                                'type'    =>  'timestamp',
                                                                'format'  =>  'Y-m-d H:i:s',
                                                                'disabled' => false,
                                                                'options' =>  array())));
    }
}
?>