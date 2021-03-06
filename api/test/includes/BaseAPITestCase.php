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

require_once( 'PHPUnit/Framework.php' );
require_once( 'SuiteTestUtils.php' );
require_once( 'ringside/api/RequestContext.php' );
require_once( 'ringside/social/config/RingsideSocialConfig.php');
class BaseAPITestCase extends PHPUnit_Framework_TestCase
{
   protected $time = null;
   
	protected function setUp() 
	{
   		require_once 'sql/AllAPITests-teardown.sql';   		
   		require_once( 'AllApiTestFixtures.php' );
   		AllApiTestFixtures::createLocalDomain();   		
   		AllApiTestFixtures::createApps();
		require_once 'sql/AllAPITests-setup.sql';
		require_once 'sql/AllAPITestsDbInit.php';
		
       
       $this->time = time();
	}
   
   protected function tearDown() 
   {
   		
   }

   /**
    * Mock/Force the initialization of a REST call.  
    * Typically avoiding the Session creation and other HTTP request processing.
    *
    * @param Api_AbstractRest $rest
    * @param string $uid
    * @param array $apiParams
    * @return The initialized REST object. 
    */
   protected function initRest( Api_AbstractRest &$rest, $apiParams, $uid = null, $aid = null, $nid = null, &$session = array() ) { 
      
      if ( $uid != null )  {
         $session['uid'] = $uid;
      }
      if ( $aid != null )  {
         $session['app_id'] = $aid;
      }
      if ( $nid != null )  {
         $session['network_key'] = $nid;
      } else {
          $session['network_key'] = RingsideSocialConfig::$apiKey;
      }
      
      $context = Api_RequestContext::createRequestContext( $apiParams );
      
      $rest->_setContext( $context );
      $rest->_setSession( $session );
      $rest->validateRequest();

      return $rest;
   }   
      
}


?>
