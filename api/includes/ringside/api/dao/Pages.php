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
 */
require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsidePagesFan.php');
require_once ('ringside/api/dao/records/RingsidePagesInfo.php');
require_once ('ringside/api/dao/records/RingsidePage.php');
require_once ('ringside/api/dao/records/RingsidePagesApp.php');

/**
 * Represents a row in the OpenFB users table.
 */
class Api_Dao_Pages
{
	const RS_FBDB_PAGES_APPS_ENABLED = 1;
	const RS_FBDB_PAGES_APPS_DISABLED = 0;
	
	const RS_FBDB_PAGES_ADMIN_ENABLED = 1;
	const RS_FBDB_PAGES_ADMIN_DISABLED = 0;
	
	const RS_FBDB_PAGES_FAN_ENABLED = 1;
	const RS_FBDB_PAGES_FAN_DISABLED = 0;
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pageIds
	 * @param unknown_type $fields
	 * @return unknown
	 */
	public static function getPagesByIds($pageIds, $fields)
	{
		/* The join SQL
		 select * from pages p LEFT JOIN pages_info pi ON p.page_id = pi.page_id
			where p.page_id in (2050, 2051, 2002) AND pi.name in ('products', 'founded') order by p.page_id
		 */
		$csFields = implode("' , '", $fields);
		$csPageIds = implode(",", $pageIds);

		$q = Doctrine_Query::create();
		$q->select('p.page_id, p.name, p.type, p.pic_url, p.published, pi.name, pi.value, pi.json_encoded')
			->from("RingsidePage p LEFT JOIN p.RingsidePagesInfo pi ON p.page_id = pi.page_id AND pi.name in ('$csFields')")
			->where("p.page_id in ($csPageIds)")
			->orderby('p.page_id');
		$pages = $q->execute();
		
		return $pages;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $fields
	 * @return unknown
	 */
	public static function getPagesByUid($uid, $fields)
	{
		/* The join SQL
		 select p.page_id, p.name, p.type, p.pic_url, p.published, pi.name as field_name, pi.value as field_value, pi.json_encoded
		from pages p LEFT JOIN pages_info pi ON p.page_id = pi.page_id LEFT JOIN pages_fans f ON p.page_id = f.page_id
		where f.fan = 1 and f.uid = 2110 AND pi.name in ('founded', 'products')
		 */
		$csFields = implode("' , '", $fields);

		$q = Doctrine_Query::create();
		$q->select('p.page_id, p.name, p.type, p.pic_url, p.published, pi.name, pi.value, pi.json_encoded')
			->from("RingsidePage p LEFT JOIN p.RingsidePagesInfo pi ON p.page_id = pi.page_id AND pi.name in ('$csFields') LEFT JOIN p.RingsidePagesFan f ON p.page_id = f.page_id AND f.uid = $uid")
			->where("f.fan = 1")
			->orderby('p.page_id');
		$pages = $q->execute();
		
		return $pages;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $fields
	 * @param unknown_type $pageIds
	 * @return unknown
	 */
	public static function getPagesByUidAndPageIds($uid, $fields, $pageIds = null)
	{
		/* The join SQL
		 select p.page_id, p.name, p.type, p.pic_url, p.published, pi.name as field_name, pi.value as field_value, pi.json_encoded
		from pages_fans f, pages p LEFT JOIN pages_info pi ON p.page_id = pi.page_id
		where p.page_id in (2050, 2051, 2002) AND p.page_id = f.page_id and f.fan = 1 and f.uid = 2110 AND pi.name in ('products', 'founded') order by p.page_id
		 */
		$csPageIds = implode(",", $pageIds);
		$csFields = implode("' , '", $fields);
		
		$q = Doctrine_Query::create();
		$q->select('p.page_id, p.name, p.type, p.pic_url, p.published, pi.name, pi.value, pi.json_encoded')
			->from("RingsidePage p LEFT JOIN p.RingsidePagesInfo pi ON p.page_id = pi.page_id AND pi.name in ('$csFields') LEFT JOIN p.RingsidePagesFan f ON p.page_id = f.page_id")
			->where("p.page_id in ($csPageIds) AND f.fan = 1 and f.uid = $uid")
			->orderby('p.page_id');
		$pages = $q->execute();
		
		return $pages;
	}

	/**
	 * Returns whether this page is mapped to this 
	 *
	 * @param unknown_type $page_id
	 * @param unknown_type $app_id
	 * @return unknown
	 */
	public static function hasApp($page_id, $app_id)
	{
		$q = Doctrine_Query::create();
		$q->select('enabled, page_id')->from('RingsidePagesApp pa')->where("app_id = $app_id AND page_id = $page_id");
		$pa = $q->execute();
		
		if(count($pa) > 0)
		{
			$enabled = $pa[0]->enabled;
			if($enabled == 1)
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Does this user have this PAGE and are they an admin.
	 *
	 * @param integer $page_id
	 * @param integer $uid
	 * @return boolean
	 */
	public static function isAdmin($page_id, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('admin')->from('RingsidePagesFan f')->where("page_id = $page_id and uid = $uid");
		$pages = $q->execute();
		
		if(count($pages) > 0)
		{
			if($pages [0]->admin > 0)
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Does this user have this PAGE and are they a fan.
	 *
	 * @param integer $page_id
	 * @param integer $uid
	 * @return boolean
	 */
	public static function isFan($page_id, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('fan')->from('RingsidePagesFan f')->where("page_id = $page_id and uid = $uid");
		$pages = $q->execute();
		
		if(count($pages) > 0)
		{
			if($pages [0]->fan > 0)
			{
				return true;
			}
		}
		
		return false;
	}
}
?>