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

require_once('ringside/web/config/RingsideWebConfig.php');
?>
<h1>Friends</h1>
<div class="">Search your friends:
<form onsubmit="" method="post"><input type="hidden" id="view"
	name="view" value="search" /> <input type="search" id="query"
	name="query" value="" /></form>
</div>
<BR>
<BR>
<div><fb:tabs>
	<fb:tab-item href='friends.php' title='Everyone' selected='true' />
	<fb:tab-item href='friends.php?view=add_friend' title='Add Friend' />
	<fb:tab-item href='friends.php?view=view_invites' title='View Invites' />
</fb:tabs></div>
<?php
require_once( 'ringside/api/clients/RingsideApiClients.php');

$client = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);
$uid = $client->require_login();
//$user_name = $client->api_client->users_getInfo($uid, "first_name");
$url = $client->get_openfb_url();

$view = null;

isset($_REQUEST['view'])? $view = $_REQUEST['view']:$view = null;

if($view == null || $view == 'everyone')
{
    // If it's not a view, check the action
    if(isset($_REQUEST['action']))
    {
        $error = '';
        $message = '';
        $action = $_REQUEST['action'];
        if($action == 'add_friend')
        {
            if(isset($_REQUEST['email']) && !empty($_REQUEST['email']))
            {
                $next = isset($_REQUEST['next'])?$_REQUEST['next']:'/friends.php?view=view_invites';
                $domain_info = $client->api_client->admin_getDomainProperties(array('url'), null, $_REQUEST['fb_sig_nid']);
                $domain_url = $domain_info['url'];
                if ( strpos($next, $domain_url) !== false )
                {
                    $rsvp_url = $next;
                } else {
                    $rsvp_url = $domain_url . $next;
                }
                $client->api_client->friends_inviteEmail($_REQUEST['email'], $rsvp_url);
            } else if (isset($_REQUEST['name']) && !empty($_REQUEST['name']))
            {
                $name = $_REQUEST['name'];
                $users = $client->api_client->users_search($name);

                if(isset($users) && count($users) > 0)
                {
                    foreach($users as $user)
                    {
                        if(isset($user) && count($user) > 0 && is_array($user))
                        {
                            $pic = $user['pic_small_url'];
                            $user_name = $user['first_name'].' '.$user['last_name'];
                            $fuid = $user['user_id'];

                            $message .= "<a href='$url/friends.php?action=add_friend&fuid=$fuid&friend_name=$user_name'>Invite $user_name?</a><BR>";
                        }else
                        {
                            $error .= "No user with name $name found!<BR>";
                        }
                    }
                }else
                {
                    $message = "No users found with the name $name";
                }
            }else if(isset($_REQUEST['fuid']) && !empty($_REQUEST['fuid']))
            {
                try{
                    $fuid = $_REQUEST['fuid'];

                    $client->api_client->friends_invite($fuid);
                    // public function notifications_send($to_ids, $notification)
                    $user_name = $client->api_client->users_getInfo($uid, "first_name");
                    $client->api_client->notifications_send(array($fuid), "$user_name has invited you to be a friend.");
                    $message = 'Friend invite sent to '.$_REQUEST['friend_name'];
                }catch(Exception $e)
                {
                    $error = $e->getMessage();
                }
            }else
            {
                $error = 'Invalid Name!';
            }
            include_once('addfriend.php');
        }else if($action == 'accept_friend')
        {
            $fuid = $_REQUEST['fuid'];
            $access = $_REQUEST['access'];
            $status = $_REQUEST['status'];
            $inv = isset($_REQUEST['inv'])?$_REQUEST['inv']:null;
            
            $user_info = $client->api_client->users_getInfo($fuid, "first_name");
            $friend_name = $user_info[0]['first_name'];
            try{
                $client->api_client->friends_accept($fuid, $status, $access);
                if($status != 0)
                {
                    $message = "$friend_name has been successfully added to your friends list!";
                }else
                {
                    $message = "$friend_name has been denied friend status!";
                }
            }catch(Exception $e)
            {
                $error = "Unable to add $friend_name to your friends list: ".$e->getMessage();
            }

            $invites = $client->api_client->friends_getInvites();
            include_once('view_invite.php');
        }else
        {
            include_once('everyone.php');
        }
    }else
    {
        $friends = $client->api_client->friends_search('');
        include_once('everyone.php');
    }
}else if($view == 'add_friend')
{
    include_once('addfriend.php');
}else if($view = 'view_invites')
{
    $inv_source = $_REQUEST['inv'];
    if ( isset($_REQUEST['inv']) )
    {
        $email_inv = $client->api_client->friends_getEmailInvite($_REQUEST['inv']);
        $invites = array($email_inv['invitation']['fuid']);
    } else {
        $invites = $client->api_client->friends_getInvites();
    }
    
    include_once('view_invite.php');
}else if( $view == 'search')
{
    $query = '';
    if(isset($_Request['query']))
    {
        $query = $_REQUEST['query'];
    }

    error_log("search query: $query");
    $friends = $client->api_client->friends_search($query);
    include_once('search.php');

}else if($view == 'status')
{
    include_once('status.php');
}else if($view == 'recent')
{
    include_once('recent.php');

}else if($view == 'online')
{
    include_once('online.php');
}
?>


