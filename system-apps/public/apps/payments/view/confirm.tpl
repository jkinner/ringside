<p>You have <?php if( $action == 'confirmed' ) { ?>successfully created<?php } else { ?>requested<?php } ?> the following subscription:
</p>
<table border="0">
<tr>
    <td>Application:</td>
    <td><?php echo $appname; ?></td>
</tr>
<tr>
    <td>First Name:</td>
    <td><?php echo $firstName; ?></td>
</tr>
<tr>
    <td>Last Name:</td>
    <td><?php echo $lastName; ?></td>
</tr>
<tr>
    <td>Credit Card:</td>
    <td><?php echo $cctypestr . ' # ' . $ccn; ?></td>
</tr>
<tr>
    <td>Expiration Date:</td>
    <td><?php echo $expmonth . '/' . $expyear; ?></td>
</tr>
<?php if( isset( $email ) && !empty( $email ) ) { ?>
<tr>
    <td>Email:</td>
    <td><?php echo $email; ?></td>
</tr>
<?php } if( isset( $phone ) && !empty( $phone ) ) { 
?>
<tr>
    <td>Phone:</td>
    <td><?php echo $phone; ?></td>
</tr>
<?php } ?>
<tr>
    <td>Plan:</td>
    <td>
<?php 
echo $planname . ' - $' . $price . '/month'; 
if( isset( $numfriends ) && $numfriends > 0 ) {
	echo ', ' . $numfriends . ' friends';
}
?>
    </td>
</tr>
<?php
if( isset( $ids ) && !empty( $ids ) ) {
?>
<tr>
    <td>Friends:</td>
    <td>
<?php 
foreach( $theids as $friend ) {
?>
<div><fb:profile-pic uid="<?php echo $friend; ?>" /><fb:name uid="<?php echo $friend; ?>" />
</div>
<br />
<?php
}
?>
    </td>
</tr>
<?php
}
?>
<?php
if( $action == 'confirm' || $action == 'subscribe' || $action == 'invite' ) {
?>
<tr>
    <td>&nbsp;</td>
    <td>
<?php
    echo "\n" . '<form id="payment-form" action="' . RingsideWebConfig::$webRoot . '/payments" method="post">' . "\n";
?>
    <form action="<?php echo RingsideWebConfig::$webRoot; ?>/payments/index.php">
        <input type="hidden" name="action" value="confirm" />        
        <input type="hidden" name="originalLocation" value="<?php echo $originalLocation; ?>" />
        <input type="hidden" name="aid" value="<?php echo $aid; ?>" />
        <input type="hidden" name="cctype" value="<?php echo $cctype; ?>" />
        <input type="hidden" name="ccn" value="<?php echo $ccn; ?>" />
        <input type="hidden" name="expmonth" value="<?php echo $expmonth; ?>" />
        <input type="hidden" name="expyear" value="<?php echo $expyear; ?>" />
        <input type="hidden" name="email" value="<?php echo $email; ?>" />
        <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
        <input type="hidden" name="selectedPlan" value="<?php echo $planid; ?>" />
        <input type="hidden" name="ids" value="<?php echo $ids; ?>" />
        <input type="submit" value="Confirm" class="btn-submit" />
        <input type="button" value="Go Back" onclick="alert( 'balls' );return true;" />
    </form>
    </td>
</tr>
<?php
}
?>
</table>
<?php
if( $action == 'confirmed' ) {
?>
<p>Click <a href="<?php echo $originalLocation; ?>">here</a> to return to the application.
</p>
<?php
}
?>
