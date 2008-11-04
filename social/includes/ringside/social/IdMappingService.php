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

require_once('ringside/ServiceFactory.php');

/**
 * Service interface for performing identity mapping. This service is primarily used
 * by the REST APIs that implement identity mapping.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
abstract class Social_IdMappingService
{
    const DEFAULT_MAPPING_SERVICE = 'Social_DatabaseIdMappingService';

    /**
     * Creates the appropriate mapper service for the current environment.
     *
     * @param string $mapperClass the class to instantiate; overrides the current configuration.
     * @return Social_IdMappingService
     */
    public static function create($mapperClass = null)
    {
        return ServiceFactory::create('Social_IdMappingService', self::DEFAULT_MAPPING_SERVICE, $mapperClass);
    }

    /**
     * For a given application, takes the set of principal IDs and maps them into subjects
     * on the given network. If a mapping does not exist for that application on that network
     * for the given principal, there will be no mapping information returned.
     *
     * @param string $app_id the application performing the mapping.
     * @param string $network_id the network for which the application is querying for subjects.
     * @param array $principal_ids the set of principals to map.
     * @return array associative array of pid => uid
     */
    public abstract function mapPrincipalsToSubjects($app_id, $network_id, $principal_ids);

    public abstract function mapPrincipalToSubjects($app_id, $principal_id);
    
    public abstract function mapSubjectToSubjects($app_id, $network_id, $uid);
    
    /**
     * For a given application, takes the set of subject IDs and for a given network and maps
     * them into principals. If a mapping does not exist for that application on that network
     * for the given subject, there will be no mapping information returned.
     *
     * @param string $app_id the application performing the mapping.
     * @param string $network_id the network for which the application is querying for principals.
     * @param array $uids the set of subjects (on the given network) to map.
     * @return array associative array of uid => pid
     */
    public abstract function mapSubjectsToPrincipals($app_id, $network_id, $uids);

    /**
     * Creates a stand-alone principal for the given subject on the given network. This principal
     * will be eligible for mapping and will be reused if the subject is subsequently linked to
     * a subject on another network.
     *
     * @param string $app_id the application performing the mapping.
     * @param string $network_id the network for which the principal is being created.
     * @param unknown_type $uid the subject for whom the principal is being created.
     * @return string the principal ID.
     */
    public abstract function createPrincipal($app_id, $network_id, $uid);

    /**
     * Deletes a principal and ALL related subjects. Use cautiously.
     *
     * @param string $pid the ID of the principal to delete.
     */
    public abstract function deletePrincipal($pid);

    /**
     * Associated a subject from one network with a subject from another network. If neither subject
     * is associated with a principal, a new principal is created automatically. If either subject
     * is already associated with a principal, that principal is used to associate the subjects. If both
     * subjects are already associated with different principals, then principal associated with the first
     * subject (<code>existing_network_id</code>, <code>existing_uid</code>) will be preferred, and the
     * second subject (<code>new_network_id</code>, <code>new_uid<code>) will be re-linked to the existing
     * principal and the application will be notified that the relinking has occurred.
     *
     * @param string $app_id the application performing the mapping.
     * @param string $existing_network_id the existing subject's network ID.
     * @param string $existing_uid the existing subject's user ID on the existing network.
     * @param string $new_network_id the new subject's network ID.
     * @param string $new_uid the new subject's user ID on the new network.
     * @return string the principal ID used to link the subjects.
     */
    public abstract function link($app_id, $existing_network_id, $existing_uid, $new_network_id, $new_uid);

    /**
     * Disassociates a subject from its principal. The principal will remain, but mapping requests for
     * the given subject will no longer return the principal. Likewise, mapping requests for the principal
     * on the subject's network will no longer return the subject.
     *
     * @param string $app_id the application performing the mapping.
     * @param string $network_id the network in which the uid is defined.
     * @param string $uid the subject's user ID on the given network.
     */
    public abstract function unlink($app_id, $network_id, $uid);
}
?>