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
require_once( 'ringside/social/api/RingsideSocialApiTrust.php' );
require_once( 'ringside/social/api/RingsideSocialApiRenderFBML.php' );
require_once('ringside/api/clients/RingsideApiClientsRest.php');

require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/social/client/RingsideSocialClientLocal.php' );

/**
 * @optional social_session_key The session key obtained from this server.  Used mostly for widgets, but can be used from any remote app.
 * @optional method Currently this can be fbml or app.
 * @optional fbml Tells the renderer to render the fbml passed in by going directly to the dsl social layer.
 * @optional app Tells the renderer to render an actual application without the normal noise you get when going through canvas.php
 * @optional view This is the view you want to render with an app, sidebar, canvas, wide, narrow, etc...
 * @optional path The actual file you want rendered for the app.  Defaults to your apps callback_url,
 * 		but if this is set to a recommended path of something like http://localhost/apps/myapp/ then index.* will be called.
 * 		if path is set to say, admin.php then http://localhost/apps/myapp/admin.php would be called instead of index.php for example.
 *
 */
class RingsideSocialServerRender
{	

	private $debugMode=false;
	private function debug($text){
		if($this->debugMode){
			error_log("RingsideSocialServerRender:".$text);
		}
	}
	private function debugVar($var){
		if($this->debugMode){
			error_log("RingsideSocialServerRender:".var_export($var,true));
		}		
	}
	
	public function execute($params)
	{
		$this->debug('Entering');
		$this->debugVar($params);
		$network_session = null;
/*
		foreach($params as $k => $v)
		{
			error_log("RingsideSocialServerRender: $k=$v");
		}
*/
		// Recreate Session if we have it
		error_log("Parameters for widget render are: ".var_export($params, true));
		if(array_key_exists('social_session_key', $params))
		{
			$session_key = $params['social_session_key'];
			$network_session = new RingsideSocialSession($session_key);
			$uid = $network_session->getUserId();
			if(null == $uid || strlen($uid) == 0)
			{
				setcookie('social_session_key', $network_session->getSessionKey());
				$uid = $network_session->getUserId();
				if ( isset($_REQUEST['uid']) ) {
				    // TODO: SECURITY: I don't think we should just be able to override the uid.
				    $uid = $_REQUEST['uid'];
				    // TODO: SECURITY: This shouldn't be a valid way to log in.
    				$network_session->setUserId($uid);
    				$network_session->setLoggedIn(true);
				}
			}
		} else if ( isset($_COOKIE['PHPSESSID']) ) {
			// Optimization if user is already logged into web front-end
			$network_session = new RingsideSocialSession($_COOKIE['PHPSESSID']);
			$uid = $network_session->getUserId();
			
			if(! isset($uid)){
				// The user has a network session but is not logged in
				// Run as an anonymous user
				$trust = new RingsideSocialApiTrust($_REQUEST);
				$network_session = $trust->getAnonymousSession();				
			}
		} else {
			// Not logged in, so login via annonymous user
			$trust = new RingsideSocialApiTrust($_REQUEST);
			$network_session = $trust->getAnonymousSession();
		}

		if ( null == $network_session->getApiSessionKey($params['api_key']) ) {
		    $rest = RingsideSocialUtils::getAdminClient();
		    $app_props = $rest->admin_getAppProperties(array('secret_key'), null, null, $params['api_key'], $network_session->getNetwork());
		    RingsideSocialUtils::getApiSessionKey($params['api_key'], $app_props['secret_key'], $network_session);
		}
		
		if(array_key_exists('method', $params))
		{
			$method = $params['method'];

			if(strcasecmp($method, 'fbml')==0 && array_key_exists('fbml', $params))
			{
				$fbml = $params['fbml'];
//error_log("fbml: $fbml");
				$render = new RingsideSocialApiRenderFBML($params);
				$result = $render->render($network_session, $fbml);
//error_log("content: ".$result['content']);
				return isset($result['content'])?$result['content']:$result['error'];
			}else if(strcasecmp($method, 'app')==0)
			{
				$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $network_session->getSessionKey());

				$inSession = $social->inSession();

				error_log("User ".($inSession?'is':'is not')." in session");
				if($inSession)
				{
					$path = '';
					if(array_key_exists('path', $params))
					{
						$path = $params['path'];
					}

					$view = 'canvas';
					if(array_key_exists('view', $params))
					{
						$view = $params['view'];
					}
//error_log("About to render: ".$params['app']." view: $view, path: $path");
		    $rest = RingsideSocialUtils::getAdminClient();
		    $app_props = $rest->admin_getAppProperties(array('application_id', 'canvas_url'), null, null, $params['api_key'], null, $network_session->getNetwork());
		    $domain_props = $rest->admin_getDomainProperties(array('resize_url'), null, $network_session->getNetwork());
					$content = $social->render( $view, $app_props['application_id'], $app_props['canvas_url'], $path );
					// TODO: Is this where error reporting should happen?
//error_log("content: $content");
                if ( isset($domain_props['resize_url']) ) {
                    $content = "<html><head><script type=\"text/javascript\">
      function resizeIframe(id) {
        var iframe = document.getElementById( 'xdiframe' );
        var wrapper = document.getElementById( 'wrapper' );
        var height = Math.max( document.body.offsetHeight, document.body.scrollHeight );
        var width = Math.max( document.body.offsetWidth, document.body.scrollWidth );
        iframe.src = '{$params['resizeUrl']}?height='+height+'&width='+width+'&id='+id;
      }
</script></head><body onload=\"resizeIframe('if_".$params['api_key']."');\">".$content."<iframe id='xdiframe' width='1' height='1' frameborder='0'/></body></html>";
                }
					return $content;
				}else
				{
					echo "<error>User not Logged in!</error>";
				}
    		}
		} else {
		    error_log("No method specified for render request");
		}
	}
}

?>
