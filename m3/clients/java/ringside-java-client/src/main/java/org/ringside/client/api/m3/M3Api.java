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

package org.ringside.client.api.m3;

/**
 * Enumerates the known M3 API method names.
 * 
 * @author John Mazzitelli
 */
public enum M3Api
{
    M3_INVENTORY_PING( "m3.inventory.ping" ),
    M3_INVENTORY_GET_VERSION_INFO ( "m3.inventory.getVersionInfo" ),
    M3_INVENTORY_GET_NETWORK ( "m3.inventory.getNetwork" ),
    M3_INVENTORY_GET_NETWORKS ( "m3.inventory.getNetworks" ),
    M3_INVENTORY_GET_APPLICATION ( "m3.inventory.getApplication" ),
    M3_INVENTORY_GET_APPLICATIONS ( "m3.inventory.getApplications" ),
    M3_INVENTORY_GET_API ( "m3.inventory.getApi" ),
    M3_INVENTORY_GET_APIS ( "m3.inventory.getApis" ),
    M3_INVENTORY_GET_TAG ( "m3.inventory.getTag" ),
    M3_INVENTORY_GET_TAGS ( "m3.inventory.getTags" ),
    M3_CONFIG_GET_PHP_INI_SETTINGS ( "m3.config.getPhpIniSettings" ),
    M3_CONFIG_GET_LOCAL_SETTINGS ( "m3.config.getLocalSettings" ),
    M3_CONFIG_SET_LOCAL_SETTINGS ( "m3.config.setLocalSettings" ),
    M3_METRICS_PURGE_API_DURATIONS ( "m3.metrics.purgeApiDurations" ),
    M3_METRICS_GET_API_DURATIONS ( "m3.metrics.getApiDurations" ),
    M3_OPERATION_EVALUATE_PHP_CODE ( "m3.operation.evaluatePhpCode" ),
    M3_OPERATION_GET_FILE_CONTENT ( "m3.operation.getFileContent" );

    private String methodName;
    
    M3Api( String name )
    {
    	this.methodName = name;
    }
    
    public String getMethodName()
    {
    	return this.methodName;
    }
}