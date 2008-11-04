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
include_once 'ringside/api/clients/SuggestionClient.php';
include_once 'ringside/social/config/RingsideSocialConfig.php';
include_once 'ringside/api/clients/RingsideRestClient.php';

class rsSuggestionsHandler {
	
	private $style = 'yesno';
	private $suggestions;
	private $topics;

	private function loadTopicsAndSuggestions( $application ) {
	   if( !empty( $this->suggestions )  ) {
	      return;
	   }
	   
	   $restClient = $application->getClient();
	   $suggestionClient = new SuggestionClient( $restClient );
	   $friends = $restClient->friends_get();
	   $friends[] = $application->getCurrentUser(); //add current user to list of friends in order to get suggestions of current user as well as their friends
//	   $this->suggestions = $suggestionClient->suggestion_get( $application->getApplicationId(), $friends );
	   $this->suggestions = $suggestionClient->suggestion_get( 'yaya', $friends );
	   
	   $this->topics = array();
	   if ( !empty( $this->suggestions ) ) {
	      foreach( $this->suggestions as $suggestion ) {
	         if( !in_array( array( $suggestion['owneruid'], $suggestion['topic'] ), $this->topics ) ) {
	            $this->topics[] = array( $suggestion['owneruid'], $suggestion['topic'] );
	         }
	      }
	   }
	}
	
	/**
	 * Emits the form for submitting suggestions, as appropriate.
	 */
	function doStartTag( $application, &$parentHandler, $args ) {
	   
		if( isset( $args['showform'] ) && strcasecmp( $args['showform'], 'true' ) == 0 ) {
			echo '<div class="suggest_form">';
			echo '<h3>Suggest something.</h3>';
			echo '<br/>';

			echo '<form method="post" action="" id="suggestion_form">';
			echo '<input type="hidden" name="uid" value="'.$application->getCurrentUser().'" />';
			if( ( isset( $args['topic'] ) && $args['topic'] != 'all' ) && 
				( $args['newtopics'] == 'false' && $args['existingtopics'] == 'false' ) ) {
				echo '<input type="hidden" name="newtopic" value="' . $args['topic'].'" />';
			}
			if( isset( $args['newtopics'] ) && strcasecmp( $args['newtopics'], 'true' ) == 0 ) {
				echo '<div class="suggest_topic_label">New Topic:</div>';
				echo '<div class="suggest_topic_input"><input type="text" name="newtopic" size="80" maxlength="80" /></div>';
				echo '<br />';
			}
			
			//call API to get existing suggestions for this user
   			$this->loadTopicsAndSuggestions( $application  );

			// Only display options if there are existing topics in the user's social network
			if( !isset( $args['existingtopics'] ) || !strcasecmp( $args['existingtopics'], 'false' ) == 0 ) {
	   			if( !empty( $this->topics ) ) {
					echo '<div class="suggest_topic_label">Existing Topic:</div>';
					echo '<div class="suggest_topic_input">';
					echo '   <select name="existingtopic"><option selected="true" value="none">-</option>';
					foreach( $this->topics as $topic ) {
						echo '      <option value="' . $topic[0] . ':' . $topic[1] . '">' . $topic[1] . '</option>';
					}
					echo '   </select>';
					echo '</div>';
					echo '<br />';
				}
			}
			echo '<div class="suggest_text_label">Suggestion:</div>';
			echo '<div class="suggest_text_input"><input type="text" name="suggestion" size="80" maxlength="80" /></div>';
			echo '<br />';
			echo '<div class="suggest_submit"><input type="submit" id="suggestion_submit" name="submit" value="Post" /></div>';
			echo '</form>';
			echo '</div>';
		} else {
		   echo '<br /><a href="index.php">Make Suggestions</a><br />';		   
		}
		
  		return 'rs:ratings';
	}
   
	function doBody( $application, &$parentHandler, $args, $body ) {
	}

	function doEndTag( $application, $parentHandler, $args ) {

   		$this->loadTopicsAndSuggestions( $application  );
   		
		$lasttopic = '';

		$count = 0;
		if ( !empty( $this->suggestions ) ) {
			$iids = array();
			foreach( $this->suggestions as $suggestion ) {
		      $topic = $suggestion['topic'];
		      if( !isset( $args['topic'] ) || $args['topic'] == 'all' || $args['topic'] == $topic ) {
			      try {
			         $names = $application->getClient()->users_getInfo( $suggestion['uid'], "first_name" );
			         $name = $names[0];
			         $firstname = $name['first_name'];
			      } catch( Exception $e ) {
			         $firstname = 'Friend of Friend';
			      }
	
			      // Checking for topic switches
			      if( $topic != $lasttopic ) {
			         if( $count > 0 ) {
			            echo '</table>';
			         }
			         echo '<br />';
			         echo '<table border="0" width="500px" cellpadding="5">';
			         
			         $colspan = isset( $this->style ) ? 3 : 2;
			         
			         echo '  <tr><td width="500px" colspan="' . $colspan .'" bgcolor="#f7931d">Topic: ' . $topic . '</td></tr>';
			         $lasttopic = $topic;
			      }
			      
			      echo '  <tr>';
			      if( isset( $this->style ) ) {
	
	 		      	 $rest = new RingsideRestClient( $application->getClient() );
	 		      	 
					 $iids[] = $suggestion['sid'];
					 $rest->items_setInfo( $suggestion['sid'], 'trail6', 'trail6', null );
	
					 //Now get ratings    
					 $ratings = array();
					 if ( count( $iids ) > 0 ) {
						$ratings = $rest->ratings_get( $iids );
						if ( !$ratings ) $ratings = array();
					 }
			      	
					 $yes = 'black';
					 $no = 'black';
					 foreach( $ratings as $rating )
					 {
						$iid = $rating['iid'];
						if( $iid == $suggestion['sid'] )
						{
							$vote = $rating['vote'];
							if( $vote == 1 )
							{
								$yes = 'green';
							}else
							{
								$no = 'red';
							}
						}
					 }
				
					 $vote_string = '<a href="index.php?action=rate&vote=1&iid='.$suggestion['sid'].'"><font color="'.$yes.'">yes</font></a>|<a href="index.php?action=rate&vote=0&iid='.$suggestion['sid'].'"><font color="'.$no.'">no</font></a> ';
			      	 echo '      <td width="5%" bgcolor="#eeeeee">' . $vote_string . '</td>';
					 
		          }
			      
			      echo '      <td width="30%" bgcolor="#fdc589">' . $suggestion['suggestion'] . '</td>';
			      echo '      <td width="65%" bgcolor="#eeeeee">posted by ' . $firstname . ' at ' . $suggestion['created'] . '</td>';
			      echo '  </tr>';
			      $count++;
			   }
			}		   
		}
		if ( $count > 0 ) { 
		   echo '</table>';
		}
	}
	
	function setStyle( $style ) {
		$this->style = $style;
	}
	
	function __toString() {
		return 'rsSuggestionsHandler';
	}
 
}
?>
