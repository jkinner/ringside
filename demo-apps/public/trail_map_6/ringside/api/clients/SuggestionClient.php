<?php

require_once('ringside/api/clients/RingsideRestClient.php');

class SuggestionClient
{
   private $delegate;
    
   /**
    * Constructs a Ringside-specific REST client.
    *
    * @param OpenFBRestClient $delegate the delegate to be used for invoking the operations.
    */
   public function __construct($delegate) {
      $this->delegate = $delegate;
   }

   /**
    * Add a suggestion to a given topic.
    * @param $topic String name of the topic.
    * @param $owner Integer uid of topic owner.
    * @param $apiKey API key of app that owns topic.
    * @param $suggestion String representing suggestion to topic.
    */
   public function suggestion_add($topic, $owner, $apiKey, $suggestion)
   {
      //construct the parameters
      $params = array();
      $params['topic'] = $topic;
      $params['owner'] = $owner;
      $params['app_api_key'] = $apiKey;
      $params['suggestion'] = $suggestion;

      //make a call to the underlying REST client to add a suggestion
      return $this->delegate->call_method("ringside.suggestion.add", $params);
   }

   /**
    * Get all suggestions for a given API key and user.
    * @param $apiKey API key of app that owns topic.
    * @param $friends Array of friend UIDs (optional)
    */
   public function suggestion_get($apiKey, $friends = array())
   {
      //construct the parameters
      $params = array();
      $params['app_api_key'] = $apiKey;
      $params['friends'] = $friends;

      //make a call to the underlying REST client to add a suggestion
      $response =  $this->delegate->call_method("ringside.suggestion.get", $params);
      if ( empty ($response) ) { 
         $response = array();
      }
      return $response;
   }

   /**
    * Get all suggestions for a given API key and user.
    * @param $apiKey API key of app that owns topic.
    * @param $friends Array of friend UIDs (optional)
    */
   public function suggestion_getTopics($friends = array())
   {
      //construct the parameters
      $params = array();
      $params['friends'] = $friends;

      //make a call to the underlying REST client to add a suggestion
      $response = $this->delegate->call_method("ringside.suggestion.getTopics", $params);
      if ( empty ($response) ) { 
         $response = array();
      }
      return $response;
   }
}

?>