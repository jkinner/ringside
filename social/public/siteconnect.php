<?php

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@sociodyne.com>
 */
require_once('ringside/api/clients/RingsideApiClients.php');
require_once('ringside/social/RingsideSocialUtils.php');

if ( isset($_REQUEST['social_session_key']) )
{
    $client = RingsideSocialUtils::getAdminClient();
    $domain_info = $client->admin_getDomainProperties(array('secret_key'), null, $_REQUEST['network_key']);
    error_log("For network ".$_REQUEST['network_key'].", the values are: ".var_export($domain_info, true));
    $secret = $domain_info['secret_key'];
    $params = array(
        'social_session_key'	=> $_GET['social_session_key'],
        'next'						=> $_GET['next']
    );
    
    error_log("Verifying signature with params: ".var_export($params, true)." and secret '$secret'");
    
    $check_sig = Facebook::generate_sig($params, $secret);
    if ( $check_sig == $_REQUEST['sig'])
    {
        $social_session_key = $_GET['social_session_key'];
        error_log("Site connect signature verified. Setting cookie.");
        setcookie('PHPSESSID', $social_session_key);
        $next = $_REQUEST['next'];
        // TODO: Think about restricting this redirect to the registered site's domain, like app login redirection
        if ( strpos($next, '?') !== false )
        {
            $next .= "&";
        }
        else
        {
            $next .= "?";
        }
        
        $params = 
            array(
            	'sc_social_session_key' => $social_session_key,
            	'sc_sig' => Facebook::generate_sig(array('social_session_key' => $social_session_key), $domain_info['secret'])
            );
        $next .= http_build_query($params);

        header('Location: '.$next, null, 302);
        exit;
    }
    else
    {
        error_log("WARNING: Site Connect signature verification failed ($check_sig expected, ".$_REQUEST['sig']." found)");
    }
}
else
{
    error_log("Invalid Site Connect session request");
}
?>
Invalid request