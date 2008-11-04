<?php
/**
 * To be of any use, this payment application expects the following parameters to be submitted to it:
 * <ul><li>apikey - The public key of the application that is being paid for
 */

require_once("header.inc");

$aid = getRequestParam( 'aid' );
if( empty( $aid ) ) {
	$props = $client->api_client->admin_getAppProperties( 'application_id', null, null, getRequestParam( 'apikey' ) );
	$aid = $props[ 'application_id' ];
}

$info = $client->api_client->users_getInfo( $uid, array( 'first_name', 'last_name' ) );
$firstName = isset( $info[0][ 'first_name' ] ) ? $info[0][ 'first_name' ] : '';
$lastName = isset( $info[0][ 'last_name' ] ) ? $info[0][ 'last_name' ] : '';

$action = getRequestParam( 'action' );
if( !isset( $action) ) {
	//	error_log( 'action not set' );
	$originalLocation = getenv( 'HTTP_REFERER' );
	include_once( 'view/form.tpl' );
}
else if( $action == 'invite' ) {
	$flds = array( 'application_name' );
	$resp = $client->api_client->admin_getAppProperties( implode( ',', $flds ), $aid, null, null );
	$appname = $resp[ 'application_name' ];
    $originalLocation = getRequestParam( 'originalLocation' );
	$cctype = getRequestParam( 'cctype' );
	$cctypestr = getCcTypeString();
	$ccn = getRequestParam( 'ccn' );
	$expmonth = getRequestParam( 'expmonth' );
	$expyear = getRequestParam( 'expyear' );
	$email = getRequestParam( 'email' );
	$phone = getRequestParam( 'phone' );
    $planid = getRequestParam( 'selectedPlan' );
    $ids = getRequestParam( 'ids' );
    $plans = $client->api_client->subscriptions_get_app_plans( $aid );
	foreach( $plans as $plan ) {
		if( $plan[ 'plan_id' ] == $planid ) {
			$planname = $plan[ 'name' ];
			$price = $plan[ 'price' ];
			$numfriends = $plan[ 'num_friends' ];
			if( isset( $numfriends ) && !empty( $numfriends ) && !isset( $ids ) ) {
				include_once( 'view/socialpay.tpl' );
			}
			else {
                if( isset( $ids ) && !empty( $ids ) ) {
					$theids = split( ',', $ids );
                }
				include_once( 'view/confirm.tpl' );
			}
		}
	}
}
else if( $action == 'confirm' ) {
	$originalLocation = getRequestParam( 'originalLocation' );
	$flds = array( 'application_name' );
	$resp = $client->api_client->admin_getAppProperties( implode( ',', $flds ), $aid, null, null );
	$appname = $resp[ 'application_name' ];
	$cctype = getRequestParam( 'cctype' );
	$cctypestr = getCcTypeString();
	$ccn = getRequestParam( 'ccn' );
	$expmonth = getRequestParam( 'expmonth' );
	$expyear = getRequestParam( 'expyear' );
	$email = getRequestParam( 'email' );
	$phone = getRequestParam( 'phone' );
	$planid = getRequestParam( 'selectedPlan' );
	$ids = getRequestParam( 'ids' );
	$plans = $client->api_client->subscriptions_get_app_plans( $aid );
	foreach( $plans as $plan ) {
		if( $plan[ 'plan_id' ] == $planid ) {
			$planname = $plan[ 'name' ];
			$price = $plan[ 'price' ];
		}
	}
	$expdate = $expmonth . '/' . $expyear;
	try {
		if( isset( $ids ) && !empty( $ids ) ) {
            $subscription = $client->api_client->social_pay_subscribe_users_to_app( $uid, null, $aid, $planid, $cctype, $ccn,
                $expdate, $ids, $firstName, $lastName, $email, $phone );
            $theids = split( ',', $ids );
		}
        else {
            $subscription = $client->api_client->subscribe_user_to_app( $uid, null, $aid, $planid, $cctype, $ccn,
                $expdate, $firstName, $lastName, $email, $phone );
        }
		if( array_key_exists( 'subscription', $subscription ) &&
		!empty( $subscription[ 'subscription' ][ 'gateway_subscription_id' ] ) ) {
			$action = 'confirmed';
			include_once( 'view/confirm.tpl' );
		}
	}
	catch( Exception $e ) {
		echo 'Subscription not created: ' . $e->getMessage();
		error_log( 'Subscription not created: ' . $e->getMessage() . '. ' . $e->getTraceAsString() );
	}
}

//echo '<br />REQUEST PARAMS:';
//echo '<br />---------------';
//foreach( $_REQUEST as $key => $value ) {
//	echo '<br />' . $key . ' = ' . $value;
//}

?>
