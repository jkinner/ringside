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
require_once 'ringside/api/DefaultRest.php';
require_once 'ringside/api/OpenFBAPIException.php';
require_once 'ringside/api/dao/User.php';

define ( 'AUTH_PLUGINS', 'ringside/api/auth/' );

/**
 * Creates a Ringside local user specific to this network.
 *
 * @author Richard Friedman
 * @apiName admin.CreateUser
 * @apiRequired username - user identifier
 * @apiRequired type - db|openid - the backend type to persist
 * @apiOptional linked - userid - if set account will be linked to UID.
 * @apiOptional ??? - Any parameters needed for back end type
 * @callMethod ringside.admin.createUser
 * @return Array the newly created USERID.
 */
class AdminCreateUser extends Api_DefaultRest {
    
   /* The network the user created this from */
   private $nid;

   /* What type of user auth are we creating?  DB|OpenID|LDAP(not implemented yet) */
   private $type;
   
   /* Should we link this to a known user account */
   private $link;
   
   /* need to pass through the params */
   private $params;
    
   /** List is managed to not just load from files system */
   public $plugins = array ( 'database'=>'Database', 'openid'=>'OpenID' );

   public function validateRequest() {

      $type = $this->getRequiredApiParam('type');
      if ( in_array( $type, $this->plugins ) === false ) {
         throw new OpenFBAPIException( "Type does not match expected.", FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }
      
      $this->link = $this->getApiParam( 'link',  false );
      $this->type = $this->plugins[ $type ];
      $this->params = $this->getContext()->getInitialRequest();
      
   }

   /**
    * Creates the user if it's not in the DB.  Throws an exception if the user already exists!
    *
    * @return Array[user][id]
    * @throws Exception
    */
   public function execute() {

      //make sure calling application is a default application
      $this->checkDefaultApp();
      
      require_once( AUTH_PLUGINS . $type . '.php' );
      $method = new $className();
      $userid = $method->findUser( $apiParams );
      
      if ( !empty( $userid ) ) {
         throw new OpenFBAPIException("User credentials already exist!", 1 );
      }

      if ( !empty( $linked ) ) { 
         // Link a give UID to a set of credentials.
         $user = RingsideOpenFBDbAuth::findUser( $linked );
         if ( $user === false ) { 
            throw new OpenFBAPIException("Linked UID does not exist", 1 );
         }
         
         $method->linkUser( $linked, $apiParams );
                  
      } else { 

         $user = new RingsideOpenFBDbAuth();
         $user->setNid( $this->getNetworkId() );
         $user->insertIntoDb( $this->getDbCon() );
         
      }

      $user = new Api_Dao_User();
      $user->setUsername($this->user_name);
      $user->setPassword($this->password);

      $response = array();
      if(!$user->initByUserName($this->user_name)) {
         $user->insertIntoDb($this->getDbCon());
         $response['user'] = array();
         $response['user']['id'] = $user->getId();
      } else {
         throw new Exception("User $this->user_name already exists!");
      }

      return $response;
   }
}

?>