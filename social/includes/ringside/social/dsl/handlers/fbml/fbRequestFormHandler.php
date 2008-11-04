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
 * Creates a two column form.  <STRONG>NOTE:</STRONG>  Only submits data via POST
 *
 * @author Ringside Networks
 * @tagName fb:request-form
 * @tagRequired string action The POST URL for this form.
 * @tagOptional int width The width of the form in pixels, defaults to 424px.
 * @tagOptional int label width The width of the first column of the form in pixels, defaults to 75px.  <STRONG>NOTE:</STRONG> Cannot be 0, use 1 or greater.
 * @tagChild fb:friend-selector
 * @tagChild fb:multi-friend-selector
 * @tagChild fb:multi-friend-input
 * @tagChild fb:request-form-submit
 * @return 
 */
class fbRequestFormHandler {

	public $type;
	public $content;
	public $invite;
	public $payment;
	public $action;
	public $method;

	function doStartTag( $application, $parentHandler, $args ) {
		error_log( 'fbRequestFormHandler->doStartTag()' );
        $obj = new ReflectionObject( $parentHandler );
        error_log( 'parent handler: ' . $obj->getName() );

        $this->type = $args[ 'type' ];
        $this->payment = $args[ 'payment' ];
        echo '<form id="form-friend-select" action="">';

		return array( 'fb:multi-friend-selector' );

	}

	function doBody($application, &$parentHandler, $args, &$body ) {
        error_log( 'fbRequestFormHandler->doBody()' );
        echo '</form>';
        
//		if ( !empty( $body ) && strlen( trim( $body )) > 0 ) {
//			$this->wildText = $body;
//		}
		return true;
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
        error_log( 'fbRequestFormHandler->doEndTag()' );
	}
	
	function getType()
   	{
   		return 'block';   	
   	}
	 
}



?>
