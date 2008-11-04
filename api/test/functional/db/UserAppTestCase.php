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
require_once ("ringside/api/dao/UsersApp.php");
require_once ("ringside/api/dao/App.php");
require_once ("ringside/api/bo/App.php");
require_once ("RsOpenFBDbTestUtils.php");
require_once ("ringside/api/dao/records/RingsideUsersApp.php");

class UserAppTestCase extends BaseDbTestCase
{

	private function getNumUserApp($appId, $userId)
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(app_id) AS aid_count')->from('RingsideUsersApp')->where("app_id = $appId AND user_id = $userId");
		$ret = $q->execute();
		
		return $ret[0]['aid_count'];
	}

	private function getUserApp($appId, $userId)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideUsersApp')->where("app_id = $appId AND user_id = $userId");
		$ret = $q->execute();
		
		return $ret[0]->toArray();
	}

	
	public function testinsertIntoDbdeleteFromDb()
	{
		$dbCon = RsOpenFBDbTestUtils::getDbCon();
		$user = RsOpenFBDbTestUtils::getUser1();
		$app = RsOpenFBDbTestUtils::getApp1();
		
		try
		{
			$user->insertIntoDb($dbCon);
		}catch(Exception $exception)
		{
			// nothing on purpose
		}
		
		$appId = $app;
		$userId = $user->getId();
		
		$id = null;
		try
		{
			$this->assertEquals(0, $this->getNumUserApp($appId, $userId));
			$id = Api_Dao_UsersApp::createUsersApp($appId, $userId);
			$this->assertEquals(1, $this->getNumUserApp($appId, $userId));
			$row = $this->getUserApp($appId, $userId);
			$this->assertEquals($id, $row['id']);
		}catch(Exception $exception)
		{
			try
			{
				Api_Dao_UsersApp::deleteUserApp($id);
			}catch(Exception $exception)
			{
				// nothing on purpose
			}
			$this->deleteUserAndApp($id, $app);
			throw $exception;
		}
		try
		{
			Api_Dao_UsersApp::deleteUserApp($id);
			$this->assertEquals(0, $this->getNumUserApp($appId, $userId));
		}catch(Exception $exception)
		{
			$this->deleteUserAndApp($id, $app);
			throw $exception;
		}
		$this->deleteUserAndApp($id, $app);
	}

	private function deleteUserAndApp($id, $app)
	{
		try
		{
			Api_Dao_UsersApp::deleteUserApp($id);
		}catch(Exception $exception)
		{
			// nothing on purpose
		}
		try
		{
			Api_Dao_App::deleteApp($app);
		}catch(Exception $exception)
		{
			// nothing on purpose
		}
	}
}
?>
