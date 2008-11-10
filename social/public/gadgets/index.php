<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */

/*
 * Written by Chris Chabot <chabotc@xs4all.nl> - http://www.chabotc.com
 * 
 * "It is not the strongest of the species that survives, nor the most intelligent that survives. 
 * It is the one that is the most adaptable to change" - Darwin
 * 
 * So in this php version of shindig we make like java and act like a servlet setup, and using
 * it's structures as our reference.
 * 
 * The .htaccess file redirects all requests that are neither an existing file or directory
 * to this index.php, and the $servletMap checks which class is associated with it, if a mapping
 * doesn't exist it display's a 404 error.
 * 
 * See config.php for the global settings and backend class selections.
 * 
 */
include_once('LocalSettings.php');
include_once (dirname(__FILE__).'/config.php');
require_once('ringside/social/session/RingsideSocialSession.php');

function shidigAutoloader($className)
{
	$src_root=Config::get('src_root');
	$aldebug=true;
	$classpath=array(
		"$src_root/common/$fileName/",
		"$src_root/../../../../includes/ringside/opensocial/",
		"$src_root/../ringside/opensocial/",
		"$src_root/gadgets/",
		"$src_root/gadgets/samplecontainer/",
		"$src_root/gadgets/http/",
		"$src_root/socialdata/",
		"$src_root/socialdata/samplecontainer/",
		"$src_root/socialdata/opensocial/",
		"$src_root/socialdata/opensocial/model/",
		"$src_root/socialdata/http/"
	);
	
	$fileName = $className.'.php';
	foreach($classpath as $path){
		$testFile = $path.$fileName;
		if(file_exists("$testFile")){
			require_once "$testFile";
			if($aldebug){
				error_log("shindig autoloader: Loaded  $className from $path");
			}
			return;
		} else {
			if($aldebug){
				error_log("shindig autoloader: could not find $className in $path");
			}
		}
	}
	
	error_log("shindig autoloader: Failed to load class $className.");
	
}

spl_autoload_register( shidigAutoloader );

$servletMap = array(
	Config::get('web_prefix') . '/gadgets/files'    => 'FilesServlet',
	Config::get('web_prefix') . '/gadgets/js'       => 'JsServlet',
	Config::get('web_prefix') . '/gadgets/proxy'    => 'ProxyServlet',
	Config::get('web_prefix') . '/gadgets/ifr'      => 'GadgetRenderingServlet',
	Config::get('web_prefix') . '/gadgets/metadata' => 'JsonRpcServlet',
	Config::get('web_prefix') . '/gadgets/social/data'      => 'GadgetDataServlet'
);

$servlet = false;
$uri = $_SERVER["REQUEST_URI"];
//error_log("OS request: ".$_SERVER["REQUEST_URI"]);
foreach ($servletMap as $url => $class) {
		//error_log(substr($uri, 0, strlen($url))."=?".$url);
	if (substr($uri, 0, strlen($url)) == $url) {
		$servlet = $class;
		break;
	}
}
if ($servlet) {		
	$class = new $class();
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$class->doPost();
	} else {
		$class->doGet();
	}
} else {
	// Unhandled event, display simple 404 error
	error_log("$uri does not map to any servlet. Request Aborted.");
	header("HTTP/1.0 404 Not Found");
	echo "<html><body><h1>404 Not Found</h1></body></html>";
	echo "<h2>Why not try one of these samples?</h2>\n";
	echo "<ul>";
	//echo "<li><a href='ifr?url=http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}$webRoot/gadgets/files/samplecontainer/examples/Test.xml'>The Hello World Social Example</a>.</li>\n";
	//echo "<li><a href='files/samplecontainer/samplecontainer.html'>Social Container Example</a>.</li>\n";
	echo "<li><a href='files/container/sample1.html'>Gadget example 1</a>.</li>\n";
	echo "<li><a href='files/container/sample2.html'>Gadget example 2</a>.</li>\n";
	echo "<li><a href='files/container/sample3.html'>Gadget example 3</a>.</li>\n";
	echo "<li><a href='files/container/sample4.html'>Gadget example 4</a>.</li>\n";
	echo "<li><a href='files/container/sample5.html'>Gadget example 5</a>.</li>\n";
	echo "<li><a href='files/container/sample6.html'>Gadget example 6</a>.</li>\n";
	echo "<li><a href='files/container/sample7.html'>Gadget example 7</a>.</li>\n";
	echo "</ul>";
	echo "<p>\n";
	echo "Or load a gadget using this container server at http://yourhost/gadgets/ifr?url=gadget-url<p>
   			Example: http://<yourhost>/gadgets/ifr?url=http://www.labpixies.com/campaigns/todo/todo.xml";
}
?>