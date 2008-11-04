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

import java.net.URL;
import java.util.Map;
import java.util.Properties;

import javax.xml.bind.JAXBElement;

import org.ringside.client.ApiMethod;
import org.ringside.client.BaseRequestHandler;
import org.ringside.client.Client;
import org.ringside.client.JaxbResponseHandler;
import org.ringside.client.M3JaxbResponseHandler;
import org.ringside.client.Parameters;
import org.ringside.client.RestSender;
import org.ringside.client.StringResponseHandler;
import org.ringside.client.XmlResponseHandler;
import org.ringside.client.api.m3.schema.ConfigGetLocalSettingsResponse;
import org.ringside.client.api.m3.schema.ConfigGetPhpIniSettingsResponse;
import org.ringside.client.api.m3.schema.ErrorResponse;
import org.ringside.client.api.m3.schema.InventoryGetApiResponse;
import org.ringside.client.api.m3.schema.InventoryGetApisResponse;
import org.ringside.client.api.m3.schema.InventoryGetApplicationResponse;
import org.ringside.client.api.m3.schema.InventoryGetApplicationsResponse;
import org.ringside.client.api.m3.schema.InventoryGetNetworkResponse;
import org.ringside.client.api.m3.schema.InventoryGetNetworksResponse;
import org.ringside.client.api.m3.schema.InventoryGetTagResponse;
import org.ringside.client.api.m3.schema.InventoryGetTagsResponse;
import org.ringside.client.api.m3.schema.InventoryGetVersionInfoResponse;
import org.ringside.client.api.m3.schema.MetricsGetApiDurationsResponse;
import org.ringside.client.util.Base64;
import org.w3c.dom.Document;

/**
 * A client that allows you to invoke M3 APIs on a Ringside server.
 * You can invoke these APIs and obtain either an XML DOM document
 * or a JAXB POJO object as the response.
 * 
 * @author John Mazzitelli
 */
