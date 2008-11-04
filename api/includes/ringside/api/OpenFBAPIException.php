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

define( 'FB_ERROR_CODE_DATABASE_ERROR', -1 );
define( 'FB_ERROR_MSG_DATABASE_ERROR', "Database error." );

define( 'FB_ERROR_CODE_UNKNOWN_ERROR', 1 );
define( 'FB_ERROR_MSG_UNKNOWN_ERROR', "An unknown error occurred. Please resubmit the request." );
define( 'FB_ERROR_MSG_BUSTED_SESSION', "No session is available!" );

define( 'FB_ERROR_CODE_SERVICE_NOT_AVAILABLE', 2 );
define( 'FB_ERROR_MSG_SERVICE_NOT_AVAILABLE', "The service is not available at this time." );

define( 'FB_ERROR_CODE_MAX_REQUESTS_REACHED', 4 );
define( 'FB_ERROR_MSG_MAX_REQUESTS_REACHED', "The application has reached the maximum number of requests allowed. More requests are allowed once the time window has completed." );

define( 'FB_ERROR_CODE_REMOTE_ADDRESS_NOT_ALLOWED', 5 );
define( 'FB_ERROR_MSG_REMOTE_ADDRESS_NOT_ALLOWED', "The request came from a remote address not allowed by this application." );

define( 'FB_ERROR_CODE_INVALID_PARAMETER', 95 );

define( 'FB_ERROR_CODE_GRAPH_EXCEPTION', 99 );
define( 'FB_ERROR_MSG_GRAPH_EXCEPTION', 'Cross the social graph permissions Error. You can only cross check application information if the calling application is a default application.');

define( 'FB_ERROR_CODE_PARAMETER_MISSING', 100 );
define( 'FB_ERROR_MSG_PARAMETER_MISSING', "One of the parameters specified was missing or invalid." );
define( 'FB_ERROR_MSG_PARAMETER_JSON_INVALID', "The properties must be specified and valid json entry.");

define( 'FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP', 101 );
define( 'FB_ERROR_MSG_API_KEY_NOT_ASSOCIATED_WITH_APP', "The api key submitted is not associated with any known application." );

define( 'FB_ERROR_CODE_BAD_SESSION_KEY', 102 );
define( 'FB_ERROR_MSG_BAD_SESSION_KEY', "The session key was improperly submitted or has reached its timeout. Direct the user to log in again to obtain another key." );

define( 'FB_ERROR_CODE_CALL_ID_NOT_GREATER', 103 );
define( 'FB_ERROR_MSG_CALL_ID_NOT_GREATER', "The submitted call_id was not greater than the previous call_id for this session." );

define( 'FB_ERROR_CODE_INCORRECT_SIGNATURE', 104 );
define( 'FB_ERROR_MSG_INCORRECT_SIGNATURE', "Incorrect signature." );

define( 'FB_ERROR_CODE_REQUIRES_PERMISSION', 205);
define( 'FB_ERROR_MSG_REQUIRES_PERMISSION', "User does not have status_update permission for this application." );
define( 'FB_ERROR_MSG_ACTOR_USER_NOT_FRIENDS', "Actor and User are not friends" );
define( 'FB_ERROR_MSG_ACTOR_USER_DONT_SHAREAPPS', "Actor does not have application" );
define( 'FB_ERROR_MSG_TARGETS_NOT_FRIENDS', "Targets not friends of actor" );

define( 'FB_ERROR_CODE_ISFAN_NOTFRIENDS', 210 );
define( 'FB_ERROR_MSG_ISFAN_NOTFRIENDS', 'User is not friends' );

define( 'FB_ERROR_CODE_PHOTO_LIMIT', 321 );
define( 'FB_ERROR_MSG_PHOTO_LIMIT', 'Your photo album has exceeded capacity' );

define( 'FB_ERROR_CODE_PHOTO_INVALID', 324 );
define( 'FB_ERROR_MSG_PHOTO_INVALID', 'Missing or invalid image file' );

define( 'FB_ERROR_CODE_INVALID_MARKUP', 330 );
define( 'FB_ERROR_MSG_INVALID_MARKUP', "The markup was invalid." );

define( 'FB_ERROR_CODE_FEED_STORY_TOO_LONG', 343 );
define( 'FB_ERROR_MSG_FEED_STORY_TOO_LONG', "Feed story title is too long. " );

define( 'FB_ERROR_CODE_FEED_STORY_BLANK', 345 );
define( 'FB_ERROR_MSG_FEED_STORY_BLANK', "Feed story title rendered as blank." );

define( 'FB_ERROR_CODE_FEED_TITLE_JSON', 360 );
define( 'FB_ERROR_MSG_FEED_TITLE_JSON_EMPTY', 'Title arguments empty. ');
define( 'FB_ERROR_MSG_FEED_TITLE_JSON_INVALID', 'Feed story title includes actor or targets.');

define( 'FB_ERROR_CODE_FEED_TITLE_PARAMS', 361 );
define( 'FB_ERROR_MSG_FEED_MISSING_PARAMS', "Feed story title template missing parameters defined in the title_data array." );
define( 'FB_ERROR_MSG_FEED_MISSING_ACTOR', "Actor missing from title template" );
define( 'FB_ERROR_MSG_FEED_MISSING_TARGETS', "Title included targets but none specified." );

define( 'FB_ERROR_CODE_FEED_BODY_JSON', 362 );
define( 'FB_ERROR_MSG_FEED_BODY_JSON_EMPTY', 'Body arguments empty. ');
define( 'FB_ERROR_MSG_FEED_BODY_JSON_INVALID', 'Feed story body includes actor or targets.');

define( 'FB_ERROR_CODE_FEED_BODY_PARAMS', 363 );
define( 'FB_ERROR_MSG_FEED_BODY_MISSING_PARAMS', "Feed story body template did not have all parameters defined in the body_data array." );
define( 'FB_ERROR_MSG_FEED_BODY_MISSING_TARGETS', "Body included targets but none specified." );

define( 'FB_ERROR_CODE_FEED_BAD_IMAGES', 364 );
define( 'FB_ERROR_MSG_FEED_BAD_IMAGES', "Feed story photos could not be retrieved, or bad image links were provided." );

define( 'FB_ERROR_CODE_INVALID_TARGET_IDS', 366 );
define( 'FB_ERROR_MSG_INVALID_TARGET_IDS', "One or more of the target IDs for this story are invalid. They must all be IDs of friends of the acting user." );	 

define( 'FB_ERROR_CODE_NO_APP', 900 );

class OpenFBAPIException extends Exception
{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}

?>
