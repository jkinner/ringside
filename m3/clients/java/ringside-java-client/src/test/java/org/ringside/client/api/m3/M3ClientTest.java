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
import java.util.HashMap;
import java.util.Map;

import org.ringside.client.api.m3.schema.ApiComplexType;
import org.ringside.client.api.m3.schema.ApplicationComplexType;
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
import org.ringside.client.api.m3.schema.NetworkComplexType;
import org.ringside.client.api.m3.schema.TagComplexType;
import org.ringside.client.api.m3.schema.ConfigGetLocalSettingsResponse.LocalSetting;
import org.ringside.client.api.m3.schema.MetricsGetApiDurationsResponse.ApiDuration;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;
import org.w3c.dom.Document;

@Test( groups = "system" )
public class M3ClientTest
{
    private static final String DEFAULT_SECRET = "r1ngs1d3";
    private static final String DEFAULT_URL    = "http://127.0.0.1:82/api/restserver.php";

    private M3Client client;
    private String   secret;
    private URL      serverUrl;

    @BeforeMethod
    public void beforeMethod()
    throws Exception
    {
        String serverUrlStr = System.getProperty( "ringside.test.serverUrl", DEFAULT_URL );
        this.secret    = System.getProperty( "ringside.test.secretKey", DEFAULT_SECRET );
        this.serverUrl = new URL( serverUrlStr );
        this.client    = new M3Client( this.serverUrl, this.secret, null );
    }

    public void testXmlClientBadSecretKey()
    throws Exception
    {
        client = new M3Client( this.serverUrl, ( secret + "BLAH" ), null );
        Document response = client.callMethodXml( M3Api.M3_INVENTORY_GET_VERSION_INFO.getMethodName(), null );
        assert response.getDocumentElement().getNodeName().equals( "error_response" );
    }

    public void testJaxbClientBadSecretKey()
    throws Exception
    {
        client = new M3Client( this.serverUrl, ( secret + "BLAH" ), null );
        try
        {
            client.callMethodJaxb( M3Api.M3_INVENTORY_GET_VERSION_INFO.getMethodName(), null );
        }
        catch ( ErrorResponseException e )
        {
            ErrorResponse error = e.getErrorResponse();
            assert error != null;
            assert error.getErrorCode() != null;
            assert error.getErrorMsg() != null;
            assert error.getRequestArgs() != null;
            assert error.getRequestArgs().getArg() != null;
            assert error.getRequestArgs().getArg().size() > 0;
            assert error.getRequestArgs().getArg().get( 0 ) != null;
            assert error.getRequestArgs().getArg().get( 0 ).getKey() != null;
            assert error.getRequestArgs().getArg().get( 0 ).getValue() != null;
        }
    }

    public void testStringClient()
    throws Exception
    {
        String response = client.callMethodString( M3Api.M3_INVENTORY_GET_VERSION_INFO.getMethodName(), null );
        assert response.contains( "inventory_getVersionInfo_response" );
    }

    public void testXmlClient()
    throws Exception
    {
        Document response = client.callMethodXml( M3Api.M3_INVENTORY_GET_VERSION_INFO.getMethodName(), null );
        assert response.getDocumentElement().getNodeName().equals( "inventory_getVersionInfo_response" );
    }

    public void testJaxbClient()
    throws Exception
    {
        Object objResponse = client.callMethodJaxb( "m3.inventory.getVersionInfo", null );
        assert objResponse instanceof InventoryGetVersionInfoResponse;
        InventoryGetVersionInfoResponse response = (InventoryGetVersionInfoResponse) objResponse;
        assert response.getRingside() != null;
        assert response.getRingside().getBuildDate() != null;
        assert response.getRingside().getBuildDate().length() > 0;
        assert response.getRingside().getBuildNumber() != null;
        assert response.getRingside().getBuildNumber().length() > 0;
        assert response.getRingside().getSvnRevision() != null;
        assert response.getRingside().getSvnRevision().length() > 0;
        assert response.getRingside().getVersion() != null;
        assert response.getRingside().getVersion().length() > 0;
        assert response.getRingside().getInstallDir() != null;
        assert response.getRingside().getInstallDir().length() > 0;

        assert response.getPhp() != null;
        assert response.getPhp().getVersion() != null;
        assert response.getPhp().getVersion().length() > 0;

        assert response.getExtensions() != null;
        assert response.getExtensions().getExtension() != null;
        assert response.getExtensions().getExtension().size() > 0;
    }

