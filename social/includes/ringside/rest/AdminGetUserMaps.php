<?php
 /*
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

require_once( "ringside/api/DefaultRest.php" );
require_once( 'ringside/social/IdMappingService.php');
/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class AdminGetUserMaps extends Api_DefaultRest
{
    private $m_aid;
    private $m_nid;
    private $m_uid;
    private $m_pid;
    
    public function validateRequest()
    {
        $this->checkOneOfRequiredParams(array('uid', 'pid'));
        $this->m_aid = $this->getApiParam('aid', $this->getNetworkId());
        $this->m_nid = $this->getApiParam('nid', $this->getNetworkId());
        $this->m_uid = $this->getApiParam('uid');
        $this->m_pid = $this->getApiParam('pid');
    }

    public function execute()
    {
        $mapper = Social_IdMappingService::create();
        
        $subjects = array();
        
        if ( null != $this->m_pid )
        {
            $subjects = $mapper->mapPrincipalToSubjects($this->m_aid, $this->m_pid);
        } else {
            $subjects = $mapper->mapSubjectToSubjects($this->m_aid, $this->m_nid, $this->m_uid);
        }
        
        $result = array();
        foreach ( $subjects as $subject )
        {
            $result['subject'][] = $subject;
        }
        
        return $result;
    }
}
?>