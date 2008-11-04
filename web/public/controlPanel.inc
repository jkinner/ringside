<?php

$webRoot = '';
if ( class_exists( 'RingsideWebConfig' )) { 
   $webRoot = RingsideWebConfig::$webRoot;
}
$webRoot .= '/';

if ( isset( $top ) ) {
   if ( $top == "empty")  {
      unset( $top );
   }
} else {
   $top = 'menu';
}

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'fbjs_all.php'; ?>
<?php if ( isset($extra_head_scripts) ) { echo $extra_head_scripts; } ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="<?php echo RingsideWebConfig::$webRoot ?>/css.php/ringside.css" type="text/css" />
<title>Ringside Social Application Server :: <?php  echo isset($canvas)?$canvas:'' ?></title>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-3945764-3");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</head>
<body>
<h1 style="font-size:50px;">Control Panel - controlPanel.inc</h1>
<div class="ringside">
	<div class="sidebar">
		<div id="logo"><a class="ringside-home" href="<?php echo RingsideWebConfig::$webRoot ?>/"></a></div>
<?php
if ( isset( $left )) {
   foreach ( $left as $application ) {
      echo $social->render( 'sidebar', null, $application, '' );
   }
}
?>
	</div><!-- end div.sidebar -->
	<div class="widebar">
<?php
if ( isset( $top) ) {
   echo $social->render( 'menu', null, $top, '' );
}
?>
<div class="canvas">
<?php
   
   if ( isset($error) && !empty( $error )) { 
      if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
         $_POST['social.error'] = $error;
      } else {
         $_GET['social.error'] = $error;
      }
      echo $social->render( 'canvas', null, 'error', '' );
   } else if ( isset( $iframe ) && !empty( $iframe ) ) { 
     echo "<iframe src='$iframe' class='' width='700' height='1000' frameborder='0' />";
   } else if( isset($canvas_content) ){
      echo $canvas_content;
   } else if ( isset ($canvas ) ) {
      echo $social->render( 'canvas', null, $canvas, '' );
   }
?>
		</div><!-- end div.canvas -->
	</div><!-- end div.widebar -->
	<br class="fixclear" />
		<div class="footer">Copyright &copy; 2008, Ringside Networks, Inc.</div><!-- end div.footer -->
</div><!-- end div.ringside -->
<?php if ( isset($extra_end_scripts) ) { echo $extra_end_scripts; } ?>
</body>
</html>
