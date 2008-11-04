<span id="numfriends" style="display:none;" ><?php echo $numfriends ?></span>
You may select <span id="social_paymentNumRemaining" ><?php echo $numfriends ?></span> more friends.
<rs:payment-form>
    <input type="hidden" name="action" value="invite" />
    <input type="hidden" name="originalLocation" value="<?php echo $originalLocation; ?>" />
    <input type="hidden" name="aid" value="<?php echo $aid; ?>" />
    <input type="hidden" name="cctype" value="<?php echo $cctype; ?>" />
    <input type="hidden" name="ccn" value="<?php echo $ccn; ?>" />
    <input type="hidden" name="expmonth" value="<?php echo $expmonth; ?>" />
    <input type="hidden" name="expyear" value="<?php echo $expyear; ?>" />
    <input type="hidden" name="email" value="<?php echo $email; ?>" />
    <input type="hidden" name="phone" value="<?php echo $phone; ?>" />
    <input type="hidden" name="selectedPlan" value="<?php echo $planid; ?>" />
    <fb:multi-friend-selector actiontext="Pay for your friends!" />
</rs:payment-form>
