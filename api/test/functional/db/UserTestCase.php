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
require_once( 'BaseDbTestCase.php' );
require_once( "ringside/api/dao/User.php" );
require_once( "RsOpenFBDbTestUtils.php" );

class UserTestCase extends BaseDbTestCase {

    public function testConstructor()
    {
        $user = new Api_Dao_User();
        $this->assertNull( $user->getId() );
        $this->assertNull( $user->getUsername() );
        $this->assertNull( $user->getPassword() );
    }

    public function testgetSetId()
    {
        $id = 234;
        $user = new Api_Dao_User();
        $this->assertNull( $user->getId() );
        $user->setId( $id );
        $this->assertEquals( $id, $user->getId() );
    }
    
    public function testgetSetUsername()
    {
        $username = "myname";
        $user = new Api_Dao_User();
        $this->assertNull( $user->getUsername() );
        $user->setUsername( $username );
        $this->assertEquals( $username, $user->getUsername() );
    }
    
    public function testgetSetPassword()
    {
        $password = "mypass";
        $user = new Api_Dao_User();
        $this->assertNull( $user->getPassword() );
        $user->setPassword( $password );
        $this->assertEquals( $password, $user->getPassword() );
    }

    private function getNumUsers( $dbCon, $id ) {
        $stmt = "SELECT COUNT(id) FROM users WHERE id = $id";
        $result = mysql_query( $stmt, $dbCon );
        if ( ! $result ) {
            throw new Exception( mysql_error(), FB_ERROR_CODE_DATABASE_ERROR );
        }
        $row = mysql_fetch_array( $result );
        return $row[ 0 ];
    }
    
    private function getUser( $dbCon, $id ) {
        $stmt = "SELECT * FROM users WHERE id = $id";
        $result = mysql_query( $stmt, $dbCon );
        if ( ! $result ) {
            throw new Exception( mysql_error(), FB_ERROR_CODE_DATABASE_ERROR );
        }
        $row = mysql_fetch_array( $result );
        return $row;
    }
    
    public function testIsUserAdmin()
    {
    	  $dbCon = RsOpenFBDbTestUtils::getDbCon();
    	  $this->assertFalse(Api_Dao_User::isUserAdmin(2, $dbCon));    
    	  $this->assertTrue(Api_Dao_User::isUserAdmin(9999, $dbCon));
    }
    
    public function testinsertIntoDbdeleteFromDb()
    {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        
        $id = 9876543;
        $password = "mypass";
        $username = "myname";

        $user = new Api_Dao_User();
        $user->setId( $id );
        $user->setUsername( $username );
        $user->setPassword( $password );

        try {
            $this->assertEquals( 0, $this->getNumUsers( $dbCon, $id ) );
            $user->insertIntoDb( $dbCon );
            $this->assertEquals( 1, $this->getNumUsers( $dbCon, $id ) );
            $row = $this->getUser( $dbCon, $id );
            $this->assertEquals( $id, $row[ id ] );
            $this->assertEquals( $password, $row[ 'password' ] );
            $this->assertEquals( $username, $row[ 'username' ] );
        } catch ( Exception $exception ) {
            $user->deleteFromDb( $dbCon );
            throw $exception;
        }
        $user->deleteFromDb( $dbCon );
        $this->assertEquals( 0, $this->getNumUsers( $dbCon, $id ) );
    }
    
    private function getNumAllUsers( $dbCon ) {
        $stmt = "SELECT COUNT(id) FROM users";
        $result = mysql_query( $stmt, $dbCon );
        if ( ! $result ) {
            throw new Exception( mysql_error(), FB_ERROR_CODE_DATABASE_ERROR );
        }
        $row = mysql_fetch_array( $result );
        return $row[ 0 ];
    }
    
    public function testautoincrementIntoDb()
    {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        
        $password = "mypass";
        $username = "myname";

        $user = new Api_Dao_User();
        $user->setUsername( $username );
        $user->setPassword( $password );

        try {
            $numRows = $this->getNumAllUsers( $dbCon );
            $this->assertNull( $user->getId() );
            $user->insertIntoDb( $dbCon );
            $this->assertEquals( $numRows + 1, $this->getNumAllUsers( $dbCon ) );
            $this->assertNotNull( $user->getId() );
            $row = $this->getUser( $dbCon, $user->getId() );
            $this->assertEquals( $user->getId(), $row[ id ] );
            $this->assertEquals( $password, $row[ 'password' ] );
            $this->assertEquals( $username, $row[ 'username' ] );
        } catch ( Exception $exception ) {
            $user->deleteFromDb( $dbCon );
            throw $exception;
        }
        $user->deleteFromDb( $dbCon );
        $this->assertEquals( $numRows, $this->getNumAllUsers( $dbCon ) );
    }
}
?>
