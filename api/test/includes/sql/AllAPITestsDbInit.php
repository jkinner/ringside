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

require_once( "db/RsOpenFBDbTestUtils.php" );

class AllAPITestsDbInit {

   public function initUsers() {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        $user = RsOpenFBDbTestUtils::getUser1();
        $user->insertIntoDb( $dbCon );
        
        $user = RsOpenFBDbTestUtils::getUser2();
        $user->insertIntoDb( $dbCon );
        
        $user = RsOpenFBDbTestUtils::getUser3();
        $user->insertIntoDb( $dbCon );
        
        $user = RsOpenFBDbTestUtils::getUser4();
        $user->insertIntoDb( $dbCon );
    }

    public function initApp() {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        $app1 = RsOpenFBDbTestUtils::getApp1();
        
        $app2 = RsOpenFBDbTestUtils::getApp2();
    }

//    private function initUsersApp() {
//        $dbCon = RsOpenFBDbTestUtils::getDbCon();
//        $user1 = RsOpenFBDbTestUtils::getUser1();
//        $user2 = RsOpenFBDbTestUtils::getUser2();
//        $user3 = RsOpenFBDbTestUtils::getUser3();
//        $user4 = RsOpenFBDbTestUtils::getUser4();
//        $app1 = RsOpenFBDbTestUtils::getApp1();
//        $app2 = RsOpenFBDbTestUtils::getApp2();
//        
//        $userApps = RsOpenFBDbTestUtils::getUserApps();
//        foreach( $userApps as $oneUserApp ) {
//            $oneUserApp->insertIntoDb( $dbCon );
//        }
//    }
    
    public function initPhotoAlbums() {
        $user1 = RsOpenFBDbTestUtils::getUser1();
        $album1 = RsOpenFBDbTestUtils::getAlbum1();
        $album2 = RsOpenFBDbTestUtils::getAlbum2();
    }
    
    public function initPhotos() {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        $user1 = RsOpenFBDbTestUtils::getUser1();
        $album1 = RsOpenFBDbTestUtils::getAlbum1();
        $album2 = RsOpenFBDbTestUtils::getAlbum2();

        $photo1 = RsOpenFBDbTestUtils::getPhoto1();
        $photo2 = RsOpenFBDbTestUtils::getPhoto2();
        $photo3 = RsOpenFBDbTestUtils::getPhoto3();
    }
    
    public function initPhotoTags() {
        $dbCon = RsOpenFBDbTestUtils::getDbCon();
        $user1 = RsOpenFBDbTestUtils::getUser1();
        $user2 = RsOpenFBDbTestUtils::getUser2();
        $photoTag1 = RsOpenFBDbTestUtils::getPhotoTag1();
        $photoTag2 = RsOpenFBDbTestUtils::getPhotoTag2();
        $photoTag3 = RsOpenFBDbTestUtils::getPhotoTag3();
    }
    
    public function clearTable( $table, $dbCon ) {
        $stmt = "DELETE FROM " . $table;
        $result = mysql_query( $stmt, $dbCon );
    }
    
}


$init = new AllAPITestsDbInit();

$init->initUsers();
$init->initApp();
//$init->initUsersApp();
$init->initPhotoAlbums();
$init->initPhotos();
$init->initPhotoTags();

?>
