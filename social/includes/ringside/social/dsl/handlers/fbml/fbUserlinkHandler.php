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
class fbUserlinkHandler {
   
   public $name;
   
   function doStartTag( $application, $parentHandler, $args ) {
      if ( !isset( $args['uid'] ) || empty($args['uid']) ) {
         echo 'RUNTIME ERROR: fb:user: Required attribute "uid" not found in node fb:userlink';
         return false;
      }

      $client = $application->getClient();
      $uid = $args['uid'];
      $columns = array('first_name', 'last_name');
      $response = $client->users_getInfo( array($uid), $columns );
      
      $this->name = $response[0]['first_name'];
      $this->name .= ' '.$response[0]['last_name'];

      $url = RingsideSocialConfig::$webRoot;
      echo '<a href="'.$url.'/profile.php?id='.$uid.'">'.$this->name.'</a>';
      return true;
   }

   function doEndTag( $application, $parentHandler, $args ) { 
   }
   
	function getType()
   	{
   		return 'inline';   	
   	}

}

?>
