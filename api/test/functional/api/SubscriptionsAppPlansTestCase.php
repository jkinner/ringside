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
require_once( 'BaseAPITestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );

require_once( "ringside/rest/SubscriptionsGetAppPlans.php" );
require_once( "ringside/rest/SubscriptionsAddAppPlan.php" );

/**
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */

class SubscriptionsAppPlansTestCase extends BaseAPITestCase 
{

	public static function providerConstructor() {

		return array(
	    array( 36001, array(), 36000 , false ),
		array( 36001, array( "aid"=>36000 ), 36000 , false )
    	);
	}


	/**
	 * @dataProvider providerConstructor
	 * 
	 * $expected if true, we're expecting the constructor to throw an exception
     */
	public function testConstructor( $uid, $params, $appId, $expected )
	{
    	try {
    	    $plan = $this->initRest( new SubscriptionsGetAppPlans(), $params, $uid, $appId );
      		$this->assertFalse ( $expected, "Should have thrown exception " . var_export( $params ,true ) );
      	} catch ( OpenFBAPIException $e ) {
        	$this->assertTrue( $expected, "Should NOT have thrown exception " . var_export( $params ,true ) );
        	$this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode() );
		}
	}


	public static function providerGet() {
	
		$expectedPlans = array( array( 'name'=>'bronze', 'price'=>'5.00', 'description'=>'Get features!' ),
			array( 'name'=>'silver', 'price'=>'10.00', 'description'=>'Get more features!' ), 
			array( 'name'=>'gold', 'price'=>'15.00', 'description'=>'Get all features!', 'numfriends'=>'6' )
		); 
		
    	return array(
    		array( 36001, array( "aid"=>36000 ), 36000 , $expectedPlans )
      );
   }


	/**
     * @dataProvider providerGet
     */
	public function testGet( $uid, $params, $appId, $expectedPlans )
	{
	   
    	$api = $this->initRest( new SubscriptionsGetAppPlans(), $params, $uid, $appId );
        $result = $api->execute();
        $this->assertNotNull( $result );
        $this->assertArrayHasKey( 'plans', $result, "Payment plans returned for " . $params['aid'] );

        $foundCount = 0;
//        error_log( 'expected plans: ' . print_r( $expectedPlans, true ) );
        foreach( $expectedPlans as $expectedPlan ) {
//        	error_log( 'expected plan: ' . print_r( $expectedPlan, true ) );
        	$foundIt = false;
        	$results = $results[ 'plans' ];
        	foreach( $result[ 'plans' ] as $plan ) {
//        		error_log( 'plan: ' . print_r( $plan, true ) );
        		
        		foreach( $expectedPlan as $expectedValue ) {
//        			error_log( 'expected value: ' . $expectedValue );
        			foreach( $plan as $actualValue ) {
//        				error_log( 'actual value: ' . $actualValue );
        				if( $expectedValue == $actualValue ) {
        					$foundCount++;
//        					error_log( 'found one!  found count: ' . $foundCount );
        					break;
        				}
        			}
        		}
        	}
        }
        $this->assertEquals( $foundCount, 10, "Each attribute should only have been found once, and there are 3 in 3 plans." );
	}

	public static function providerGetNegative() {
	
		$expectedPlans = array(); 
		
    	return array(
    		array( '36001', array( 'aid'=>'36001' ), '36001' , $expectedPlans )
      );
   }
   
	/**
     * @dataProvider providerGetNegative
     */
	public function testGetNegative( $uid, $params, $appId, $expectedPlans )
	{
    	$api = $this->initRest( new SubscriptionsGetAppPlans(), $params, $uid, $appId );
        $result = $api->execute();
        $this->assertArrayNotHasKey( 'plans', $result );
	}



    public static function providerAdd() {

        $plans = array( array( 'aid'=>36000, 'plan_name'=>'a', 'price'=>'4.00', 'description'=>'Get a features!' ),
            array( 'aid'=>36000, 'plan_name'=>'b', 'price'=>'8.00', 'description'=>'Get b features!' ), 
            array( 'aid'=>36000, 'plan_name'=>'c', 'price'=>'12.00', 'description'=>'Get c features!' ),
            array( 'aid'=>36000, 'plan_name'=>'d', 'price'=>'16.00', 'description'=>'Get d features!', 'numfriends'=>'9' )
        );
    	
    	$expectedPlans = array( array( 'name'=>'a', 'price'=>'4.00', 'description'=>'Get a features!' ),
            array( 'name'=>'b', 'price'=>'8.00', 'description'=>'Get b features!' ), 
            array( 'name'=>'c', 'price'=>'12.00', 'description'=>'Get c features!' ),
            array( 'name'=>'d', 'price'=>'16.00', 'description'=>'Get d features!', 'numfriends'=>'9' )
        );
        
        return array(
            array( 36001, $plans, 36000 , $expectedPlans )
        );
    }

    /**
     * @dataProvider providerAdd
     */
    public function testAdd( $uid, $plans, $appId, $expectedPlans )
    {
    	foreach( $plans as $plan ) {
    	    $api = $this->initRest( new SubscriptionsAddAppPlan(), $plan, $uid, $appId );
 	        $result = $api->execute();
	        $this->assertNotNull( $result );
            $this->assertArrayHasKey( 'result', $result );
	        $this->assertTrue( $result[ 'result' ] == 1 );
    	}
        
    	$params = array( 'aid'=>$plans[0][ 'aid' ] );
    	$api = $this->initRest( new SubscriptionsGetAppPlans(), $params, $uid, $appId );
        $result = $api->execute();
        $this->assertNotNull( $result );
        $this->assertArrayHasKey( 'plans', $result, "Payment plans returned for " . $params['aid'] );

        $foundCount = 0;
//        error_log( 'expected plans: ' . print_r( $expectedPlans, true ) );
        foreach( $expectedPlans as $expectedPlan ) {
            $foundIt = false;
            $results = $results[ 'plans' ];
            foreach( $result[ 'plans' ] as $plan ) {
                foreach( $expectedPlan as $expectedValue ) {
                    foreach( $plan as $actualValue ) {
                        if( $expectedValue == $actualValue ) {
                            $foundCount++;
//                            error_log( 'expected plan: ' . print_r( $expectedPlan, true ) );
//                            error_log( 'plan: ' . print_r( $plan, true ) );
//                            error_log( 'expected value: ' . $expectedValue );
//                            error_log( 'found one!  found count: ' . $foundCount );
                            break;
                        }
                    }
                }
            }
        }
        $this->assertEquals( $foundCount, 13, "Each attribute should only have been found once, and there are 3 in 3 plans." );
    }
}

?>
