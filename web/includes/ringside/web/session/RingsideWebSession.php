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

/*
 * WebSession should only be included once, at that point we load the
 * session into memory.   Since we are in the web space we just use
 * the default mechanism to load the session.
 */
session_start();

/**
 * WebSession is a facade for the web session information.  Gives
 * us a constant space to manage the session information.  Constructor
 * will load the expected session information.
 *
 */

class RingsideWebSession {

   const WEB_SESSION_SOCIAL = 'web.social';
   const WEB_SESSION_USERID = 'web.userid';
   
   public function getSocial() {
      return isset( $_SESSION[self::WEB_SESSION_SOCIAL] )? $_SESSION[self::WEB_SESSION_SOCIAL] : null;
   }
   
   public function setSocial($socialSession) {
      $_SESSION[self::WEB_SESSION_SOCIAL]= $socialSession;
   }

   public function getUserId( ) {
      return isset( $_SESSION[self::WEB_SESSION_USERID] )? $_SESSION[self::WEB_SESSION_USERID] : null;      
   }
   
   public function setUserId( $userId ) { 
      $_SESSION[self::WEB_SESSION_USERID]= $userId;
   }
   
   public function clearSession( ) { 
      unset( $_SESSION[ self::WEB_SESSION_SOCIAL ] );
      unset( $_SESSION[ self::WEB_SESSION_USERID ] );
   }

}
?>
