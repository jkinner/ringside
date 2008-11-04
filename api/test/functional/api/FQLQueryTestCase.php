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
require_once( "ringside/api/facebook/FqlQuery.php" );

 
class FqlQueryTestCase extends BaseAPITestCase
{    
	/**
	 * Really just checks the implementation of FqlQuery.php,
	 * because FQL queries have already been fairly tested
	 * by the devtest/fql/* cases.
    */
	public function testExecute()
   {
      $uid = 17001;
      $fql = "SELECT uid FROM user WHERE uid IN (17001)";
      $params = array("query" => $fql);
      $fq = $this->initRest( new FqlQuery(), $params, $uid, $this->appId );
      $resp = $fq->execute();

      $this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
   }
}
?>
