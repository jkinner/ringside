<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Ringside Social Application Server</title>
<link rel="stylesheet" href="<?php echo RingsideWebConfig::$webRoot; ?>/css.php/admin.css" type="text/css" />
<?php include 'fbjs_all.php'; ?>
<script src="<?php echo RingsideWebConfig::$webRoot ?>/js/fbml.js?v=2"></script>
<?php if ( isset($extra_head_scripts) ) { echo $extra_head_scripts; } ?>
</head>
<body>

<div id="wrapper">

	<div id="header">
		<img src="<?php echo RingsideWebConfig::$webRoot; ?>/images/logo-ringside.png" id="logo" alt="Ringside Social Application Server" />
		<div id="profile">
			<img src="<?php echo RingsideWebConfig::$webRoot; ?>/images/icon-user-male.png" alt="" id="profilePic" width="55" height="55" />
			<div id="userInfo">
				<div id="loggedInAs">Logged in as:</div>
				<div id="profileName">Profile Name</div>
				<a href="#" id="editProfile">Edit Profile</a>
			</div><!-- end div#userInfo -->
			<a href="#" id="logout">Logout</a>
		</div><!-- end div#profile -->
		<ul id="nav-top">
			<li class="<?php if(!isset($_GET['apps'])&&!isset($_GET['communities'])) print "selected"; ?>"><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php"><span>Home</span></a></li>
			<li class="<?php if(isset($_GET['apps'])) print "selected"; ?>"><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php?apps"><span>Applications</span></a></li>
			<li class="<?php if(isset($_GET['communities'])) print "selected"; ?>"><a href="<?php echo RingsideWebConfig::$webRoot ?>/admin.php?communities"><span>Communities</span></a></li>
		</ul>
		
	</div><!-- end div#header -->
	
	<div id="nav-top-sub">&nbsp;</div>
	
	<div id="welcome-wrapper">
		<div id="welcome-content">
			<div id="welcome-content-viewer">
				Lots of stuff will go here.  Video, content and images.
			</div><!-- end div#welcome-content-viewer -->
			<ul id="welcome-nav">
				<li>Welcome!</li>
				<li>Get Social</li>
				<li>Get Connected</li>
				<li>Get Creative</li>
			</ul><!-- end ul#welcome-nav -->
		</div><!-- end div#welcome-content -->
	</div><!-- end div#welcome-wrapper -->

	<div id="content-wrapper">
		<div id="content">
	
			<ul id="welcome-tab">
				<li><a href="#"><span class="arrow-down">Welcome!</span></a></li>
			</ul><!-- end ul#welcome-tab -->

			<div id="stage-wrapper">
				<div id="stage">
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
        </div>
			</div><!-- end div#content -->
			
		</div><!-- end div#content -->
	</div><!-- end div#content-wrapper -->

	<div id="footer">
		&copy; 2008 Ringside Networks, Inc.
	</div><!-- end div#footer -->

</div><!-- end div#wrapper -->

</body>
</html>
