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
require_once ('BaseDbTestCase.php');
require_once ("ringside/api/dao/App.php");
require_once ("RsOpenFBDbTestUtils.php");
require_once('ringside/social/config/RingsideSocialConfig.php');

class AppTestCase extends BaseDbTestCase
{

	private function getNumApps($dbCon, $id)
	{
		$stmt = "SELECT COUNT(id) FROM app WHERE id = " . $id;
		$result = mysql_query($stmt, $dbCon);
		if(! $result)
		{
			throw new Exception(mysql_error(), FB_ERROR_CODE_DATABASE_ERROR);
		}
		$row = mysql_fetch_array($result);
		return $row[0];
	}

	private function getApp($dbCon, $id)
	{
		$stmt = "SELECT * FROM app WHERE id = " . $id;
		$result = mysql_query($stmt, $dbCon);
		if(! $result)
		{
			throw new Exception(mysql_error(), FB_ERROR_CODE_DATABASE_ERROR);
		}
		$row = mysql_fetch_array($result);
		return $row;
	}

	public function testUpdateAppProperties()
	{
		$app_id = "1200";
		
		$info = Api_Dao_App::getApplicationInfoById($app_id, RingsideSocialConfig::$apiKey);
		$res = $info[0]->toArray();
		$oldCanvasUrl = $res["canvas_url"];
		$oldAuthor = $res["author"];
		$oldDescription = $res["description"];
		
		$newSecret = "newsecret";
		$newCanvasUrl = "newcanvasurl";
		$newAuthor = "newauthor";
		$newDescription = "newdescription";
		
		$props = array();
		$props["RingsideApp.canvas_url"] = $newCanvasUrl;
		$props["RingsideApp.author"] = $newAuthor;
		$props["RingsideApp.description"] = $newDescription;
		
		Api_Dao_App::updateAppProperties($app_id, $props, RingsideSocialConfig::$apiKey);
		
		$info = Api_Dao_App::getApplicationInfoById($app_id, RingsideSocialConfig::$apiKey);
		$res = $info[0]->toArray();
		$this->assertEquals($newCanvasUrl, $res["canvas_url"]);
		$this->assertEquals($newAuthor, $res["author"]);
		$this->assertEquals($newDescription, $res["description"]);
		
		$props = array();
		$props["RingsideApp.canvas_url"] = $oldCanvasUrl;
		$props["RingsideApp.author"] = $oldAuthor;
		$props["RingsideApp.description"] = $oldDescription;
		
		Api_Dao_App::updateAppProperties($app_id, $props, RingsideSocialConfig::$apiKey);
		
		$info = Api_Dao_App::getApplicationInfoById($app_id, RingsideSocialConfig::$apiKey);
		$res = $info[0]->toArray();
		$this->assertEquals($oldCanvasUrl, $res["canvas_url"]);
		$this->assertEquals($oldAuthor, $res["author"]);
		$this->assertEquals($oldDescription, $res["description"]);
	}

	public function testinsertIntoDbdeleteFromDb()
	{
		$dbCon = RsOpenFBDbTestUtils::getDbCon();
		
		$apiKey = "apiKe";
		$callbackUrl = "dbUrl";
		$canvasUrl = "canUrl";
		$default = 1;
		$id = 9876543;
		$name = "name";
		$secretKey = "secKey";
		$sidenavUrl = "sideUrl";
		
		try
		{
			$this->assertEquals(0, $this->getNumApps($dbCon, $id));
			$id = Api_Dao_App::createApp($apiKey, $callbackUrl, $canvasUrl, $name, $default, $secretKey, $sidenavUrl, null, null);
			$this->assertEquals(1, $this->getNumApps($dbCon, $id));
			$row = $this->getApp($dbCon, $id);
			$this->assertEquals($callbackUrl, $row['callback_url']);
			$this->assertEquals($canvasUrl, $row['canvas_url']);
			$this->assertEquals($default, $row['isdefault']);
			$this->assertEquals($id, $row['id']);
			$this->assertEquals($name, $row['name']);
			$this->assertEquals($sidenavUrl, $row['sidenav_url']);
		}catch(Exception $exception)
		{
			Api_Dao_App::deleteApp($id);
			throw $exception;
		}
		Api_Dao_App::deleteApp($id);
		$this->assertEquals(0, $this->getNumApps($dbCon, $id));
	}

	private function getNumAllApps($dbCon)
	{
		$stmt = "SELECT COUNT(id) FROM app";
		$result = mysql_query($stmt, $dbCon);
		if(! $result)
		{
			throw new Exception(mysql_error(), FB_ERROR_CODE_DATABASE_ERROR);
		}
		$row = mysql_fetch_array($result);
		return $row[0];
	}