    public void testInventoryPing()
    throws Exception
    {
        assert client.inventoryPing();
    }

    public void testInventoryGetApis()
    throws Exception
    {
        InventoryGetApisResponse response = client.inventoryGetApis();
        assert response != null;
        assert response.getApi() != null;
        assert response.getApi().size() > 0;
        assert response.getApi().get( 0 ) != null;
        assert response.getApi().get( 0 ).getApiName() != null;
        assert response.getApi().get( 0 ).getPathname() != null;
    }

    public void testInventoryGetApi()
    throws Exception
    {
        // gotta get an api from the list to test
        InventoryGetApisResponse response1   = client.inventoryGetApis();
        ApiComplexType           testApi     = response1.getApi().get( 0 );
        String                   testApiName = testApi.getApiName();

        // ok, we can test now
        InventoryGetApiResponse response2 = client.inventoryGetApi( testApiName );
        assert response2 != null;
        assert response2.getApi() != null;
        assert response2.getApi().getApiName().equals( testApi.getApiName() );
        assert response2.getApi().getPathname().equals( testApi.getPathname() );
    }

    public void testInventoryGetApplications()
    throws Exception
    {
        InventoryGetApplicationsResponse response = client.inventoryGetApplications();
        assert response != null;
        assert response.getApplication() != null;
        assert response.getApplication().size() > 0;
        ApplicationComplexType application = response.getApplication().get( 0 );
        assert application != null;
        assert application.getAboutUrl() != null;
        assert application.getApplicationType() != null;
        assert application.getAttachmentAction() != null;
        assert application.getAttachmentCallbackUrl() != null;
        assert application.getAuthor() != null;
        assert application.getAuthorDescription() != null;
        assert application.getAuthorUrl() != null;
        assert application.getCallbackUrl() != null;
        assert application.getCanvasType() != null;
        assert application.getCanvasUrl() != null;
        assert application.getDefaultColumn() != null;
        assert application.getDefaultFbml() != null;
        assert application.getDeployed() != null;
        assert application.getDescription() != null;
        assert application.getDesktop() != null;
        assert application.getDeveloperMode() != null;
        assert application.getEditUrl() != null;
        assert application.getIconUrl() != null;
        assert application.getId() != null;
        assert application.getIpList() != null;
        assert application.getIsdefault() != null;
        assert application.getLogoUrl() != null;
        assert application.getMobile() != null;
        assert application.getName() != null;
        assert application.getPostaddUrl() != null;
        assert application.getPostremoveUrl() != null;
        assert application.getPrivacyUrl() != null;
        assert application.getSidenavUrl() != null;
        assert application.getSupportEmail() != null;
        assert application.getTosUrl() != null;
    }

    public void testInventoryGetApplication()
    throws Exception
    {
        // first gotta get an application to test with
        InventoryGetApplicationsResponse allResponse = client.inventoryGetApplications();
        ApplicationComplexType           app1        = allResponse.getApplication().get( 0 );

        // ok, we can test now...
        InventoryGetApplicationResponse response = client.inventoryGetApplication( Integer.parseInt( app1
                                                                                                     .getId() ) );
        assert response != null;
        assert response.getApplication() != null;
        ApplicationComplexType app2 = response.getApplication();
        assert app2 != null;
        try
        {
            assert app2.getAboutUrl().equals( app1.getAboutUrl() );
            assert app2.getApplicationType().equals( app1.getApplicationType() );
            assert app2.getAttachmentAction().equals( app1.getAttachmentAction() );
            assert app2.getAttachmentCallbackUrl().equals( app1.getAttachmentCallbackUrl() );
            assert app2.getAuthor().equals( app1.getAuthor() );
            assert app2.getAuthorDescription().equals( app1.getAuthorDescription() );
            assert app2.getAuthorUrl().equals( app1.getAuthorUrl() );
            assert app2.getCallbackUrl().equals( app1.getCallbackUrl() );
            assert app2.getCanvasType().equals( app1.getCanvasType() );
            assert app2.getCanvasUrl().equals( app1.getCanvasUrl() );
            assert app2.getDefaultColumn().equals( app1.getDefaultColumn() );
            assert app2.getDefaultFbml().equals( app1.getDefaultFbml() );
            assert app2.getDeployed().equals( app1.getDeployed() );
            assert app2.getDescription().equals( app1.getDescription() );
            assert app2.getDesktop().equals( app1.getDesktop() );
            assert app2.getDeveloperMode().equals( app1.getDeveloperMode() );
            assert app2.getEditUrl().equals( app1.getEditUrl() );
            assert app2.getIconUrl().equals( app1.getIconUrl() );
            assert app2.getId().equals( app1.getId() );
            assert app2.getIpList().equals( app1.getIpList() );
            assert app2.getIsdefault().equals( app1.getIsdefault() );
            assert app2.getLogoUrl().equals( app1.getLogoUrl() );
            assert app2.getMobile().equals( app1.getMobile() );
            assert app2.getName().equals( app1.getName() );
            assert app2.getPostaddUrl().equals( app1.getPostaddUrl() );
            assert app2.getPostremoveUrl().equals( app1.getPostremoveUrl() );
            assert app2.getPrivacyUrl().equals( app1.getPrivacyUrl() );
            assert app2.getSidenavUrl().equals( app1.getSidenavUrl() );
            assert app2.getSupportEmail().equals( app1.getSupportEmail() );
            assert app2.getTosUrl().equals( app1.getTosUrl() );
        }
        catch ( AssertionError t )
        {
            String app1xml = client.stringifyJaxbObject( allResponse );
            String app2xml = client.stringifyJaxbObject( response );
            String msg     = t.getMessage();

            throw new Exception( msg + "\nApplication 1:\n" + app1xml + "\nApplication 2:\n" + app2xml, t );
        }
    }

