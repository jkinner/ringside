
<?php
/*******************************************************************************
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
 ******************************************************************************/

require_once( "header.inc" );
$props = array( "application_id", "application_name", "api_key" );
$apiKey = getRequestParam( 'api_key' );
$appId = getRequestParam( 'app_id' );
$resp = $client->api_client->admin_getAppProperties( $props, $appId );
$appName = $resp["application_name"];
$pageHeader = "Edit Application: $appName";

$action = getRequestParam( 'action' );
if( $action == 'add' ) {
	$result = $client->api_client->subscription_add_app_plan( $appId, getRequestParam( 'name' ), getRequestParam( 'price' ), getRequestParam( 'description' ), getRequestParam( 'numfriends' ) );
	$message = $result ? 'Plan successfully added!' : 'Error!  Plan not added.';
}
else if( $action == 'delete' ) {
	$selectedPlans = getRequestParam( 'selectedPlan' );
	foreach( $selectedPlans as $plan ) {
		$result = $client->api_client->subscription_delete_app_plan( $plan );
		$message = $result ? 'Plan successfully deleted!' : 'Error!  Plan not deleted.';
	}
}
?>

<h1>Developer</h1>
<h2><?php echo $pageHeader; ?></h2>

<?php
$appPropsSelected = 'false';
$payPlansSelected = 'true';
$manageKeysSelected = 'false';
include("ringside/apps/developer/templates/tabitems.tpl");

?>


<?php if ( $action == 'add' || $action == 'delete' ) { ?>
<fb:explanation message="<?php echo $message; ?>" />
<?php } ?>
<fb:editor action="edit_app_pay_plans.php?action=add" >
	<fb:editor-custom>
		Add a new pricing plan for your application.
	</fb:editor-custom>
	<fb:editor-custom>
		<input type="hidden" name="app_id" value="<?php echo $appId; ?>" />
	</fb:editor-custom>
	<fb:editor-text label="Plan Name" name="name" />
	<fb:editor-text label="Monthly Price" name="price" />
	<fb:editor-text label="Description" name="description" />
    <fb:editor-text label="Number of friends" name="numfriends" />
	<fb:editor-buttonset>
		<fb:editor-button name="add_button" value="Add Plan" class="btn-input"  />
	</fb:editor-buttonset>
</fb:editor>

<fb:editor action="edit_app_pay_plans.php?action=delete">
	<fb:editor-custom>
		<input type="hidden" name="app_id" value="<?php echo $appId; ?>" />
	</fb:editor-custom>
	<rs:payment-plans aid="<?php echo $appId ?>" editable="true" />
</fb:editor>
