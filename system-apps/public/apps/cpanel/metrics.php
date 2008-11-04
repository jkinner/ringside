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

$GLOBALS['metricsSelected'] = 'true';
require_once("header.inc");
include("ringside/apps/cpanel/templates/tabitems.tpl");

function echo_agg_user_stats()
{
    $_metrics = $GLOBALS['m3client']->metricsGetAggregatedUserStatistics();
    echo <<<EOF
    <table border="1">
    <tr>
        <th>Statistic</td>
        <th>Value</th>
    </tr>
    <tr>
        <td>Total Users</td>
        <td>{$_metrics['total_users']}</td>
    </tr>
    <tr>
        <td>Recently Added Users</td>
        <td>{$_metrics['recently_added_users']}</td>
    </tr>
    <tr>
        <td>Recently Added Friends</td>
        <td>{$_metrics['recently_added_friends']}</td>
    </tr>
    <tr>
        <td>Friends Per User</td>
        <td>{$_metrics['friends_per_user']}</td>
    </tr>
    <tr>
        <td>Recently Registered Apps Per User</td>
        <td>{$_metrics['recently_registered_apps_per_user']}</td>
    </tr>
    <tr>
        <td>Mapped Identities Per User</td>
        <td>{$_metrics['mapped_identities_per_user']}</td>
    </tr>
    <tr>
        <td>Photos Per User</td>
        <td>{$_metrics['photos_per_user']}</td>
    </tr>
    </table>
EOF;
    
}

function echo_api_durations()
{
    if (get_request_param('purgedata', 'false') === 'true')
    {
        $GLOBALS['m3client']->metricsPurgeApiDurations();        
    }

    echo <<<EOF
    <form action="metrics.php?show=apidurations" method="get">
       <input type="submit" value="Clear Data" />
       <input type="hidden" name="show" value="apidurations" />
       <input type="hidden" name="purgedata" value="true" />
    </form>
EOF;
    
    // $_metrics[API] = array("count", "min", "max", "avg")
    $_metrics = $GLOBALS['m3client']->metricsGetApiDurations();

    echo <<<EOF
    <table border="1">
    <tr>
        <th>API</td>
        <th>Invocation Count</th>
        <th>Min Time(ms)</th>
        <th>Max Time(ms)</th>
        <th>Average Time(ms)</th>
    </tr>
EOF;

    foreach ($_metrics as $_apiName => $_stats)
    {
        $_count = $_stats['count'];
        $_min = sprintf("%5.2f", 1000*$_stats['min']);
        $_max = sprintf("%5.2f", 1000*$_stats['max']);
        $_avg = sprintf("%5.2f", 1000*$_stats['avg']);

    echo <<<EOF2
    <tr>
        <td>{$_apiName}</td>
        <td>{$_count}</td>
        <td>{$_min}</td>
        <td>{$_max}</td>
        <td>{$_avg}</td>
    </tr>
EOF2;
    }
    echo "</table>\n";
    
    echo_api_visualization($_metrics);
}

function echo_api_visualization($metrics)
{
    if (!isset($metrics) || empty($metrics))
    {
        return;
    }

    $_totalApis = count($metrics);

    echo <<<EOF
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["piechart","barchart","columnchart"]});
            google.setOnLoadCallback(drawVisualization); // Set callback to run when API is loaded
            function drawVisualization()
            {
                var dataCallCount = new google.visualization.DataTable();
                dataCallCount.addColumn('string', 'API');
                dataCallCount.addColumn('number', 'Invocation Count');
                dataCallCount.addRows($_totalApis);
                
                var dataAverage = new google.visualization.DataTable();
                dataAverage.addColumn('string', 'API');
                dataAverage.addColumn('number', 'Average Response Time(ms)');
                dataAverage.addRows($_totalApis);
                
                var dataResponseTime = new google.visualization.DataTable();
                dataResponseTime.addColumn('string', 'API');
                dataResponseTime.addColumn('number', 'Min Time(ms)');
                dataResponseTime.addColumn('number', 'Avg Time(ms)');
                dataResponseTime.addColumn('number', 'Max Time(ms)');
                dataResponseTime.addRows($_totalApis);

EOF;

    $_apiNumber = 0;
    foreach ($metrics as $_apiName => $_stats)
    {
        $_count = $_stats['count'];
        $_min = sprintf("%5.2f", 1000*$_stats['min']);
        $_max = sprintf("%5.2f", 1000*$_stats['max']);
        $_avg = sprintf("%5.2f", 1000*$_stats['avg']);
    
        echo "dataCallCount.setValue($_apiNumber, 0, '$_apiName');\n";
        echo "dataCallCount.setValue($_apiNumber, 1, $_count);\n";

        echo "dataAverage.setValue($_apiNumber, 0, '$_apiName');\n";
        echo "dataAverage.setValue($_apiNumber, 1, $_avg);\n";

        echo "dataResponseTime.setValue($_apiNumber, 0, '$_apiName');\n";
        echo "dataResponseTime.setValue($_apiNumber, 1, $_min);\n";
        echo "dataResponseTime.setValue($_apiNumber, 2, $_avg);\n";
        echo "dataResponseTime.setValue($_apiNumber, 3, $_max);\n";
        $_apiNumber++;
    }

    echo <<<EOF
                var chart = new google.visualization.PieChart(document.getElementById('count_div'));
                chart.draw(dataCallCount, {width: 500, height: 400, is3D: false, title: 'API Call Counts'});

                var chart = new google.visualization.BarChart(document.getElementById('avg_div'));
                chart.draw(dataAverage, {width: 500, height: 400, is3D: false, title: 'API Average Response Times'});

                var chart = new google.visualization.BarChart(document.getElementById('rt_div'));
                chart.draw(dataResponseTime, {width: 500, height: 400, is3D: false, title: 'API Response Times'});
            }
       </script>
       <div id="count_div"></div>
       <hr/>
       <div id="avg_div"></div>
       <hr/>
       <div id="rt_div"></div>
EOF;
}

function echo_app_durations()
{
    echo "Application Duration metrics are not yet implemented.";
}

?>
<fb:tabs>
    <fb:tab-item href='metrics.php?show=apidurations'
                 title='API Response Times'
                 selected='<?php echo (get_request_param('show', 'apidurations') === 'apidurations') ? 'true' : ''; ?>' />
    <fb:tab-item href='metrics.php?show=appdurations'
                 title='Application Response Times'
                 selected='<?php echo (get_request_param('show') === 'appdurations') ? 'true' : ''; ?>' />
    <fb:tab-item href='metrics.php?show=aggregateuserstats'
                 title='Aggregate User Statistics'
                 selected='<?php echo (get_request_param('show') === 'aggregateuserstats') ? 'true' : ''; ?>' />
</fb:tabs>
<?php

if (get_request_param('show', 'apidurations') === 'apidurations')
{
    echo_api_durations();
}
else if (get_request_param('show') === 'appdurations')
{
    echo_app_durations();
}
else if (get_request_param('show') === 'aggregateuserstats')
{
    echo_agg_user_stats();
}
else
{
    echo_api_durations();    
}
?>