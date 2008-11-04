
<fb:tabs>
	<fb:tab-item href='edit_app.php?form_action=edit&app_id=<?php echo $appId; ?>' title='App Properties'
		selected='true' selected='<?php echo $appPropsSelected; ?>' />
	<fb:tab-item href='edit_app_keys.php?app_id=<?php echo $appId; ?>'
		title='Manage Keys' selected='<?php echo $manageKeysSelected; ?>' />
	<fb:tab-item href='edit_app_pay_plans.php?app_id=<?php echo $appId; ?>'
		title='Payment Plans'  selected='<?php echo $payPlansSelected; ?>' />
</fb:tabs>