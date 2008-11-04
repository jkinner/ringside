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

class fbErrorHandler {

	protected $css_class = 'error_message';
	private $banner;
   private $message = null;

   function doStartTag( $application, $parentHandler, $args ) {

      $this->banner = $args['message'];
      return 'fb:message';
   }

   function doBody( $application, $parentHandler, $args ) {
      if ( $this->banner == null && $this->message == null) {
         echo "OpenFBML Error : fb:error: You must specify a message with an fb:message tag or a message attribute ";
      }
      echo '<div class="'.$this->css_class.'"><h2>'.($this->banner?$this->banner:$this->message).'</h2>'."\n";
   }

   function doEndTag( $application, $parentHandler, $args ) {
      if ( $this->message && $this->banner ) {
      	echo $this->message;
      }
   	echo '</div>'."\n";
   }

   function setMessage( $message ) {

      $this->message = $message;

   }
   
	function getType()
   	{
   		return 'block';   	
   	}

}

?>
