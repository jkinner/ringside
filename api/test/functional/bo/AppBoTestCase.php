<?php
/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 *
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attri ftware Foundation; either version 2.1 of
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
require_once('ringside/api/bo/App.php');

class AppBoTestCase extends BaseDbTestCase 
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetUserAppSession()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetUserAppSession($uid, $aid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetUserAppSession()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetUserAppSession($aid, $uid, $infinite = 0, $key)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForDeleteUserAppSession()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testDeleteUserAppSession($aid, $uid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetUserApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetUserApp($userId, $aid = null)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCheckUserHasPermission()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCheckUserHasPermission($api_key, $userId, $ext_perm)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForIsUsersApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testIsUsersApp($appId, $userId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForRemoveUsersApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testRemoveUsersApp($userId, $appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetUsersApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetUsersApp($app_id, $user_id, $allows_status_update = 0, $allows_create_listing = 0, $allows_photo_upload = 0, $auth_information = 0, $auth_profile = 0, $auth_leftnav = 0, $auth_newsfeeds = 0, $profile_col = 'wide', $profile_order = 0)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateApp($api_key, $callback_url, $canvas_url, $name, $default = 0, $secret_key, $sidenav_url, $icon_url = null, $canvas_type = 0, $desktop = 0, $developer_mode = 0, $author = null, $author_url = null, $author_description = null, $support_email = null, $application_type = null, $mobile = 0, $deployed = 0, $description = null, $default_fbml = null, $tos_url = null, $postadd_url = null, $postremove_url = null, $privacy_url = null, $ip_list = null, $about_url = null, $logo_url = null, $edit_url = null, $default_column = 1, $attachment_action = null, $attachment_callback_url = null)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForUpdateAppProperties()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testUpdateAppProperties($apiKey, $props)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationListByUserId()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationListByUserId($userId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAllApplications()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAllApplications()
	{
	
	}

	public function testGetAllApplicationsAndKeys()
	{
	    $apps = Api_Bo_App::getAllApplicationsAndKeys();
	    $this->assertGreaterThan(0, count($apps));
	    foreach ( $apps as $app )
	    {
	        $this->assertArrayHasKey('keys', $app);
	    }
	}
	
	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationInfoByName()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationInfoByName($applicationName)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationInfoByCanvasName()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationInfoByCanvasName($applicationCanvasName)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationInfoByApiKey()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationInfoByApiKey($apiKey)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationInfoById()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationInfoById($appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForRemoveAppByApiKey()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testRemoveAppByApiKey($apiKey)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCheckUserOwnsApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCheckUserOwnsApp($userId, $appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAppIdByApiKey()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAppIdByApiKey($apiKey)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAppIdByApiKeyAndSecret()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAppIdByApiKeyAndSecret($apiKey, $secret)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAppIdByName()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAppIdByName($name)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetApplicationPreferences()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetApplicationPreferences($appId, $userId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForHasAppPrefs()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testHasAppPrefs($appId, $userId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSaveAppPrefs()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSaveAppPrefs($appId, $userId, $prefs)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetAppKeys()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetAppKeys($new_api_key, $new_secret_key, $current_api_key)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetUsersAppKeys()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetUsersAppKeys($userId, $appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetUsersAppKeys()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetUsersAppKeys($userId, $app_id, $keyProps)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFBML()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFBML($userId, $appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetFBML()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetFBML($userId, $appId, $fbml)
	{
	
	}
}
?>
