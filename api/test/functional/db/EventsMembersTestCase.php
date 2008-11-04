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
require_once 'BaseDbTestCase.php';
require_once 'ringside/api/bo/Events.php';

class EventsMembersTestCase extends BaseDbTestCase {

    public function testConvertStatusToString()
    {
       $code = Api_Bo_Events::getRsvpStatusCode( Api_Bo_Events::RS_FBDB_RSVP_STR_ATTENDING );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_ATTENDING, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_ATTENDING );

       $code = Api_Bo_Events::getRsvpStatusCode( Api_Bo_Events::RS_FBDB_RSVP_STR_DECLINED );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_DECLINED, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_DECLINED );

       $code = Api_Bo_Events::getRsvpStatusCode( Api_Bo_Events::RS_FBDB_RSVP_STR_NOT_REPLIED);
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_NOT_REPLIED, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_NOT_REPLIED );

       $code = Api_Bo_Events::getRsvpStatusCode( Api_Bo_Events::RS_FBDB_RSVP_STR_UNSURE );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_UNSURE, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_UNSURE );

       $code = Api_Bo_Events::getRsvpStatusCode( "WRONG" );
       $this->assertTrue( $code == -1 , "$code does not match " . -1 );
    }

    public function testConvertStatusToCode()
    {
       $code = Api_Bo_Events::getRsvpStatusString( Api_Bo_Events::RS_FBDB_RSVP_ATTENDING  );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_STR_ATTENDING, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_STR_ATTENDING );

       $code = Api_Bo_Events::getRsvpStatusString( Api_Bo_Events::RS_FBDB_RSVP_DECLINED );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_STR_DECLINED, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_STR_DECLINED );

       $code = Api_Bo_Events::getRsvpStatusString( Api_Bo_Events::RS_FBDB_RSVP_NOT_REPLIED);
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_STR_NOT_REPLIED, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_STR_NOT_REPLIED );

       $code = Api_Bo_Events::getRsvpStatusString( Api_Bo_Events::RS_FBDB_RSVP_UNSURE );
       $this->assertTrue( $code == Api_Bo_Events::RS_FBDB_RSVP_STR_UNSURE, "$code does not match " . Api_Bo_Events::RS_FBDB_RSVP_STR_UNSURE );
    }
    
}
?>
