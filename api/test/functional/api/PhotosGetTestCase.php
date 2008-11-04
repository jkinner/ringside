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
require_once( "ringside/api/facebook/PhotosGet.php" );
require_once( "db/RsOpenFBDbTestUtils.php" );

class PhotosGetTestCase extends BaseAPITestCase {
    public function testConstructor()
    {
        $uid = 123;

        // missing subj_id, aid, and pids
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        try {
            $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
            $this->fail( "Should have gotten an exception." );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_INCORRECT_SIGNATURE, $exception->getCode() );
        }
        
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'pids' ] = "";
        try {
            $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
            $this->fail( "Should have gotten an exception." );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_INCORRECT_SIGNATURE, $exception->getCode() );
        }
        
        // no more exceptions

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'pids' ] = "56, 57, 58";
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        $this->assertEquals( "56, 57, 58", implode( ",", $faf->getPids() ) );
        $this->assertNull( $faf->getSubjectId() );
        $this->assertNull( $faf->getAid() );

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'subj_id' ] = 21;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        $this->assertNull( $faf->getPids() );
        $this->assertEquals( 21, $faf->getSubjectId() );
        $this->assertNull( $faf->getAid() );

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = 31;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        $this->assertNull( $faf->getPids() );
        $this->assertNull( $faf->getSubjectId() );
        $this->assertEquals( 31, $faf->getAid() );

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'pids' ] = "56, 57, 58";
        $apiParams[ 'aid' ] = 31;
        $apiParams[ 'subj_id' ] = 21;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        $this->assertEquals( "56, 57, 58", implode( ",", $faf->getPids() ) );
        $this->assertEquals( 21, $faf->getSubjectId() );
        $this->assertEquals( 31, $faf->getAid() );
    }

    private function assertPhoto( $row, $photo ) {
        $this->assertEquals( $photo->pid, $row[ FB_PHOTOS_PID ] );
        $this->assertEquals( $photo->aid, $row[ FB_PHOTOS_AID ] );
        $this->assertEquals( $photo->owner, $row[ FB_PHOTOS_OWNER ] );
        $this->assertEquals( $photo->src, $row[ FB_PHOTOS_SRC ] );
        $this->assertEquals( $photo->src_big, $row[ FB_PHOTOS_SRC_BIG ] );
        $this->assertEquals( $photo->src_small, $row[ FB_PHOTOS_SRC_SMALL ] );
        $this->assertEquals( $photo->link, $row[ FB_PHOTOS_LINK ] );
        $this->assertEquals( $photo->caption, $row[ FB_PHOTOS_CAPTION ] );
    }

    private function assertPhoto1Photo2( $row, $photo1, $photo2 ) {
        $pid = $row[ FB_PHOTOS_PID ];
        if ( $pid == $photo1->pid ) {
            $this->assertPhoto( $row, $photo1 );
        } else if ( $pid == $photo2->pid ) {
            $this->assertPhoto( $row, $photo2 );
        } else {
            throw new Exception( "Unkown pid: " . $pid );
        }
        
    }
    public function testExecute()
    {
        $photo1 = RsOpenFBDbTestUtils::getPhoto1();
        $photo2 = RsOpenFBDbTestUtils::getPhoto2();
        $photo3 = RsOpenFBDbTestUtils::getPhoto3();
        $user1 = RsOpenFBDbTestUtils::getUser1();
        $album1 = RsOpenFBDbTestUtils::getAlbum1();
        $album2 = RsOpenFBDbTestUtils::getAlbum2();
        
        // test by subj_id by itself
        $uid = 10001;

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'subj_id' ] = $user1->getId();
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 2, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        $row = $fi[ 1 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        
        // test pids all by itself
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'pids' ] = $photo1->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 1 photo
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 1, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto( $row, $photo1 );
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'pids' ] = $photo2->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 1 photo
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 1, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto( $row, $photo2 );
        
        // test aid all by itself
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = $album1;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 2, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        $row = $fi[ 1 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = $album2;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 1 photo
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 1, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto( $row, $photo3 );

        // test subject id and pids
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'subj_id' ] = $user1->getId();
        $apiParams[ 'pids' ] = $photo1->pid . ", " . $photo2->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 2, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        $row = $fi[ 1 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'subj_id' ] = $user1->getId();
        $apiParams[ 'pids' ] = $photo1->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 1 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 1, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto( $row, $photo1 );

        // test aid and pids
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = $album1;
        $apiParams[ 'pids' ] = $photo1->pid . ", " . $photo2->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 2, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        $row = $fi[ 1 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = $album2;
        $apiParams[ 'pids' ] = $photo3->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 1 photo
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 1, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto( $row, $photo3 );

        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'aid' ] = $album1;
        $apiParams[ 'pids' ] = $photo3->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 0, count( $fi ) );
        
        //test subject id, aid, and pids
        $apiParams = array();
        $apiParams[ 'api_key' ] = "32";
        $apiParams[ 'subj_id' ] = $user1->getId();
        $apiParams[ 'aid' ] = $album1;
        $apiParams[ 'pids' ] = $photo1->pid . ", " . $photo2->pid;
        $faf = $this->initRest( new PhotosGet(), $apiParams, $uid );
        // should get back 2 photos
        $result = $faf->execute();
        $fi = $result[ FB_PHOTOS_PHOTO ];
        $this->assertEquals( 2, count( $fi ) );
        $row = $fi[ 0 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
        $row = $fi[ 1 ];
        $this->assertPhoto1Photo2( $row, $photo1, $photo2 );
    }

}
?>
