<?php

// this defines some of your basic setup
include_once 'config.php';

// the facebook client library
include_once '../client/facebook.php';

// some basic library functions
include_once 'lib.php';

$facebook = new Facebook($api_key, $secret);
$facebook->require_frame();
$user = $facebook->require_login();

if (isset($_POST['to'])) {
  $prints_id = (int)$_POST['to'];
  $prints = do_step($user, $prints_id);
} else {
  if (isset($_GET['to'])) {
    $prints_id = (int)$_GET['to'];
  } else {
    $prints_id = $user;
  }
  $prints = get_prints($prints_id);
}


?>

<div style="padding: 10px;">
  <h2>Hey <fb:name firstnameonly="true" uid="<?php echo $user?>" useyou="false"/>!</h2><br/>
  <a href="<?php echo $facebook->get_add_url() ?>">Put Footprints in your profile</a>, if you haven't already!
    <form method="post" action="">
    <input type="hidden" name="canvas" value="footprints" />
<?php
      if ($prints_id != $user) {
        echo 'Do you want to step on <fb:name uid="' . $prints_id . '"/>?';
        echo '<input type="hidden" name="to" value="' . $prints_id . '"/>';
      } else {
        echo '<br/>Step on a friend:';
        echo '<fb:friend-selector name="to"/>';
      }
?>
      <input value="step" type="submit"/>
    </form>
  <hr/>
  These are <fb:name uid="<?php echo $prints_id ?>" possessive="true"/> Footprints:<br/>
  <?php echo render_prints($prints, 10); ?>
</div>
