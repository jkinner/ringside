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
require_once ("ringside/api/dao/records/RingsideUser.php");
require_once ("ringside/api/dao/Profile.php");

class UsersProfileTestCase extends BaseDbTestCase
{
	public function testDao()
	{
		$u = new RingsideUser();
		$u->id = -1;
		$u->username = 'awesome_username';
		$u->password = md5('goats');
		$this->assertTrue($u->trySave());
		$uid = $u->getIncremented();
	
		include 'LocalSettings.php';
		$domainId = $networkKey;
				        
		$props = $this->getTestUserData();
		
		$this->assertTrue(Api_Dao_Profile::createProfile($uid, $domainId, $props));
		
		$nprops = array_keys($props);
		$p = Api_Dao_Profile::getProfile($uid, $domainId, $nprops);
		
		//print_r($p);
		
		//check all the values to make sure the input properties
		//are the same as the retrieved output properties
		$this->assertEquals($uid, $p['user_id']);
		foreach ($props as $pname => $pval) {
			if (!is_array($pval)) {
				$this->assertEquals($pval, $p[$pname],
									"Failed comparing '$pname': expected='$pval', actual='{$p[$pname]}'");
			} else {				
				foreach ($pval as $indx => $obj) {
					$this->assertTrue(array_key_exists('id', $p[$pname][$indx]));
					foreach ($obj as $aname => $aval) {
						if (($aname != 'id') && ($aname != 'domain_id')) {
							$this->assertEquals($aval, $p[$pname][$indx][$aname],
												"Failed comparing '$pname.$aname': expected='$aval', actual='{$p[$pname][$aname]}'");
						}			
					}
				}
			}
		}
		
		//update with some new values
		$schoolId = $p['schools'][0]['id'];		
		$newProps = array();
		$newProps['activities'] = 'mas cabras, mas rapido.';
		
		$s1 = array();
		$s1['id'] = $schoolId;
		$s1['grad_year'] = '1966';
        $s1['concentrations'] = 'log tossing';
		$newProps['schools'] = array($s1);
		
		$this->assertTrue(Api_Dao_Profile::updateProfile($uid, $domainId, $newProps));
		
		$p = Api_Dao_Profile::getProfile($uid, $domainId, $nprops);
		//print_r($p);
		$this->assertEquals($newProps['activities'], $p['activities']);
		$this->assertEquals($newProps['schools'][0]['id'], $schoolId);
		$this->assertEquals($newProps['schools'][0]['grad_year'], $s1['grad_year']);
		$this->assertEquals($newProps['schools'][0]['concentrations'], $s1['concentrations']);
		
		//now delete that particular school
		$newProps = array();
		$s1 = array();
		$s1['id'] = $schoolId;
		$s1['delete'] = true;
		$newProps['schools'] = array($s1);
		
		$this->assertTrue(Api_Dao_Profile::updateProfile($uid, $domainId, $newProps));
		
		$p = Api_Dao_Profile::getProfile($uid, $domainId, $nprops);
		$this->assertEquals(1, count($newProps['schools'][0]['id']));		
				
		$this->assertTrue(Api_Dao_Profile::deleteProfile($uid, $domainId));
		$this->assertTrue($u->delete());
	}

	
	protected function getTestUserData()
	{
		$props = array();
		$props['first_name'] = 'Xxx';
		$props['last_name'] = 'Yyy';
		
		$bday = strtotime('06-09-2008');
		$bday_date = date('Y-m-d', $bday);
		$props['dob'] = $bday_date;
		
		$props['sex'] = 'M';
		$props['political'] = 'Other';
			
		$props['religion'] = 'Hare Krishna';

		$lup = strtotime('now');
		$lup_date = date('Y-m-d H:i:s', $lup);		
		$props['last_updated'] = $lup_date;

		$props['timezone'] = 2;
		
		$props['status_message'] = 'writing a unit test'; 
		$props['status_update_time'] = $lup_date;
		
		$props['pic_url'] = 'http://www.3772773.org/somepic.jpg'; 
		$props['pic_big_url'] = 'http://www.3772773.org/somepic_big.jpg';
		$props['pic_small_url'] = 'http://www.3772773.org/somepic_small.jpg'; 
		$props['pic_square_url'] =  'http://www.3772773.org/somepic_square.jpg';
		$props['activities'] = 'hiking, boating, spelunking';
		$props['interests'] = 'spelunking, spelunking, spelunking';
		$props['music'] = 'life metal';
		$props['tv'] = 'CSPAN';
		$props['movies'] = 'airplane 3';
		$props['books'] = 'can\'t read';
		$props['quotes'] = '"these are quotes"';
		$props['about'] = 'a really interesting fictional person';
		$props['relationship_status'] = 'Married';
		$props['alternate_name'] = 'Ortiz';
		$props['significant_other'] = 898952;
		$props['meeting_for'] = 'Whatever I can get';
		$props['meeting_sex'] = 'M,F'; 
		$props['layout'] = 'none';
		
		
		$carr1 = array();
		$carr1['home_phone'] = '215-887-2737';
		$carr1['mobile_phone'] = '777-372-8375'; 
        $carr1['address'] = '4725 6th Street Avenue'; 
        $carr1['city'] = 'Transylvania'; 
        $carr1['state'] = 'Kentucky'; 
        $carr1['country'] = 'Unizited Tates'; 
        $carr1['zip'] = '28717'; 
        $carr1['website'] = 'www.4codersfingerpainting.com'; 
        $carr1['is_hometown'] = '1'; 
        $carr1['is_current'] = '0';
        
        $carr2 = array();
		$carr2['home_phone'] = '928-377-2857';
		$carr2['mobile_phone'] = '018-377-5723'; 
        $carr2['address'] = '82757 Henry Igloos'; 
        $carr2['city'] = 'Candy'; 
        $carr2['state'] = 'Goattown'; 
        $carr2['country'] = 'Peanut Butter'; 
        $carr2['zip'] = '98971'; 
        $carr2['website'] = 'www.2u308930829230.edu'; 
        $carr2['is_hometown'] = '0'; 
        $carr2['is_current'] = '1';
        
        $props['contact'] = array($carr1, $carr2);
        
        $e1 = array();
        $e1['contact_type'] = 'AIM';
        $e1['contact_value'] = 'u2572nnn';
        
        $e2 = array();
        $e2['contact_type'] = 'Yahoo';
        $e2['contact_value'] = 'jkajadgh@yahoo.com';
        
        $e3 = array();
        $e3['contact_type'] = 'gmail';
        $e3['contact_value'] = 'jj923jfj@gmail.com';
        
        $props['econtact'] = array($e1, $e2, $e3);
        
        $s1 = array();
        $s1['school_name'] = 'Claretown Low';
        $s1['grad_year'] = '1975';
        $s1['concentrations'] = 'debauchery,goats,arrowing';
        $s1['is_highschool'] = 0;
        
        $s2 = array();
        $s2['school_name'] = 'Outback High';
        $s2['grad_year'] = '1884';
        $s2['concentrations'] = 'brutal stabbings,echidnas';
        $s2['is_highschool'] = 1;

        $props['schools'] = array($s1, $s2);
        
        $w1 = array();
        $w1['employer'] = 'The Dog Store';
        $w1['description'] = 'We buy and sell dogs.';
        $w1['position'] = 'Dog Wholesale Manager';
        $w1['city'] = 'Obonga';
        $w1['state'] = 'HL';
        $w1['country'] = 'ZM';
        $w1['current'] = 0;
        $st = strtotime('11-29-1967');
		$st_date = date('Y-m-d', $bday);
		$w1['start_date'] = $st_date;		
		$en = strtotime('08-12-1985');
		$en_date = date('Y-m-d', $bday);
		$w1['end_date'] = $en_date;
		
		$w2 = array();
        $w2['employer'] = 'The Cat Store';
        $w2['description'] = 'We buy and sell cats.';
        $w2['position'] = 'Cat Chopper';
        $w2['city'] = 'xxxville';
        $w2['state'] = 'QQ';
        $w2['country'] = 'MM';
        $w2['current'] = 1;
        $st = strtotime('05-21-1990');
		$st_date = date('Y-m-d', $bday);
		$w2['start_date'] = $st_date;
		
		$props['work'] = array($w1, $w2);
	
		return $props;
	}
	
}
?>