    public void testInventoryGetNetworks()
    throws Exception
    {
        InventoryGetNetworksResponse response = client.inventoryGetNetworks();
        assert response != null;
        assert response.getNetwork() != null;
        assert response.getNetwork().size() > 0;
        assert response.getNetwork().get( 0 ) != null;
        assert response.getNetwork().get( 0 ).getId() != null;
        assert response.getNetwork().get( 0 ).getName() != null;
    }

    public void testInventoryGetNetwork()
    throws Exception
    {
        // first have to get a network to test with
        InventoryGetNetworksResponse allNetworks = client.inventoryGetNetworks();
        NetworkComplexType           network     = allNetworks.getNetwork().get( 0 );

        // ok, can test now
        InventoryGetNetworkResponse response = client.inventoryGetNetwork( Integer.parseInt( network.getId() ) );
        assert response != null;
        assert response.getNetwork() != null;
        assert response.getNetwork().getId().equals( network.getId() );
        assert response.getNetwork().getName().equals( network.getName() );
    }

    public void testInventoryGetTags()
    throws Exception
    {
        InventoryGetTagsResponse response = client.inventoryGetTags();
        assert response != null;
        assert response.getTag() != null;
        assert response.getTag().size() > 0;
        assert response.getTag().get( 0 ) != null;
        assert response.getTag().get( 0 ).getTagNamespace() != null;
        assert response.getTag().get( 0 ).getTagName() != null;
        assert response.getTag().get( 0 ).getHandlerClass() != null;
        assert response.getTag().get( 0 ).getSourceFile() != null;
        assert response.getTag().get( 0 ).getSourceFileLastModified() != null;
    }

    public void testInventoryGetTag()
    throws Exception
    {
        // gotta get an api from the list to test
        InventoryGetTagsResponse response1        = client.inventoryGetTags();
        TagComplexType           testTag          = response1.getTag().get( 0 );
        String                   testTagNamespace = testTag.getTagNamespace();
        String                   testTagName      = testTag.getTagName();

        // ok, we can test now
        InventoryGetTagResponse response2 = client.inventoryGetTag( testTagNamespace, testTagName );
        assert response2 != null;
        assert response2.getTag() != null;
        assert response2.getTag().getTagNamespace().equals( testTag.getTagNamespace() );
        assert response2.getTag().getTagName().equals( testTag.getTagName() );
        assert response2.getTag().getHandlerClass().equals( testTag.getHandlerClass() );
        assert response2.getTag().getSourceFile().equals( testTag.getSourceFile() );
        assert response2.getTag().getSourceFileLastModified().equals( testTag.getSourceFileLastModified() );
    }

    public void testInventoryGetVersionInfo()
    throws Exception
    {
        InventoryGetVersionInfoResponse response = client.inventoryGetVersionInfo();
        assert response != null;

        assert response.getRingside() != null;
        assert response.getRingside().getBuildDate() != null;
        assert response.getRingside().getBuildNumber() != null;
        assert response.getRingside().getSvnRevision() != null;
        assert response.getRingside().getVersion() != null;

        assert response.getPhp() != null;
        assert response.getPhp().getVersion() != null;
        assert response.getPhp().getDoctrineVersion() != null;
        assert response.getPhp().getPhpIniFile() != null;

        assert response.getExtensions() != null;
        assert response.getExtensions().getExtension() != null;
        assert response.getExtensions().getExtension().size() > 0;
    }

