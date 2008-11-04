<?php
if ( !isset( $REQUEST) || !$REQUEST ) {
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$REQUEST = &$_POST;
	} else {
		$REQUEST = &$_GET;
	}
}

$webRoot = '';
if ( class_exists( 'RingsideWebConfig' )) { 
   $webRoot = RingsideWebConfig::$webRoot;
}
$webRoot .= '/';

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
<div class="ringside">
	<div class="widebar">
<div class="canvas">
<?php
   
   if ( isset($error) && !empty( $error )) { 
      $REQUEST['social.error'] = $error;
      echo $social->render( 'canvas', null, 'error', '', $REQUEST );
   } else if ( isset( $iframe ) && !empty( $iframe ) ) { 
     echo "<iframe src='$iframe' class='' width='700' height='1000' frameborder='0' />";
   } else if( isset($canvas_content) ){
      echo $canvas_content;
   } else if ( isset ($canvas ) ) {
      echo $social->render( 'canvas', null, $canvas, '', $REQUEST );
   }
?>
		</div><!-- end div.canvas -->
	</div><!-- end div.widebar -->
	<br class="fixclear" />
		<div class="footer">Copyright &copy; 2008, Ringside Networks, Inc.</div><!-- end div.footer -->
</div><!-- end div.ringside -->
</body>
</html>
