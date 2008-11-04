<?php
 /*
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
  */

$demousers = array(
    '100000'	=>    'Joe',
    '100001'	=>    'Jack',
    '100002'	=>    'Jeff',
    '100003'	=>    'Joel',
    '100004'	=>    'Jane',
//    '100005'	=>    'Jill',
//    '100006'	=>    'John',
//    '100007'	=>    'Jon',
//    '100008'	=>    'Jared',
    '100009'	=>    'June'
);

/*
 * Start a PHP session for us to put user goodies in, including their
 * Ringside session key for rendering social application widgets.
 */
$session_id = session_start();

/*
 * Initialize our Site Connect session. Defines a variable, $siteconnect, with
 * the Site Connect client.
 */
include_once('siteconnect.php');

/*
 * If the operator selects a user to login as, siteconnect.php takes care of creating
 * a session for the user, and this code takes care of the user experience to make
 * reloads easier.
 */
if ( isset($_REQUEST['uid']) ) {
    // Redirect just to make the UX nicer on reload
    $baseuri = $_SERVER['REQUEST_URI'];
    $idx = strpos($baseuri, '?');
    if ( false !== $idx ) {
        $baseuri = substr($baseuri, 0, $idx);
    }
    header('Location: http://'.$_SERVER['HTTP_HOST'].$baseuri, null, 302);
    exit;
}

?>
<html>
<head>
<title>Ringside Site Connect :: Home</title>
  <script src="http://social.example.com:8888/social/rswidget.js" type="text/javascript"></script>
  <script src="logout.js" type="text/javascript"></script>
  <script src="iframeresize.js" type="text/javascript"></script>
</head>
<body style="background-color: #DDDDDD">
<h1>Sample Page</h1>
Log in as: 
<form action="" method="POST">
<input type="hidden" name="uid" value="">
<?php foreach ( $demousers as $uid => $name) { ?>
<a style="color: #222222" href="javascript:" onclick="parentNode.uid.value = <?php echo $uid ?>; parentNode.submit(); return false;"><?php echo $name ?></a>
<?php } ?>
</form>
<a style="color: #222222" id="login_button" href="#" onclick="eraseCookie('PHPSESSID'); return false;">Logout</a>
<div style="width: 500" class="rs-fbml"><h1>Loading...</h1></div>
<script type="text/javascript">
rswidget.renderAllWidgets({
		apiKey:'796aa6bc8d81d958847eb38e85761882',
		app:'footprints',
		mode: 'app',
		loginServer:'http://social.example.com:8888/web',
		resizeUrl:'http://www.example.com:8888/demo/siteconnect/iframe_resizer.html',
		social_session_key:'<?php echo $_SESSION['ringside_session']['session_id'] ?>',
		renderEndpoint:'http://social.example.com:8888/social/render.php',
		trustEndpoint:'http://social.example.com:8888/social/trust.php'
		});
</script>
</body>
</html>