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
require_once( 'BaseApiUnitTestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/rest/SubscribeUsersToApp.php" );

/**
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */
class SubscribeUsersToAppUnitTestCase extends BaseApiUnitTestCase 
{

    public static function providerConstructor() {

        return array(
            array( 36001, array(), 36050, true ),
            array( 36001, array( 'uid'=>36001 ), 36050, true ),
            array( 36001, array( 'aid'=>36050 ), 36050, true ),
            array( 36001, array( 'planid'=>36010 ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050 ), 36050, true ),
            array( 36001, array( 'planid'=>36010, 'aid'=>36050 ), 36050, true ),
            array( 36001, array( 'planid'=>36010, 'uid'=>36001 ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010 ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'nid'=>36010 ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'nid'=>36050, 'planid'=>36010 ), 36050, true ),
            array( 36001, array( 'nid'=>36001, 'aid'=>36050, 'planid'=>36010 ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020 ), 
                36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100' ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111' ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013' ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'firstname'=>'Larry' ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'firstname'=>'Larry', 'lastname'=>'Jones' ), 
                36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'firstname'=>'Larry', 'lastname'=>'Jones', 'email'=>'l@j.com' ), 
                36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'firstname'=>'Larry', 'lastname'=>'Jones', 'email'=>'l@j.com',
                'phone'=>'555-555-5555' ), 36050, true ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'friends'=>'36071,36072,36073', 'firstname'=>'Larry', 'lastname'=>'Jones', 'email'=>'l@j.com',
                'phone'=>'555-555-5555' ), 36050, false ),
            array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'planid'=>36010, 'nid'=>36020, 
                'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'10/2013', 'friends'=>'36071', 'firstname'=>'Larry', 'lastname'=>'Jones', 'email'=>'l@j.com',
                'phone'=>'555-555-5555' ), 36050, false )
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
            $plan = $this->initRest( new SubscribeUsersToApp(), $params, $uid, $appId );
            $this->assertFalse ( $expected, "Should have thrown exception " . var_export( $params ,true ) );
        } catch ( OpenFBAPIException $e ) {
            $this->assertTrue( $expected, "Should NOT have thrown exception " . var_export( $params ,true ) );
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode() );
        }
    }
        
}

?>