public class M3Client
    extends Client
{
    // the sender objects that are used to actually send the requests to the server
    private RestSender<Document> xmlSender;
    private RestSender<Object>   jaxbSender;

    public M3Client( URL        serverAddr,
                     String     secret,
                     Properties config )
    {
        super( serverAddr, secret, config );

        this.xmlSender  = createXmlSender();
        this.jaxbSender = createJaxbSender();
    }

    /**
     * Creates a sender object that will be used to process M3 API responses as raw Strings.
     * This is usually used for debugging REST methods by examining the raw responses without
     * them first going through a parsing phase.
     * 
     * @return the String {@link RestSender}
     */
    private RestSender<String> createStringSender()
    {
        BaseRequestHandler    requestHandler  = new BaseRequestHandler();
        StringResponseHandler responseHandler = new StringResponseHandler();
        return new RestSender<String>( this.getContext(), requestHandler, responseHandler );
    }

    /**
     * Creates a sender object that will be used to process M3 API responses as DOM documents.
     * @return the XML {@link RestSender}
     */
    private RestSender<Document> createXmlSender()
    {
        BaseRequestHandler requestHandler  = new BaseRequestHandler();
        XmlResponseHandler responseHandler = new XmlResponseHandler();
        return new RestSender<Document>( this.getContext(), requestHandler, responseHandler );
    }

    /**
     * Creates a sender object that will be used to process M3 API responses as JAXB POJOs.
     * @return the JAXB {@link RestSender}
     */
    private RestSender<Object> createJaxbSender()
    {
        BaseRequestHandler    requestHandler  = new BaseRequestHandler();
        M3JaxbResponseHandler responseHandler = new M3JaxbResponseHandler();
        responseHandler.getJaxbContext(); // force the context to load now
        return new RestSender<Object>( this.getContext(), requestHandler, responseHandler );
    }

    /**
     * Provides a generic way to call any M3 method in a loosely typed manner.
     * You provide the method to call and the parameters and this will invoke that
     * API on the Ringside server and return the raw response as a String.
     * 
     * A typical use case of this method is if you want to invoke an API on the server,
     * but you do not want the response to go through a DOM and/or JAXB parsing stage.
     * This is usually most useful when debugging.
     * 
     * @param method the method to invoke
     * @param params the parameters to pass to the method
     * 
     * @return the raw XML response as a String
     * 
     * @throws Exception if the response failed to be received
     */
    public String callMethodString( String     method,
                                    Parameters params )
    throws Exception
    {
        String response = createStringSender().callRestMethod( new ApiMethod( method ), params );
        return response;
    }

    /**
     * Provides a generic way to call any M3 method in a loosely typed manner.
     * You provide the method to call and the parameters and this will invoke that
     * API on the Ringside server and return the DOM document response.
     * 
     * A typical use case of this method is if you want to invoke an API on the server,
     * but its a relatively new API and there is no schema for it yet or the Java
     * client hasn't been rebuilt in order to get the JAXB generated classes
     * to represent the new API response.
     * 
     * @param method the method to invoke
     * @param params the parameters to pass to the method
     * 
     * @return the XML response as a DOM document
     * 
     * @throws Exception if the response failed to be sent and processed properly
     */
    public Document callMethodXml( String     method,
                                   Parameters params )
    throws Exception
    {
        Document response = this.xmlSender.callRestMethod( new ApiMethod( method ), params );

        // TODO: if the response is an <error_response> then throw an exception here
        return response;
    }

    /**
     * Provides a generic way to call any M3 method in a loosely typed manner.
     * You provide the method to call and the parameters and this will invoke that
     * API on the Ringside server and return the JAXB POJO object response.
     * 
     * @param method the method to invoke
     * @param params the parameters to pass to the method
     * 
     * @return the response as a POJO generated by JAXB
     * 
     * @throws Exception if the response failed to be sent and processed properly
     *
     * @throws ErrorResponseException if the server responded with an error
     */
    public Object callMethodJaxb( String     method,
                                  Parameters params )
    throws Exception
    {
        Object response = this.jaxbSender.callRestMethod( new ApiMethod( method ), params );

        if ( response instanceof ErrorResponse )
        {
            ErrorResponse error = (ErrorResponse) response;
            throw new ErrorResponseException( "Method [" + method + "] failed.", error );
        }

        return response;
    }

    /**
     * Given a JAXB object, this will return an XML representation of that object.
     * 
     * @param obj the JAXB object to stringify as an XML test document
     *
     * @return the XML text that represents the JAXB object.
     */
    public String stringifyJaxbObject( Object obj )
    {
        return ( (JaxbResponseHandler) this.jaxbSender.getResponseHandler() ).stringify( obj );
    }

    /**
     * Simply pings the Ringside Server.
     * @return <code>true</code> if the server can be pinged; false if it could not be contacted
     * @throws Exception
     */
    public boolean inventoryPing()
    throws Exception
    {
        try
        {
            callMethodJaxb( M3Api.M3_INVENTORY_PING.getMethodName(), null );
            return true; // the mere fact that this returned OK means we pinged it successfully
        }
        catch ( Exception e )
        {
            return false;
        }
    }

    /**
     * Returns the list of APIs deployed and available in the Ringside server.
     * @return the list of APIs response
     * @throws Exception
     */
    public InventoryGetApisResponse inventoryGetApis()
    throws Exception
    {
        String                   methodName = M3Api.M3_INVENTORY_GET_APIS.getMethodName();
        InventoryGetApisResponse response   = (InventoryGetApisResponse) callMethodJaxb( methodName,
                                                                                         null );
        return response;
    }

    /**
     * Returns information on an API deployed and available in the Ringside server.
     * @param apiName the dot-notation API name
     * @return the API information
     * @throws Exception
     */
    public InventoryGetApiResponse inventoryGetApi( String apiName )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "apiName", apiName );

        String                  methodName = M3Api.M3_INVENTORY_GET_API.getMethodName();
        InventoryGetApiResponse response   = (InventoryGetApiResponse) callMethodJaxb( methodName, params );
        return response;
    }

    /**
     * Returns the list of applications that are known to the Ringside server.
     * @return the list of applications response
     * @throws Exception
     */
    public InventoryGetApplicationsResponse inventoryGetApplications()
    throws Exception
    {
        String                           methodName = M3Api.M3_INVENTORY_GET_APPLICATIONS.getMethodName();
        InventoryGetApplicationsResponse response   = (InventoryGetApplicationsResponse) callMethodJaxb( methodName,
                                                                                                         null );
        return response;
    }

    /**
     * Returns information on a deployed application known to the Ringside Server.
     * @param appId the application ID
     * @return the application information
     * @throws Exception
     */
    public InventoryGetApplicationResponse inventoryGetApplication( int appId )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "appId", Integer.toString( appId ) );

        String                          methodName = M3Api.M3_INVENTORY_GET_APPLICATION.getMethodName();
        InventoryGetApplicationResponse response   = (InventoryGetApplicationResponse) callMethodJaxb( methodName,
                                                                                                       params );
        return response;
    }

    /**
     * Returns the list of networks that are known to the Ringside server.
     * @return the list of networks response
     * @throws Exception
     */
    public InventoryGetNetworksResponse inventoryGetNetworks()
    throws Exception
    {
        String                       methodName = M3Api.M3_INVENTORY_GET_NETWORKS.getMethodName();
        InventoryGetNetworksResponse response   = (InventoryGetNetworksResponse) callMethodJaxb( methodName,
                                                                                                 null );
        return response;
    }

    /**
     * Returns information on a network that is known to the Ringside server.
     * @param networkId identifies the network
     * @return the network's information
     * @throws Exception
     */
    public InventoryGetNetworkResponse inventoryGetNetwork( int networkId )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "networkId", Integer.toString( networkId ) );

        String                      methodName = M3Api.M3_INVENTORY_GET_NETWORK.getMethodName();
        InventoryGetNetworkResponse response   = (InventoryGetNetworkResponse) callMethodJaxb( methodName,
                                                                                               params );
        return response;
    }

    /**
     * Returns the list of tags deployed and available in the Ringside server.
     * @return the list of tags response
     * @throws Exception
     */
    public InventoryGetTagsResponse inventoryGetTags()
    throws Exception
    {
        String                   methodName = M3Api.M3_INVENTORY_GET_TAGS.getMethodName();
        InventoryGetTagsResponse response   = (InventoryGetTagsResponse) callMethodJaxb( methodName,
                                                                                         null );
        return response;
    }

    /**
     * Returns information on a tag deployed and available in the Ringside server.
     * @param tagNamespace the namespace of the deployed tag
     * @param tagName the name of the deployed tag
     * @return the deployed tag's information
     * @throws Exception
     */
    public InventoryGetTagResponse inventoryGetTag( String tagNamespace,
                                                    String tagName )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "tagNamespace", tagNamespace );
        params.put( "tagName", tagName );

        String                  methodName = M3Api.M3_INVENTORY_GET_TAG.getMethodName();
        InventoryGetTagResponse response   = (InventoryGetTagResponse) callMethodJaxb( methodName, params );
        return response;
    }

    /**
     * Returns the version info of the Ringside server.
     * @return the version info response
     * @throws Exception
     */
    public InventoryGetVersionInfoResponse inventoryGetVersionInfo()
    throws Exception
    {
        String                          methodName = M3Api.M3_INVENTORY_GET_VERSION_INFO.getMethodName();
        InventoryGetVersionInfoResponse response   = (InventoryGetVersionInfoResponse) callMethodJaxb( methodName,
                                                                                                       null );
        return response;
    }

    /**
     * Returns the local settings of the Ringside server.
     * @return the local settings response
     * @throws Exception
     */
    public ConfigGetLocalSettingsResponse configGetLocalSettings()
    throws Exception
    {
        String                         methodName = M3Api.M3_CONFIG_GET_LOCAL_SETTINGS.getMethodName();
        ConfigGetLocalSettingsResponse response   = (ConfigGetLocalSettingsResponse) callMethodJaxb( methodName,
                                                                                                     null );
        return response;
    }

    /**
     * Sets the local settings of the Ringside server to the given full set of configuration values.
     * You must pass in the full set of configuration items, whether or
     * not you are changing the values from their currently existing ones.
     * This means you should get the full config from {@link #configGetLocalSettings()} first,
     * then change the values you want to change and pass the full set of config settings
     * to this method. It will be assumed that any
     * settings that do not exist in <code>newSettings</code> that exist in the current
     * configuration are to be set to null (i.e "unset").

     * @param newSettings the new configuration settings
     * @return true if the set was successful. An exception will usually be thrown on error.
     * @throws Exception
     */
    @SuppressWarnings( "unchecked" )
    public boolean configSetLocalSettings( Map<String, String> newSettings )
    throws Exception
    {
        // its possible some config settings are arrays (e.g. GLOBAL['facebook']['debug']=0).
        // That would convert to a real array in $_POST on the server, which we do not want.
        // To get this to be sent as-is, convert all [ chars to _m3lsqb_
        // (as per phpdoc of the Ringside REST API implementation class)
        Parameters params = new Parameters();
        for ( Map.Entry<String, String> entry : newSettings.entrySet() )
        {
            String key = entry.getKey().replace( "[", "_m3lsqb_" );
            params.put( key, entry.getValue() );
        }

        String               methodName  = M3Api.M3_CONFIG_SET_LOCAL_SETTINGS.getMethodName();
        JAXBElement<Integer> response    = (JAXBElement<Integer>) callMethodJaxb( methodName, params );
        int                  numResponse = ( (Number) response.getValue() ).intValue();
        return ( numResponse != 0 );
    }

    /**
     * Returns the php.ini settings of the Ringside server.
     * @return the php.ini settings response
     * @throws Exception
     */
    public ConfigGetPhpIniSettingsResponse configGetPhpIniSettings()
    throws Exception
    {
        String                          methodName = M3Api.M3_CONFIG_GET_PHP_INI_SETTINGS.getMethodName();
        ConfigGetPhpIniSettingsResponse response   = (ConfigGetPhpIniSettingsResponse) callMethodJaxb( methodName,
                                                                                                       null );
        return response;
    }

    /**
     * Returns the API duration metrics.
     * @return the duration response
     * @throws Exception
     */
    public MetricsGetApiDurationsResponse metricsGetApiDurations()
    throws Exception
    {
        String                         methodName = M3Api.M3_METRICS_GET_API_DURATIONS.getMethodName();
        MetricsGetApiDurationsResponse response   = (MetricsGetApiDurationsResponse) callMethodJaxb( methodName,
                                                                                                     null );
        return response;
    }

    /**
     * Purges the API duration metrics currently cached in the Ringside server.
     * 
     * @return true if the purge was successful, false otherwise.
     * @throws Exception
     */
    @SuppressWarnings( "unchecked" )
    public boolean metricsPurgeApiDurations()
    throws Exception
    {
        String               methodName  = M3Api.M3_METRICS_PURGE_API_DURATIONS.getMethodName();
        JAXBElement<Integer> response    = (JAXBElement<Integer>) callMethodJaxb( methodName, null );
        int                  numResponse = ( (Number) response.getValue() ).intValue();
        return ( numResponse != 0 );
    }

    /**
     * Evaluates PHP code on the Ringside Server.
     * 
     * @param phpCode
     * 
     * @return the results of the code evaluation
     * @throws Exception
    */
    @SuppressWarnings( "unchecked" )
    public String operationEvaluatePhpCode( String phpCode )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "phpCode", phpCode );

        String              methodName = M3Api.M3_OPERATION_EVALUATE_PHP_CODE.getMethodName();
        JAXBElement<String> response   = (JAXBElement<String>) callMethodJaxb( methodName, params );
        String              results    = response.getValue();
        return results;
    }

    /**
     * Gets the content of a file deployed in the Ringside Server.
     * 
     * @param pathname the relative path of the file to retrieve
     * @param useIncludePath if <code>true</code>, the file will be found on the include path; otherwise
     *                       the <code>pathname</code> is assumed to be relative to the Ringside Server's
     *                       install directory
     * 
     * @return the content of the file
     * @throws Exception
     */
    @SuppressWarnings( "unchecked" )
    public String operationGetFileContent( String  pathname,
                                           Boolean useIncludePath )
    throws Exception
    {
        Parameters params = new Parameters();
        params.put( "pathname", pathname );
        if ( useIncludePath != null )
        {
            params.put( "useIncludePath", useIncludePath.toString() );
        }

        String              methodName = M3Api.M3_OPERATION_GET_FILE_CONTENT.getMethodName();
        JAXBElement<String> response   = (JAXBElement<String>) callMethodJaxb( methodName, params );
        String              results    = response.getValue();
        results = new String( Base64.decode( results ) );
        return results;
    }
}