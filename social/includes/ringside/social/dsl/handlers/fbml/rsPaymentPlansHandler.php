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

class rsPaymentPlansHandler {

	function doStartTag( $application, $parentHandler, $args ) {
		$aid = !isset( $args['aid'] ) ? $aid = $application->getApplicationId() : $args[ 'aid' ];
		$editable = false;
		if( isset( $args[ 'editable' ] ) && $args[ 'editable' ] == 'true' ) {
		  $editable = true;
		}
		
//		error_log( 'payment plans tag got aid: ' . $aid );
		$client = $application->getClient();
        $plans = $client->subscriptions_get_app_plans( $aid );
//        error_log( 'rsPaymentPlansHandler - plans: ' . print_r( $plans, true ) );
        
		if( $plans != null ) {
			//if the key is 'result', that means there are no plans in the database.
			//when plans are found, the key 'plans' is used.
			if( array_key_exists( 'result', $plans ) ) return false;
			
			$str = '<table border="0" cellspacing="1" cellpadding="0" id="pricing-plans">'."\n";
			$str .= '  <tr>'."\n";
			$str .= '      <th>&nbsp;</th>'."\n";
			$str .= '      <th>Plan Name</th>'."\n";
			$str .= '      <th>Price / Month</th>'."\n";
			$str .= '      <th>Description</th>'."\n";
            $str .= '      <th>Number of Friends</th>'."\n";
			$str .= '  </tr>'."\n";
	
			foreach( $plans as $plan ) {
				$str .= '  <tr>'."\n";
				if( $editable ) {
					$str .= '      <td><input type="checkbox" name="selectedPlan[]" value="' . $plan[ 'plan_id' ] . '" /></td>'."\n";
				}
				else {
					$str .= '      <td><input type="radio" name="selectedPlan" value="' . $plan[ 'plan_id' ] . '" /></td>'."\n";
				}
				$str .= '      <td>' . $plan[ 'name' ] . '</td>'."\n";
				$str .= '      <td>$' . $plan[ 'price' ] . '</td>'."\n";
				$str .= '      <td>' . $plan[ 'description' ] . '</td>'."\n";
                $str .= '      <td>' . $plan[ 'num_friends' ] . '</td>'."\n";
				$str .= '  </tr>'."\n";
	        }
	        if( $editable ) {
	        	$str .= '	<tr><td>&nbsp;</td><td colspan="3"><input type="submit" name="delete_button" value="Delete Selected Plan(s)" /></td></tr>';
	        }
	        $str .= '</table>';
			if ( $parentHandler != null && method_exists( $parentHandler, 'addCustom' ) ) {
				$parentHandler->addCustom( 'Pricing Plan(s)', 'id', $str );
				return false;
			}
			else {
				echo $str;
				return false;
			}
		}
	}

	function doBody( $application, $parentHandler, $args ) {
		
	}

	function doEndTag( $application, $parentHandler, $args ) {
		
	}
	
	function isEmpty()
	{
		return true;
	}
	
	function getType()
   	{
   		return 'inline';   	
   	}
}

?>
