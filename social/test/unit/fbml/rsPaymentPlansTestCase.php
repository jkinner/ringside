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

require_once( 'ringside/social/dsl/RingsideSocialDslParser.php' );
require_once( 'MockApplication.php' );
require_once( 'MockClient.php' );
include_once( 'ringside/social/RingsideSocialUtils.php');
include_once( 'ringside/social/config/RingsideSocialConfig.php');

class rsPaymentPlansTestCase extends PHPUnit_Framework_TestCase {

   /**
    * Builds an array of inputs that mirrors the parameters of the rs:payment-plans tag
    *
    * @return the inputs to the test method.
    */
	public static function providerTestPlansBasic() {		
		//basic
		$plans = array();

		$plan = array();
        $plan[ 'plan_id' ] = 1;
        $plan[ 'aid' ] = 100101;
        $plan[ 'name' ] = 'bronze';
        $plan[ 'length' ] = 12;
        $plan[ 'unit' ] = 'months';
        $plan[ 'price' ] = 5;
        $plan[ 'description' ] = 'Get Features!';
		$plans[] = $plan;
        
		$plan2 = array();
        $plan2[ 'plan_id' ] = 2;
        $plan2[ 'aid' ] = 100101;
        $plan2[ 'name' ] = 'gold';
        $plan2[ 'length' ] = 12;
        $plan2[ 'unit' ] = 'months';
        $plan2[ 'price' ] = 10;
        $plan2[ 'description' ] = 'Get More Features!';
        $plan2[ 'num_friends' ] = '42';
        $plans[] = $plan2;

		//specify an application that does not have plans associated with it
		$case1 = '<rs:payment-plans aid="100002" />';
		
		//specify an application that does have plans associated with it
		$case2 = '<rs:payment-plans aid="100101" />';
		$mockResults = array( 'ringside.subscriptions.getAppPlans'=>$plans );
		$case2Expected = array( '<th>Plan Name</th>', '<th>Price / Month</th>', '<th>Description</th>', 
								'<td>bronze</td>', '<td>$5</td>', '<td>Get Features!</td>',
								'<td>gold</td>', '<td>$10</td>', '<td>Get More Features!</td>', '<td>42</td>' );
		
		//specify payment plan tag inside fb:editor tag with plans
		$case3 = '<fb:editor action="whatever.php"><rs:payment-plans aid="100002" /></fb:editor>';
		
		//specify payment plan tag inside fb:editor tag with plans
		$case4 = '<fb:editor action="whatever.php"><rs:payment-plans aid="100101" /></fb:editor>';
		
		//test using default aid
		$case5 = '<rs:payment-plans />';
		$mockResults = array( 'ringside.subscriptions.getAppPlans'=>$plans );
		
		//test editable attribute
		$case6 = '<rs:payment-plans editable="true" />';
		$mockResults = array( 'ringside.subscriptions.getAppPlans'=>$plans );
		$case6Expected = array( '<th>Plan Name</th>', '<th>Price / Month</th>', '<th>Description</th>', 
								'<td>bronze</td>', '<td>$5</td>', '<td>Get Features!</td>',
								'<td>gold</td>', '<td>$10</td>', '<td>Get More Features!</td>',
								'<input type="submit" name="delete_button"', '<input type="checkbox" name="selectedPlan[]" ' );
		
	  	return array(
	  		array( null, $case1, null, array( ' id="pricing-plans">' ) ),
	  		array( $mockResults, $case2, $case2Expected, null ),
	  		array( $mockResults, $case3, $case2Expected, null ),
	  		array( null, $case4, null, array( ' id="pricing-plans">' ) ),
	  		array( $mockResults, $case5, $case2Expected, null ),
	  		array( $mockResults, $case6, $case6Expected, null )
	  	);
	}

	/**
	 * @dataProvider providerTestPlansBasic
	 */
	public function testPlansBasic ( $mockMethodResults, $parseString, $expectedSubstrings, $notExpectedSubstrings ) {
		$ma = new MockApplication();
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        $ma->applicationId = '100101';
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );

        if( $expectedSubstrings != null ) {
	        foreach( $expectedSubstrings as $substring ) {
	        	$this->assertContains( $substring, $results );
	        }
        }
        
        if( $notExpectedSubstrings != null ) {
	        foreach( $notExpectedSubstrings as $notsubstring ) {
	        	$this->assertNotContains( $notsubstring, $results );
	        }
        }
	}
}
?>
