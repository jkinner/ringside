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
define('NOT_IMPLEMENTED', "notImplemented");
define('UNAUTHORIZED', "unauthorized");
define('FORBIDDEN', "forbidden");
define('BAD_REQUEST', "badRequest");
define('INTERNAL_ERROR', "internalError");

class RingsideDataService extends DataService {
	
	public function getPersonData($ids, $keys, $token)
	{
				
		$client=$token->getAppClient();		
		
		$data = array();
		foreach ( $ids as $id ) {

			$personData = array();
			foreach ( $keys as $key ) {
				//TODO support getting other peoples keys
				//TODO support facebook compatible storage (ie numbers)
				$resp = $client->data_getUserPreference($key);
				if($resp==""){
					return new ResponseItem(BAD_REQUEST, "The person data key does not exist.", null);
				}
				$personData[$key] = $resp;
			}
			$data[$id] = $personData;
		}
		return new ResponseItem(null, null, $data);
	}
	
	public function updatePersonData($id, $key, $value, $token)
	{
		$client=$token->getAppClient();		
		
		$resp=$client->data_setUserPreference($key, $value);
		
		//		if (! RingsideDataService::isValidKey($key)) {
		//			return new ResponseItem(BAD_REQUEST, "The person data key had invalid characters", null);
		//		}
		//		XmlStateFileFetcher::get()->setAppData($id, $key, $value);
		
		return new ResponseItem(null, null, array());
	}
	
	/**
	 * Determines whether the input is a valid key. Valid keys match the regular
	 * expression [\w\-\.]+.
	 * 
	 * @param key the key to validate.
	 * @return true if the key is a valid appdata key, false otherwise.
	 */
	public static function isValidKey($key)
	{
		if (empty($key)) {
			return false;
		}
		for($i = 0; $i < strlen($key); ++ $i) {
			$c = substr($key, $i, 1);
			if (($c >= 'a' && $c <= 'z') || ($c >= 'A' && $c <= 'Z') || ($c >= '0' && $c <= '9') || ($c == '-') || ($c == '_') || ($c == '.')) {
				continue;
			}
			return false;
		}
		return true;
	}
}

?>