<?php

$_metrics = $this->m3->metricsGetApiDurations();


echo_api_visualization($_metrics);
  
  
  function echo_api_visualization($metrics)
  {
  if (!isset($metrics) || empty($metrics))
  {
      return;
  }

  $_totalApis = count($metrics);

echo <<<EOF

   <div id="count_div"></div>
   <hr/>
   <div id="avg_div"></div>
   <hr/>
   <div id="rt_div"></div>

<script type="text/javascript">
        //google.setOnLoadCallback(drawVisualization); // Set callback to run when API is loaded
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
        
        drawVisualization();
   </script>

EOF;
  }
  
  ?>