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
require_once 'LocalSettings.php';
include_once 'ringside/m3/client/RestClient.php';

$secret = @$_REQUEST['s'];
if (!($secret === $GLOBALS['m3SecretKey']))
{
    echo "Test OK.";
    return;
}

function ahref($apiName)
{
    echo '<tr><td><a href=api_test.php?api=' . $apiName . '&s=' . $GLOBALS['m3SecretKey'] . '>' . $apiName . "</a></td></tr>\n"; 
}

$client = new M3_Client_RestClient($GLOBALS['serverUrl'], $GLOBALS['m3SecretKey'], array('debug' => true));
$api = @$_REQUEST['api'];
$userApi = @$_REQUEST['userApi'];
$userParams = @$_REQUEST['userParams'];

// TO ADD MORE APIS TO TEST, ADD ANOTHER ELSE-IF CLAUSE THEN ADD A ROW TO THE <TABLE>

if (!isset($api) && !isset($userApi))
{
    echo '<p align="center">Specify the API you want to test</p>';    
}
else if (isset($userApi))
{
    if (empty($userApi))
    {
        echo '<p align="center">You forgot to specify the API method name</p>';
    }
    else
    {
        if (isset($userParams) && !empty($userParams))
        {
            parse_str($userParams, $params);
            $results = $client->callRestMethod($userApi, $params);
        }
        else
        {
            $results = $client->callRestMethod($userApi);        
        }
    }
}
else if ( $api == 'm3.config.getLocalSettings')
{
   $results = $client->configGetLocalSettings();
}
else if ( $api == 'm3.config.setLocalSettings')
{
   $results = $client->configGetLocalSettingsFlattened();
   $results['m3MaxSizeAllowedOperationGetFileContent']++;
   $results = $client->configSetLocalSettings($results);
}
else if ( $api == 'm3.config.getPhpIniSettings')
{
   $results = $client->configGetPhpIniSettings();
}
else if ( $api == 'm3.inventory.ping')
{
   $results = $client->inventoryPing();
}
else if ( $api == 'm3.inventory.getApis')
{
   $results = $client->inventoryGetApis();
}
else if ( $api == 'm3.inventory.getApi')
{
   $results = $client->inventoryGetApi('m3.inventory.getApis');
}
else if ( $api == 'm3.inventory.getApplications')
{
   $results = $client->inventoryGetApplications();
}
else if ( $api == 'm3.inventory.getApplication')
{
   $results = $client->inventoryGetApplication('3100');
}
else if ( $api == 'm3.inventory.getNetworks')
{
   $results = $client->inventoryGetNetworks();
}
else if ( $api == 'm3.inventory.getNetwork')
{
   $results = $client->inventoryGetNetwork('111');
}
else if ( $api == 'm3.inventory.getTags')
{
   $results = $client->inventoryGetTags();
}
else if ( $api == 'm3.inventory.getTag')
{
   $results = $client->inventoryGetTag('fb', 'user');
}
else if ( $api == 'm3.inventory.getVersionInfo')
{
   $results = $client->inventoryGetVersionInfo();
}
else if ( $api == 'm3.metrics.getApiDurations')
{
   $results = $client->metricsGetApiDurations();
}
else if ( $api == 'm3.metrics.purgeApiDurations')
{
   $results = $client->metricsPurgeApiDurations();
}
else if ( $api == 'm3.operation.evaluatePhpCode')
{
   $results = $client->operationEvaluatePhpCode('return "eval done.";');
}
else if ( $api == 'm3.operation.getFileContent')
{
   $results = $client->operationGetFileContent('LocalSettings.php');
}
else
{
    echo "Cannot test that API: " . $api;
}

echo '<table style="margin-left: auto; margin-right: auto" border="1">';
echo '<tr><th>Click an API to test</th></tr>';
ahref('m3.config.getLocalSettings');
ahref('m3.config.setLocalSettings');
ahref('m3.config.getPhpIniSettings');
ahref('m3.inventory.ping');
ahref('m3.inventory.getApis');
ahref('m3.inventory.getApi');
ahref('m3.inventory.getApplications');
ahref('m3.inventory.getApplication');
ahref('m3.inventory.getNetworks');
ahref('m3.inventory.getNetwork');
ahref('m3.inventory.getTags');
ahref('m3.inventory.getTag');
ahref('m3.inventory.getVersionInfo');
ahref('m3.metrics.getApiDurations');
ahref('m3.metrics.purgeApiDurations');
ahref('m3.operation.evaluatePhpCode');
ahref('m3.operation.getFileContent');

echo <<<EOF
<tr><th>Enter API To Test</th></tr>
<tr><td>
    <form action="api_test.php" method="GET">
        <input type="hidden" name='s' value="{$GLOBALS['m3SecretKey']}">
        Method: <input type="text" size="30" name="userApi" value="$userApi"><br/>
        Params: <input type="text" size="30" name="userParams" value="$userParams">
        <input type="submit" value="Invoke"><br/>
    </form>
</td></tr>
EOF;

echo '</table>';

if (isset($results))
{
   echo "<hr/><h1>RESULTS DUMP</h1>\n";
   echo "<pre>\n";
   echo var_export($results, true);
   echo "</pre>\n";
}
?>