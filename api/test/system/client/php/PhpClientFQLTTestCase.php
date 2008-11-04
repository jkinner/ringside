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

require_once("BasePhpClientTestCase.php");

class PhpClientFQLTestCase extends BasePhpClientTestCase
{	
	public function testGroupsGet()
	{
		$flds = array("uid", "about_me", "activities", "affiliations", "books", "birthday",
						  "first_name", "interests", "last_name", "education_history", "movies",
						  "music", "pic", "political", "quotes", "relationship_status",
						  "profile_update_time", "religion", "sex", "significant_other_id",
						  "timezone", "tv", "current_location", "is_app_user", "has_added_app",
						  "hometown_location","meeting_for","meeting_sex","pic_big","pic_small",
						  "pic_square","profile_update_time","status","name","work_history",
							"wall_count","notes_count");
		
		$uid = 17001;
		$fql = "SELECT " . implode(",", $flds) . " FROM user WHERE uid=$uid";		
		
		$resp = $this->fbClient->fql_query($fql);
		
		$this->assertEquals(1, count($resp));
	}
}

?>
