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

class rsFeedHandler {

	function doStartTag( $application, $parentHandler, $args ) {
        return true;
	}

	function doEndTag( $application, $parentHandler, $args ) {
        //error_log( 'args: ' . var_export( $args, true ) );
        //error_log( 'current user: ' . $application->getCurrentUser() );
        $uid = ( !empty( $args[ 'uid' ] ) ) ? $args[ 'uid' ] : $application->getCurrentUser();
        $actorid = isset($args['actorid']) ? $args['actorid'] : NULL;
        $friends = ( !empty( $args[ 'friends' ] ) ) ? $args[ 'friends' ] : false;
        $actions = ( !empty( $args[ 'actions' ] ) ) ? $args[ 'actions' ] : true;
        $stories = ( !empty( $args[ 'stories' ] ) ) ? $args[ 'stories' ] : true;
        
//        error_log( 'uid: ' . $uid );
//        error_log( 'actorid: ' . var_export( $actorid, true ) );
//        error_log( 'friends: ' . var_export( $friends, true ) );
//        error_log( 'actions: ' . var_export( $actions, true ) );
//        error_log( 'stories: ' . var_export( $stories, true ) );

        $client = $application->getClient();
        $entries = $client->feed_get( $uid, $actorid, $friends, $actions, $stories );
        //error_log( 'entries: ' . var_export( $entries, true ) );
        //error_log( 'count of entries: ' . count( $entries ) );
        //error_log( 'empty: ' . empty( $entries ) );
        $str = '';
        if( count( $entries ) == 0 || empty( $entries ) ) {
            $str .= '<ul>';
        	$str .= '<li>There are no feed entries to display.</li>' . "\n";
        }
        else {
			$emittedToday = false;
            $emittedYesterday = false;
            $emittedUl = false;
            $dateStack = array();
        	foreach( $entries as $entry ) {
				if( $this->isToday( $entry[ 'created' ] ) ) {
					if( !$emittedToday ) {
    					$str .= "\n\t<h3>Today</h3>";
    					$emittedToday = true;
					}
				}
                else if( $this->isYesterday( $entry[ 'created' ] ) ) {
                	if( !$emittedYesterday ) {
                        $str .= "\n\t<h3>Yesterday</h3>";
                        $emittedYesterday = true;
                	}
                }
                else if( !in_array( $entry[ 'created' ], $dateStack, true ) ) {
                	$displayDate = date( 'F j, Y', strtotime( $entry[ 'created' ] ) );
                	$str .= "\t\t<h3>$displayDate</h3>";
                	$dateStack[] = $entry[ 'created' ];
                }
                if( !$emittedUl ) {
                    $str .= '<ul>';
                    $emittedUl = true;
                }
                if( $entry[ 'templatized' ] ) {
                    $response = $client->users_getInfo( array( $uid ), array( 'first_name', 'last_name') );
//                    error_log( 'response: ' . var_export( $response, true ) );
                    $name = $response[0];
                	$firstName = $name[ 'first_name' ];
                	$lastName = $name[ 'last_name' ];
                	$name = $firstName . ' ' . $lastName;
                	$str .= "\t\t<li>" . $this->parseTemplate( $entry[ 'title' ], $entry[ 'title_data' ], $name ) . "</li>\n";
                }
                else {
                    $str .= "\t\t<li>" . $entry[ 'title' ] . "</li>\n";
                }
        	}
        }
        $str .= '</ul>' . "\n";
//        error_log( 'str: ' . var_export( $str, true ) );
        echo $str;
	}
	
	public function getType() {
		return 'inline';
	}
	
	public function isEmpty() {
		return true;
	}

	private function isToday( $created ) {
		$todayDate = strtotime( date( 'Y-m-d' ) ); 
		$entryDate = strtotime( date( 'Y-m-d', strtotime( $created ) ) );
//        error_log( 'today: ' . $todayDate );
//        error_log( 'created: ' . $entryDate );
		return ( $entryDate == $todayDate );
	}

    private function isYesterday( $created ) {
        
        $today = mktime();
        $yesterday = $today - ( 60 * 60 * 24 );
        
        $yesterdayDate = strtotime( date( 'Y-m-d', $yesterday ) );
        $entryDate = strtotime( date( 'Y-m-d', strtotime( $created ) ) );
//        error_log( 'yesterday: ' . $yesterdayDate );
//        error_log( 'created: ' . $entryDate );
        return ( $entryDate == $yesterdayDate );
    }

    /**
     * Returns an ordered array of replacement data for template items.
     *
     * @param unknown_type $template
     * @param unknown_type $templateData
     * @param unknown_type $name
     * @return unknown
     */
    private function parseTemplate( $template, $templateData, $name ) {
    	$replaceData = array();
//    	error_log( 'template: ' . $template );
    	$tokens = self::get_tokens( $template );
    	foreach( $tokens as $token ) {
//    		error_log( 'token: ' . $token );
    		//replace 'actor' with name
    		if( $token == 'actor' ) {
    			$replaceData[] = $name;
    		}
    		//lookup replacement from template data
    		if( isset( $templateData ) ) {
        		$decoded = json_decode( $templateData );
        		$replaceData[] = $decoded;
    		}
    	}
//    	error_log( 'tokens: ' . var_export( $tokens, true ) );
    	$searchData = array();
        foreach( $tokens as $token ) {
        	$searchData[] = '{' . $token . '}';
        }
//        error_log( 'replaceData: ' . var_export( $replaceData, true ) );
        return str_replace( $searchData, $replaceData, $template );
    }
    
    private static function get_tokens( $txt )
    {
        $matches = array();
        preg_match_all( '|{(.*)}|U', $txt, $matches, PREG_PATTERN_ORDER );
        return array_unique( $matches[1] );
    }
}

?>
