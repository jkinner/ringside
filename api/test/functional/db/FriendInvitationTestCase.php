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

require_once ('BaseDbTestCase.php');
require_once ("RsOpenFBDbTestUtils.php");
require_once('ringside/api/dao/FriendInvitation.php');

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class FriendInvitationTestCase extends BaseDbTestCase
{
    public function testCreateInvitation()
    {
        $inv = Api_Dao_FriendInvitation::createInvitation(100001, 3600);
        $this->assertNotNull($inv);
        $inv_record = Api_Dao_FriendInvitation::getInvitation($inv);
        $this->assertNotNull($inv_record);
        $this->assertEquals($inv, $inv_record->inv_key);
        $this->assertEquals(100001, $inv_record->from_id);
    }
    
    public function testDeleteInvitation()
    {
        $inv = Api_Dao_FriendInvitation::createInvitation(100001, 3600);
        $this->assertEquals(1, Api_Dao_FriendInvitation::deleteInvitation($inv));
        $inv_record = Api_Dao_FriendInvitation::getInvitation($inv);
        $this->assertFalse($inv_record);
        
    }

    public function testExpireInvitation()
    {
        $inv = Api_Dao_FriendInvitation::createInvitation(100001, 3600);
        $inv_record = Api_Dao_FriendInvitation::getInvitation($inv);
        $inv_record->expires = time();
        $inv_record->save();
        Api_Dao_FriendInvitation::deleteAllExpired();
        $inv_record = Api_Dao_FriendInvitation::getInvitation($inv);
        $this->assertFalse($inv_record);
    }
    
}
?>