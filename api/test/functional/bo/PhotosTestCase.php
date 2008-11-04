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

class PhotosTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetPhotoTags()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetPhotoTags($pids)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreatePhotoTag()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreatePhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, $created = null)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetNumberOfPhotots()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetNumberOfPhotots($aid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetPhotos()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetPhotos($pids = array(), $albumId = null, $subjectId = null)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAlbums()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAlbums($aids, $uid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateAlbum()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateAlbum($cover_pid, $created = null, $description, $location, $modified = null, $name, $owner)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateAlbumLink()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateAlbumLink($albumId, $userId)
	{
	
	}
}
?>
