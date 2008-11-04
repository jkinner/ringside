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

require_once( 'shindig/gadgets/GadgetTokenDecoder.php' );
require_once( 'shindig/gadgets/GadgetTokenDecoder.php' );
/**
 * Adds a factory for the creation of gadget tokens from Ringside Social Sessions.
 * 
 * @author William Reichardt <wreichardt@ringsidenetworks.com>
 * 
 */
class RingsideGadgetTokenDecoder extends GadgetTokenDecoder {
	private $OWNER_INDEX = 0;
	private $VIEWER_INDEX = 1;
	private $APP_ID_INDEX = 2;
	private $CONTAINER_INDEX = 3;
	private $APP_URL_INDEX = 4;
	private $MODULE_ID_INDEX = 5;

	/**
 	* {@inheritDoc}
	*
 	* Returns a token with some faked out values.
 	*/
	//TODO Assume the token is a  ringside social_session_key : api_key
	public function createToken($stringToken)
	{
		if (empty($stringToken)) {
			throw new GadgetException('INVALID_GADGET_TOKEN');
		}
		return RingsideGadgetToken::createFromSocialSession($stringToken);
	}
}