	public function testCheckUserOwnsApp()
	{
		$dbCon = RsOpenFBDbTestUtils::getDbCon();
		$this->assertTrue(Api_Dao_App::checkUserOwnsApp(9999, 1200, $dbCon));
		$this->assertFalse(Api_Dao_App::checkUserOwnsApp(2, 1200, $dbCon));
	}

	public function testautoincrementIntoDb()
	{
		$dbCon = RsOpenFBDbTestUtils::getDbCon();
		
		$apiKey = "apiKe";
		$callbackUrl = "dbUrl";
		$canvasUrl = "canUrl";
		$default = 1;
		$name = "name";
		$secretKey = "secKey";
		$sidenavUrl = "sideUrl";
		
		try
		{
			$numRows = $this->getNumAllApps($dbCon);
			$id = Api_Dao_App::createApp($apiKey, $callbackUrl, $canvasUrl, $name, $default, $secretKey, $sidenavUrl, null, null);
			$this->assertEquals($numRows + 1, $this->getNumAllApps($dbCon));
			$row = $this->getApp($dbCon, $id);
			$this->assertEquals($callbackUrl, $row['callback_url']);
			$this->assertEquals($canvasUrl, $row['canvas_url']);
			$this->assertEquals($default, $row['isdefault']);
			$this->assertEquals($id, $row['id']);
			$this->assertEquals($name, $row['name']);
			$this->assertEquals($sidenavUrl, $row['sidenav_url']);
		}catch(Exception $exception)
		{
			Api_Dao_App::deleteApp($id);
			throw $exception;
		}
		Api_Dao_App::deleteApp($id);
		$this->assertEquals($numRows, $this->getNumAllApps($dbCon));
	}

	public static function testGetInfoByIdProvider()
	{
		
		$tests[] = array(array(1299), array());
		
		$tests[] = array(array(1200), array(1200 => array('support_email' => "jack@mail.com")));
		
		$tests[] = array(array(1200, 1201), array(1200 => array('support_email' => "jack@mail.com"), 1201 => array('support_email' => "jeff@mail.com")));
		
		return $tests;
	
	}

	/**
	 * @dataProvider testGetInfoByIdProvider
	 */
	public function testGetInfoById($aids, $expected)
	{
		$appsCollection = Api_Dao_App::getApplicationInfoByIds($aids, RingsideSocialConfig::$apiKey);
		$apps = $appsCollection->toArray();
		$this->assertEquals(empty($expected), empty($apps));
		if(! empty($expected))
		{
			foreach($apps as $app)
			{
				$id = (int)$app['id'];
				$this->assertArrayHasKey($id, $expected);
				foreach($expected[$id] as $key=>$value)
				{
					$this->assertEquals($value, $app[$key]);
				}
			}
		}
	}

	public static function testGetInfoByNameProvider()
	{
		$tests = array();
		$tests[] = array("Application 1200", array("id" => "1200"));
		
		return $tests;
	}

	/**
	 * @dataProvider testGetInfoByNameProvider
	 */
	public function testGetInfoByName($name, $expected)
	{
		$info = Api_Dao_App::getApplicationInfoByName($name, RingsideSocialConfig::$apiKey);
		$apps = $info[0]->toArray();
		foreach($expected as $name=>$val)
		{
			$this->assertEquals($val, $apps[$name]);
		}
	}

	public static function testGetInfoByCanvasNameProvider()
	{
		$tests = array();
		$tests[] = array("app1200", array("id" => "1200"));
		
		return $tests;
	}

	/**
	 * @dataProvider testGetInfoByCanvasNameProvider
	 */
	public function testGetInfoByCanvasName($name, $expected)
	{
		$info = Api_Dao_App::getApplicationInfoByCanvasName($name, RingsideSocialConfig::$apiKey);
		$apps = $info[0]->toArray();
		foreach($expected as $name=>$val)
		{
			$this->assertEquals($val, $apps[$name]);
		}
	}

	// TODO: Create an equivalent test on the App BO
//	public static function testGetInfoByApiKeyProvider()
//	{
//		$tests = array();
//		$tests[] = array("application-1200", array("name" => "Application 1200"));
//		
//		return $tests;
//	}

	// TODO: Create an equivalent test on the App BO
	/**
	 * @dataProvider testGetInfoByApiKeyProvider
	 */
//	public function testGetInfoByApiKey($name, $expected)
//	{
//		$info = Api_Dao_App::getApplicationInfoByApiKey($name);
//		$apps = $info[0]->toArray();
//		foreach($expected as $name=>$val)
//		{
//			$this->assertEquals($val, $apps[$name]);
//		}
//	}

}
?>
