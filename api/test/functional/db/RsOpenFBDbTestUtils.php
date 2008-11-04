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

require_once ("ringside/api/dao/Album.php");
require_once ("ringside/api/dao/App.php");
require_once ("ringside/api/dao/User.php");
require_once ("ringside/api/dao/UsersApp.php");
require_once ("ringside/api/dao/Photo.php");
require_once ("ringside/api/dao/PhotoTag.php");

require_once 'ringside/api/config/RingsideApiConfig.php';

class RsOpenFBDbTestUtils
{
	/** The database connection. */
	private static $m_dbCon;

	public static function getDbCon()
	{
		if(self::$m_dbCon == null)
		{
			self::$m_dbCon = mysql_connect(RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password);
			if(! self::$m_dbCon)
			{
				throw new Exception('The service is not available at this time : db connect failure ' . mysql_error(), mysql_errno());
			}
			
			if(! mysql_select_db(RingsideApiConfig::$db_name, self::$m_dbCon))
			{
				echo "error selecting" . mysql_error();
				throw new Exception('The service is not available at this time : db catalog failure ' . mysql_error(), mysql_errno());
			}
		}
		
		return self::$m_dbCon;
	}

	/**
	 * Used by DB-UserAppTestCase
	 *
	 * @return unknown
	 */
	public static function getUser1()
	{
		static $user;
		if(! isset($user))
		{
			$user = new Api_Dao_User();
			$user->setId(10001);
			$user->setUsername("test user 1");
			$user->setPassword("test password 1");
		}
		return $user;
	}

	public static function getUser2()
	{
		static $user;
		if(! isset($user))
		{
			$user = new Api_Dao_User();
			$user->setId(10002);
			$user->setUsername("test user 2");
			$user->setPassword("test password 2");
		}
		return $user;
	}

	public static function getUser3()
	{
		static $user;
		if(! isset($user))
		{
			$user = new Api_Dao_User();
			$user->setId(10003);
			$user->setUsername("test user 3");
			$user->setPassword("test password 3");
		}
		return $user;
	}

	public static function getUser4()
	{
		static $user;
		if(! isset($user))
		{
			$user = new Api_Dao_User();
			$user->setId(10004);
			$user->setUsername("test user 4");
			$user->setPassword("test password 4");
		}
		return $user;
	}

	/**
	 * Used by Db-UserAppTestCase
	 *
	 * @return unknown
	 */
	public static function getApp1()
	{
		static $app;
		if(! isset($app))
		{
			$apiKey = "1000";
			$callbackUrl = "dbUrl 1";
			$canvasUrl = "canUrl 1";
			$default = 0;
			$name = "app name 1";
			$secretKey = "secKey 1";
			$sidenavUrl = "sideUrl 1";
			
			$app = Api_Dao_App::createApp($apiKey, $callbackUrl, $canvasUrl, $name, $default, $secretKey, $sidenavUrl, null, null);
		
		}
		return $app;
	}

	public static function getApp2()
	{
		static $app;
		if(! isset($app))
		{
			$apiKey = "1001";
			$callbackUrl = "dbUrl 2";
			$canvasUrl = "canUrl 2";
			$default = 0;
			$name = "app name 2";
			$secretKey = "secKey 2";
			$sidenavUrl = "sideUrl 2";
			
			$app = Api_Dao_App::createApp($apiKey, $callbackUrl, $canvasUrl, $name, $default, $secretKey, $sidenavUrl, null, null);
		}
		return $app;
	}

