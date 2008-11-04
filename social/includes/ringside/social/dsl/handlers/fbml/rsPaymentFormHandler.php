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

/**
 * Creates a form that submits to the Ringside Payment system application.  <STRONG>NOTE:</STRONG>  Only submits data via POST
 *
 * @author Ringside Networks
 * @tagName fb:request-form
 * @tagRequired string action The POST URL for this form.
 * @tagOptional string content The content of the form.
 * @tagChild fb:friend-selector
 * @tagChild fb:multi-friend-selector
 * @tagChild fb:multi-friend-input
 * @tagChild fb:request-form-submit
 * @return 
 */
class rsPaymentFormHandler {

    public $content;
    public $action;

    function doStartTag( $application, $parentHandler, $args ) {
        echo "\n" . '<form id="payment-form" action="' . RingsideWebConfig::$webRoot . '/payments" method="post">' . "\n";
        echo '  <input type="hidden" id="payment-ids" name="ids" />';
        if( isset( $args[ 'action' ] ) && !empty( $args[ 'action' ] ) ) {
            $this->action = $args[ 'action' ];
        }
        if( isset( $args[ 'content' ] ) && !empty( $args[ 'content' ] ) ) {
            $this->content = $args[ 'content' ];
        }
//        return array( 'fb:multi-friend-selector' );
      return true;
    }

    function doBody($application, &$parentHandler, $args, &$body ) {
//        return 'fb:multi-friend-selector';
//    	return true;
    }

    /**
     * At this point valid child tags are collected, so should be able
     * to just print the form.
     *
     *
     * @param unknown_type $application
     * @param unknown_type $parentHandler
     * @param unknown_type $args
     */
    function doEndTag( $application, $parentHandler, $args ) {
        echo '</form>';
    }
    
	function getType()
   	{
   		return 'block';   	
   	}
     
}



?>
