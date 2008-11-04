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
require_once 'ringside/api/db/RingsideApiDbDatabase.php';
require_once ("ringside/api/fql/FQLException.php");
require_once ("ringside/api/fql/FQLEngine.php");
require_once ("ringside/api/DefaultRest.php");

class FqlQuery extends Api_DefaultRest
{
	
	protected $m_query;

	public function validateRequest()
	{
		
		$this->checkRequiredParam('query');
		$this->m_query = $this->getApiParam("query");
	}

	public function execute()
	{
		$dbCon = RingsideApiDbDatabase::getDatabaseConnection();
		$fqlEngine = FQLEngine::getInstance($dbCon);
		
		$result = null;
		try
		{
			//execute query
			$result = $fqlEngine->query($this->getAppId(), $this->getUserId(), $this->m_query);
		}catch(FQLException $exception)
		{
			throw new OpenFBAPIException($exception->getMessage(), FB_ERROR_CODE_DATABASE_ERROR);
		}
		
		return $result;
	}
}

?>
