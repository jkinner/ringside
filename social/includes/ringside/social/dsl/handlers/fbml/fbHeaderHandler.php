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
class fbHeaderHandler {
   private $body;
      
   function doStartTag($application, &$parentHandler, $args ) {
      $client = $application->getClient();
      $result = $client->admin_getAppProperties( 'application_name,icon_url'  );

      $set = isset( $result['icon_url'] );
      $iconUrl = $result['icon_url'];
      
      $application_icon = isset( $result['icon_url'] ) ? $result['icon_url'] : null;
      $headerClass = $application_icon ? '' : 'no_icon';
      $headerStyle = $application_icon ? 'style="background-image: url('.$application_icon.')"' : '';

      echo '<div class="title_header">';
      echo '	<h2 class="'.$headerClass.'" '.$headerStyle.'>';
      echo ($result['application_name']?$result['application_name']:'');
      return true;
   }
   
   function doBody($application, &$parentHandler, $args, $body ) {
      $this->body = $body;
   }
   
   function doEndTag( $application, &$parentHandler, $args) {
      echo '</h2>';
      echo '</div>';
   }
   
	function getType()
   	{
   		return 'block';   	
   	}
   
}

?>
