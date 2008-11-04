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
 * Represents a Friend request in the database. Friend requests expire after a certain period,
 * determined by the server configuration.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

class RingsideFriendInvitation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('friend_invitations');
        $this->hasColumn('id', 'integer', 4, array('unsigned' => true, 'primary' => true, 'notnull' => true, 'autoincrement' => true));
        $this->hasColumn('inv_key', 'string', 32, array('primary' => true, 'unique' => true, 'notnull' => true));
        $this->hasColumn('from_id', 'integer', 4, array('unsigned' => true, 'primary' => true, 'notnull' => true));
        $this->hasColumn('expires', 'integer', 4, array('unsigned' => true, 'notnull' => true));
        // If the to_id is null, then it is inferred when the invitation is requested from the logged-in user
        // Not needed now, because the rest of the lifecycle is taken care of within the Friends table (see RingsideFriends)
//        $this->hasColumn('to_id', 'integer', 4, array('unsigned' => true, 'primary' => true));
    }

    public function setUp()
    {
        parent::setUp();
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