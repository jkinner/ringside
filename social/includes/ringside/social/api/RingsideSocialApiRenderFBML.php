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

require_once( 'ringside/social/api/RingsideSocialApiBase.php' );
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'ringside/social/config/RingsideSocialConfig.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslParser.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslContext.php' );
require_once( 'ringside/social/dsl/RingsideSocialDslFlavorContext.php' );
require_once( 'ringside/api/clients/RingsideApiClientsRest.php' );

class RingsideSocialApiRenderFBML extends RingsideSocialApiBase
{
	public function __construct(&$params )
	{
		parent::__construct( $params );
	}

	/**
	 * Renders the fbml into text and returns it.
	 *
	 * @param RingsideSocialSession $network_session
	 * @param unknown_type $fbmlText
	 * @return unknown
	 */
	public function render( RingsideSocialSession $network_session, $fbmlText)
	{
		$response = array();
		$error = null;
		// Exceptions are valid FBML and should be returned
		// to the end user.
		//try {
			$api_key = $this->getParam('api_key');
			
			// build a Social Session to get the properties for the api key passed in
			$apiSessionKey = RingsideSocialUtils::getApiSessionKey( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $network_session );
			$apiClientSocial = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $apiSessionKey );

			// Get the app properties						
			$result = $apiClientSocial->admin_getAppProperties( "application_id,application_name,api_key,secret_key,callback_url" , null, null, $api_key );
			$secret = $result['secret_key'];
			$app_id = $result['application_id'];
			
			// Now create the real session for this api
			$session_key = RingsideSocialUtils::getApiSessionKey( $api_key, $secret, $network_session );
			$restClient = new RingsideApiClientsRest( $api_key, $secret, $session_key );
			 
			$text = $this->renderFbml( $fbmlText, $network_session, $restClient, $app_id);

			if ( !empty( $text ) ) $response['content'] = $text;
//		} catch ( Exception $exception ) {
//			error_log( "Exception : " . $exception->getMessage()." \n".$exception->getTraceAsString() );
//			$error = RingsideSocialUtils::SOCIAL_ERROR_RENDER_EXCEPTION;
//		}

		if ( $error != null ) $response['error'] = $error;

		return $response;
	}

	public function renderFbml( $fbmlText, $network_session, $apiClient, $app_id )
	{
		if ( !empty ( $fbmlText ))
		{
			try {
				$flavor_context = new RingsideSocialDslFlavorContext();
				$context = new RingsideSocialDslContext( $apiClient, $network_session, $flavor_context, $app_id );
				$parser = new RingsideSocialDslParser( $context );

				$flavor_context->startFlavor('canvas');
				$text = $parser->parseString( $fbmlText );
				$flavor_context->endFlavor('canvas');

				$this->redirect = $parser->getRedirect();
				return $text;

			} catch ( Exception $exception ) {
				$this->error = $exception->getMessage();
				error_log(  "Exception thrown during parsing page ($exception)" );
			}
		}

		return null;
	}
}


?>