	public static function getAlbum1()
	{
		static $aid;
		if(! isset($aid))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			
			$coverPid = 0;
			$created = 345;
			$description = "desc 1";
			$location = "loc 1";
			$name = "pa 1";
			$owner = $user1->getId();
			
			$aid = Api_Dao_Album::createAlbum($coverPid, $description, $location, $name, $owner);
		}
		return $aid;
	}

	public static function getAlbum2()
	{
		static $aid;
		if(! isset($aid))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			
			$coverPid = 0;
			$created = 345;
			$description = "desc 2";
			$location = "loc 2";
			$modified = 234;
			$name = "pa 2";
			$owner = $user1->getId();
			
			$aid = Api_Dao_Album::createAlbum($coverPid, $description, $location, $name, $owner);
		}
		return $aid;
	}

	public static function getOneUserApp($appId, $userId)
	{
		$id = Api_Dao_UsersApp::createUsersApp($appId, $userId);
		return Api_Dao_UsersApp::getUserAppById($id);
	}

	//    public static function getUserApps() {
	//        static $userApps;
	//
	//        if ( ! isset( $userApps ) ) {
	//            $user1 = RsOpenFBDbTestUtils::getUser1();
	//            $user2 = RsOpenFBDbTestUtils::getUser2();
	//            $user3 = RsOpenFBDbTestUtils::getUser3();
	//            $user4 = RsOpenFBDbTestUtils::getUser4();
	//            $app1 = RsOpenFBDbTestUtils::getApp1();
	//            $app2 = RsOpenFBDbTestUtils::getApp2();
	//
	//            $userApp1 = RsOpenFBDbTestUtils::getOneUserApp( $app1->getId(), $user1->getId() );
	//            $userApp2 = RsOpenFBDbTestUtils::getOneUserApp( $app2->getId(), $user1->getId() );
	//            $userApp3 = RsOpenFBDbTestUtils::getOneUserApp( $app1->getId(), $user2->getId() );
	//            $userApp4 = RsOpenFBDbTestUtils::getOneUserApp( $app2->getId(), $user2->getId() );
	//            $userApp5 = RsOpenFBDbTestUtils::getOneUserApp( $app1->getId(), $user3->getId() );
	//            $userApp6 = RsOpenFBDbTestUtils::getOneUserApp( $app1->getId(), $user4->getId() );
	//
	//            $userApps = array( $userApp1, $userApp2, $userApp3, $userApp4, $userApp5, $userApp6 );
	//        }
	//        return $userApps;
	//    }
	

	public static function getOnePhoto($pid, $aid, $caption, $link, $owner, $src, $srcBig, $srcSmall)
	{
		$id = Api_Dao_Photo::createPhoto($aid, $caption, $link, $owner, $src, $srcBig, $srcSmall, null);
		if($id)
		{
			return Api_Dao_Photo::getPhotoById($id);
		}
		return null;
	}

	public static function getPhoto1()
	{
		static $photo;
		if(! isset($photo))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			$aid = RsOpenFBDbTestUtils::getAlbum1();
			
			$photo = RsOpenFBDbTestUtils::getOnePhoto(9999236, $aid, "cap 1", "link 1", $user1->getId(), "src 1", "src Big 1", "src Small 1");
		
		}
		return $photo;
	}

	public static function getPhoto2()
	{
		static $photo;
		if(! isset($photo))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			$aid = RsOpenFBDbTestUtils::getAlbum1();
			
			$photo = RsOpenFBDbTestUtils::getOnePhoto(9999237, $aid, "cap 2", "link 2", $user1->getId(), "src 2", "src Big 2", "src Small 2");
		}
		return $photo;
	}

	public static function getPhoto3()
	{
		static $photo;
		if(! isset($photo))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			$aid = RsOpenFBDbTestUtils::getAlbum2();
			
			$photo = RsOpenFBDbTestUtils::getOnePhoto(9999238, $aid, "cap 3", "link 3", $user1->getId(), "src 3", "src Big 3", "src Small 3");
		}
		return $photo;
	}

	public static function getOnePhotoTag($pid, $subjectId, $text, $xcoord, $ycoord)
	{
		$id = Api_Dao_PhotoTag::createPhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, null);
		
		$tags = Api_Dao_PhotoTag::getPhotoTags(array($id));
		if(count($tags) == 1)
		{
			return $tags[0];
		}
		return null;
	}

	public static function getPhotoTag1()
	{
		static $photoTag;
		if(! isset($photoTag))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			$photo1 = RsOpenFBDbTestUtils::getPhoto1();
			
			$photoTag = RsOpenFBDbTestUtils::getOnePhotoTag($photo1->pid, $user1->getId(), "t1", 1.1, 1.2);
		}
		return $photoTag;
	}

	public static function getPhotoTag2()
	{
		static $photoTag;
		if(! isset($photoTag))
		{
			$user2 = RsOpenFBDbTestUtils::getUser2();
			$photo1 = RsOpenFBDbTestUtils::getPhoto1();
			
			$photoTag = RsOpenFBDbTestUtils::getOnePhotoTag($photo1->pid, $user2->getId(), "t2", 2.1, 2.2);
		}
		return $photoTag;
	}

	public static function getPhotoTag3()
	{
		static $photoTag;
		if(! isset($photoTag))
		{
			$user1 = RsOpenFBDbTestUtils::getUser1();
			$photo2 = RsOpenFBDbTestUtils::getPhoto2();
			
			$photoTag = RsOpenFBDbTestUtils::getOnePhotoTag($photo2->pid, $user1->getId(), "t3", 3.1, 3.2);
		}
		return $photoTag;
	}

}

?>