    public void testConfigGetLocalSettings()
    throws Exception
    {
        ConfigGetLocalSettingsResponse response = client.configGetLocalSettings();
        assert response != null;
        assert response.getLocalSetting() != null;
        assert response.getLocalSetting().size() > 0;
        assert response.getLocalSetting().get( 0 ).getName() != null;
        assert response.getLocalSetting().get( 0 ).getValue() != null;
    }

    public void testConfigSetLocalSettings()
    throws Exception
    {
        String settingNameToChange = "m3MaxSizeAllowedOperationGetFileContent";

        // first get the config
        ConfigGetLocalSettingsResponse response = client.configGetLocalSettings();
        assert response != null;
        assert response.getLocalSetting() != null;

        Integer             originalValue = null;
        Map<String, String> newSettings   = new HashMap<String, String>();

        for ( LocalSetting localSetting : response.getLocalSetting() )
        {
            if ( localSetting.getName().equals( settingNameToChange ) )
            {
                originalValue = Integer.parseInt( localSetting.getValue() );
                localSetting.setValue( Integer.toString( originalValue.intValue() + 1 ) );
            }

            newSettings.put( localSetting.getName(), localSetting.getValue() );
        }

        assert originalValue != null : "missing the setting we want to change: " + settingNameToChange;

        // now set the new value
        assert client.configSetLocalSettings( newSettings );

        // get the config again and make sure it changed
        response = client.configGetLocalSettings();
        assert response != null;
        assert response.getLocalSetting() != null;

        Integer newValue = null;
        newSettings.clear();

        for ( LocalSetting localSetting : response.getLocalSetting() )
        {
            if ( localSetting.getName().equals( settingNameToChange ) )
            {
                newValue = Integer.parseInt( localSetting.getValue() );
                localSetting.setValue( originalValue.toString() );
            }

            newSettings.put( localSetting.getName(), localSetting.getValue() );
        }

        assert newValue != null : "missing the new setting we just changed! " + settingNameToChange;

        // reset the value back to its original value
        assert client.configSetLocalSettings( newSettings );
    }

    public void testConfigGetPhpIniSettings()
    throws Exception
    {
        ConfigGetPhpIniSettingsResponse response = client.configGetPhpIniSettings();
        assert response != null;
        assert response.getPhpIniSetting() != null;
        assert response.getPhpIniSetting().size() > 0;
        assert response.getPhpIniSetting().get( 0 ).getName() != null;
        assert response.getPhpIniSetting().get( 0 ).getValue() != null;
    }

    public void testMetricsGetApiDurations()
    throws Exception
    {
        MetricsGetApiDurationsResponse response = client.metricsGetApiDurations();
        assert response != null;
        assert response.getApiDuration() != null;
        assert response.getApiDuration().size() > 0;
        ApiDuration apiDuration = response.getApiDuration().get( 0 );
        assert apiDuration.getKey() != null;
        assert apiDuration.getCount() >= 0;
        assert apiDuration.getMin() >= 0.0;
        assert apiDuration.getMax() >= 0.0;
        assert apiDuration.getAvg() >= 0.0;
    }

    public void testMetricsPurgeApiDurations()
    throws Exception
    {
        boolean response = client.metricsPurgeApiDurations();
        assert response : "We should always be able to purge, why did this fail?";
    }

    public void testOperationEvaluatePhpCode()
    throws Exception
    {
        String response = client.operationEvaluatePhpCode( "return 'foobar';" );
        assert response.equals( "foobar" );
    }

    public void testOperationGetFileContent()
    throws Exception
    {
        String response = client.operationGetFileContent( "LocalSettings.php", null );
        assert response.contains( "m3SecretKey" );
        response = client.operationGetFileContent( "LocalSettings.php", Boolean.TRUE );
        assert response.contains( "m3SecretKey" );
        response = client.operationGetFileContent( "LocalSettings.php", Boolean.FALSE );
        assert response.contains( "m3SecretKey" );

        try
        {
            client.operationGetFileContent( "does/not/exist", null );
            assert false : "Should have thrown an exception, the file doesn't exist";
        }
        catch ( ErrorResponseException expected )
        {
        }
    }
}