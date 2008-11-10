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

require_once('ringside/social/IdMappingService.php');
require_once('ringside/api/dao/records/RingsidePrincipal.php');
require_once('ringside/api/dao/records/RingsidePrincipalMap.php');
require_once('ringside/api/dao/tables/RingsidePrincipalTable.php');
require_once('ringside/api/dao/tables/RingsidePrincipalMapTable.php');

/**
 * Implements {@link Social_IdMappingService} using the Ringside database schema and Doctrine.
 * The service manages transactions where appropriate or uses the default Doctrine behavior for
 * simple database interactions.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class Social_DatabaseIdMappingService extends Social_IdMappingService
{
    const PRINCIPAL_COMPONENT = 'RingsidePrincipal';
    const PRINCIPAL_MAP_COMPONENT = 'RingsidePrincipalMap';
    
    private $manager;
    private $tables;
    
    public function __construct()
    {
        $this->manager = Doctrine_Manager::getInstance();
        $this->tables[self::PRINCIPAL_MAP_COMPONENT] = $this->manager->getConnectionForComponent(self::PRINCIPAL_MAP_COMPONENT)->getTable(self::PRINCIPAL_MAP_COMPONENT);
        $this->tables[self::PRINCIPAL_COMPONENT] = $this->manager->getConnectionForComponent(self::PRINCIPAL_COMPONENT)->getTable(self::PRINCIPAL_COMPONENT);
    }
    
    /**
     * Retrieves (or creates) the Doctrine manager.
     *
     * @return Doctrine_Manager the database manager servicing this service.
     */
    public function getManager()
    {
        // By default, we will use the Doctrine global context to get the Doctrine_Manager, but IoC is allowed
        if ( ! isset( $this->manager ) )
        {
            $this->manager = Doctrine_Manager::getInstance();
        }

        return $this->manager;
    }

    public function setManager(Doctrine_Manager $manager)
    {
        $this->manager = $manager;
        $this->tables[self::PRINCIPAL_MAP_COMPONENT] = $manager->getConnectionForComponent(self::PRINCIPAL_MAP_COMPONENT)->getTable(self::PRINCIPAL_MAP_COMPONENT);
        $this->tables[self::PRINCIPAL_COMPONENT] = $manager->getConnectionForComponent(self::PRINCIPAL_COMPONENT)->getTable(self::PRINCIPAL_COMPONENT);
    }

    public function mapPrincipalsToSubjects($app_id, $network_id, $principal_ids)
    {
        $results = array();
        $principals = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findByNetwork($app_id, $network_id, $principal_ids);
        foreach ( $principals as $principal )
        {
            $results[$principal->principal_id] = $principal->uid;
        }

        return $results;
    }

    public function mapPrincipalToSubjects($app_id, $principal_id)
    {
        $result = array();
        $principals = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findByIdForApp($app_id, $principal_id);
        foreach ( $principals as $principal )
        {
            $result[] = array('nid' => $principal->network_id, 'uid' => $principal->uid);
        }
        return $result;
    }
    
    public function mapSubjectToSubjects($app_id, $network_id, $uid)
    {
        $result = array();
        $subjects = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findBySubject($app_id, $network_id, $uid);
        foreach ( $subjects as $subject )
        {
            $result[] = array('nid' => $subject->network_id, 'uid' => $subject->uid);
        }
        return $result;
    }
    
    public function mapSubjectsToPrincipals($app_id, $network_id, $uids)
    {
        $results = array();
        error_log("Tables defined: ".var_export($this->tables, true));
        $principal_maps = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findBySubjectForNetwork($app_id, $network_id, $uids);
        foreach ( $principal_maps as $principal_map )
        {
            $results[$principal_map->uid] = $principal_map->principal_id;
        }

        return $results;
    }

    public function createPrincipal($app_id, $network_id, $uid)
    {
        $pid = null;
        $map = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findOneBySubject($app_id, $network_id, $uid);
        if ( $map == null )
        {
            $conn = $this->getManager()->getConnectionForComponent(self::PRINCIPAL_MAP_COMPONENT);
            $conn->beginTransaction();
    
            $pid = $this->_createPrincipal($app_id, $network_id, $uid);
            
            $conn->commit();
        }
        else
        {
            $pid = $map->principal_id;
        }
        
        return $pid;
    }

    public function deletePrincipal($pid)
    {
        $principal = $this->tables[self::PRINCIPAL_COMPONENT]->find($pid);
        $principal->delete();
    }
    
    /**
     * Creates a principal without managing database transactions. For internal use only. This method
     * is called from both the primary {@link #createPrincipal} method and from the {@link #link} method
     * if there is no existing principal to use.
     *
     * @param string $app_id the application creating the principal.
     * @param string $network_id the network associated with the user ID.
     * @param string $uid the subject's user ID on the given network.
     * @return string the created principal's ID.
     */
    private function _createPrincipal($app_id, $network_id, $uid)
    {
        $principal = new RingsidePrincipal();
        // TODO: Exception: Throw a meaningful service-level exception
        $principal->state('TDIRTY');
        $principal->save();

        $pid = $principal->id;

        $principal_map = new RingsidePrincipalMap();
        $principal_map->principal_id = $pid;
        $principal_map->app_id = $app_id;
        $principal_map->network_id = $network_id;
        $principal_map->uid = $uid;
        $principal_map->save();

        return $pid;
    }

    public function link($app_id, $existing_network_id, $existing_uid, $new_network_id, $new_uid)
    {
        $conn = $this->getManager()->getConnectionForComponent(self::PRINCIPAL_MAP_COMPONENT);
        $conn->beginTransaction();
        $manager = $this->getManager();

        try
        {
            // Find a mapping for either (or both!) subject(s)
            $existing_map = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findOneBySubject($app_id, $existing_network_id, $existing_uid);
            $new_map = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findOneBySubject($app_id, $new_network_id, $new_uid);
            $existing_principal_id = isset($existing_map) && false !== $existing_map?$existing_map->principal_id:null;
            $new_principal_id = isset($new_map) && false !== $new_map?$new_map->principal_id:null;
            if ( null != $new_principal_id )
            {
                if ( null == $existing_principal_id )
                {
                    error_log("Existing principal is $existing_principal_id and new principal is $new_principal_id (reversing them)");
                    // There is a new_principal_id, but there is no existing_principal_id; swap them to match the semantics of this call
                    $hold_principal_id = $new_principal_id;
                    $hold_network_id = $new_network_id;
                    $hold_uid = $new_uid;
                    $new_principal_id = $existing_principal_id;
                    $new_network_id = $existing_network_id;
                    $new_uid = $existing_uid;
                    $existing_principal_id = $hold_principal_id;
                    $existing_network_id = $hold_network_id;
                    $existing_uid = $hold_uid;
                }
            }

            /*
             * The state at this point is that we have ensured that existing_principal_id exists (if there is any principal)
             * and is set to the proper value. Otherwise, existing_principal_id will be empty, meaning we need to create a principal.
             */
            	
            if ( null == $existing_principal_id )
            {
                error_log("For $existing_principal_id and $new_principal_id ($existing_network_id, $existing_uid, $new_network_id, $new_uid)");
                // Since we already have a transaction started, don't start a new one; use the internal method
                $existing_principal_id = $this->_createPrincipal($app_id, $existing_network_id, $existing_uid);
            }
            	
            $map = new RingsidePrincipalMap();
            $map->app_id = $app_id;
            $map->principal_id = $existing_principal_id;
            $map->network_id = $new_network_id;
            $map->uid = $new_uid;
            $map->save();
            // Note the notification happens
            if ( ! empty($new_principal_id) && $new_principal_id != $existing_principal_id )
            {
                // TODO: Send a notification to the application that a mapping change has occurred
            }
            $conn->commit();
            return $existing_principal_id;
        }
        catch ( Exception $e )
        {
            $conn->rollback();
            throw $e;
        }
    }

    public function unlink($app_id, $network_id, $uid)
    {
        $principal_id = null;
        $map = $this->tables[self::PRINCIPAL_MAP_COMPONENT]->findOneBySubject($app_id, $network_id, $uid);
        if ( null != $map )
        {
            $principal_id = $map->id;
            $map->delete();
        }

        return $principal_id;
    }
}
?>
