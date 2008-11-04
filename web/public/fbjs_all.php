<?php

require_once('include.php');

function getLoginNextURL()
{
	$pstr = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $prot = substr($pstr, 0, strpos($pstr, '/'));
    $port = $_SERVER["SERVER_PORT"]; 
    return $prot . '://' . $_SERVER['SERVER_NAME'] . ':' . $port . $_SERVER['REQUEST_URI'];
}

//deal with login stuff, pass variables to JavaScript
$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );
$inSession = $social->inSession();

$jSession = $inSession ? 'true' : 'false';
$jLoginUrl = RingsideWebConfig::$webRoot . '/login.php?next=' . urlencode(getLoginNextURL());

echo "<script type=\"text/javascript\">\n";
echo "   var ringside_fbjs_in_session = $jSession;\n";
echo "   var ringside_fbjs_login_url = \"$jLoginUrl\";\n";
echo "</script>\n";

//put links in for stylesheets
$styleSheets = array('flora/flora.all.css');
foreach ($styleSheets as $s) {
	$sPath = RingsideWebConfig::$webRoot . '/js/jquery/css/' . $s;
	echo "<link rel=\"stylesheet\" href=\"$sPath\" type=\"text/css\" media=\"screen\" title=\"Flora (Default)\">\n";
}

//include relevant .js files
$jqueryIncFiles = array('jquery-1.2.6.js', 'ui.core.js', 'ui.dialog.js', 'ui.resizable.js', 'ui.draggable.js');
foreach ($jqueryIncFiles as $incFile) {
	$incPath = RingsideWebConfig::$webRoot . '/js/jquery/' . $incFile;
	echo "<script type='text/javascript' src='$incPath'></script>\n";
}


$fbjsIncFiles = array('fbjs_animation.js', 'fbjs_dialog.js', 'fbjs_events.js', 'fbjs_ajax.js', 'fbjs_dom.js');
foreach ($fbjsIncFiles as $incFile) {
	$incPath = RingsideWebConfig::$webRoot . '/js/fbjs/' . $incFile;
	echo "<script type='text/javascript' src='$incPath'></script>\n";
}

?>
