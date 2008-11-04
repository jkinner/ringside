<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */

/**
 * Servlet for serving the data required for opensocial.
 * 
 * This will expand to be more sophisticated as time goes on.
 */


require_once('ringside/opensocial/RingsidePeopleService.php' );
require_once('ringside/opensocial/RingsideDataService.php' );
require_once('ringside/opensocial/RingsideActivitiesService.php' );
require_once('ringside/opensocial/RingsideGadgetToken.php' );

class OpenSocialDataHandler extends GadgetDataHandler {
	private $handles = array('FETCH_PEOPLE', 'FETCH_PERSON_APP_DATA', 'UPDATE_PERSON_APP_DATA', 'FETCH_ACTIVITIES', 'CREATE_ACTIVITY');
	private $peopleHandler;
	private $dataHandler;
	private $activitiesHandler;
	
	public function __construct()
	{
		$this->peopleHandler = new RingsidePeopleService();
		$this->dataHandler = new RingsideDataService();
		$this->activitiesHandler = new RingsideActivitiesService();
	}
	
	public function shouldHandle($requestType)
	{
		return in_array($requestType, $this->handles);
	}
	
	public function handleRequest($request)
	{
		try {
			$params = $request->getParams();
			$type = $params['type'];
			error_log ("OPEN SOCIAL DATA HANDLER:".$type);
			$response = new ResponseItem(NOT_IMPLEMENTED, $type . " has not been implemented yet.", array());
			$idSpec = idSpec::fromJson($params['idSpec']);
			$peopleIds = $this->peopleHandler->getIds($idSpec, $request->getToken());
			error_log('peopleIds'.implode(',',$peopleIds));
			switch ( $type) {
				
				case 'FETCH_PEOPLE' :
					error_log("SOCIAL CALL:FETCH_PEOPLE");
					$profileDetail = $params["profileDetail"];
					$profileDetailFields = Array();
					foreach ( $profileDetail as $detail ) {
						//error_log("     ".$detail);
						$profileDetailFields[] = $detail;
					}
					$sortOrder = ! empty($params["sortOrder"]) ? $params["sortOrder"] : 'topFriends';
					$filter = ! empty($params["filter"]) ? $params["filter"] : 'all';
					$first = intval($params["first"]);
					$max = intval($params["max"]);
					// TODO: Should we put this in the requestitem and pass the whole
					// thing along?
					$response = $this->peopleHandler->getPeople($peopleIds, $sortOrder, $filter, $first, $max, $profileDetailFields, $request->getToken());
					break;
				
				case 'FETCH_PERSON_APP_DATA' :
				error_log("FETCH_PERSON_APP_DATA");
					$jsonKeys = $params["keys"];
					$keys = array();
					foreach ( $jsonKeys as $key ) {
						$keys[] = $key;
					}
					$response = $this->dataHandler->getPersonData($peopleIds, $keys, $request->getToken());
					break;
				
				case 'UPDATE_PERSON_APP_DATA' :
				error_log("UPDATE_PERSON_APP_DATA");
					// this is either viewer or owner right? lets hack in propper support shall we?
					// We only support updating one person right now
					$id = $peopleIds[0];
					$key = $params["key"];
					$value = ! empty($params["value"]) ? $params["value"] : '';
					$response = $this->dataHandler->updatePersonData($id, $key, $value, $request->getToken());
					break;
				
				case 'FETCH_ACTIVITIES' :
				error_log("FETCH_ACTIVITIES");
					$response = $this->activitiesHandler->getActivities($peopleIds, $request->getToken());
					break;
				
				case 'CREATE_ACTIVITY' :
					error_log("CREATE_ACTIVITY");
					$activities=$params['activity'];
					$activityArry=$activities['fields_'];
					$activity=new Activity($activityArry['id'],$peopleIds[0]);
					$activity->setBody($activityArry['body']);
					$activity->setTitle($activityArry['title']);
					$response = $this->activitiesHandler->createActivity($personId, $activity, $request->getToken());
					break;
			}
		} catch ( Exception $e ) {
			$response = new ResponseItem(BAD_REQUEST, $e->getMessage());
		}
		error_log("EXITING NORMALLY");
		return $response;
	}
}
