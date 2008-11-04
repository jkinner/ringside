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

require_once( 'ringside/api/DefaultRest.php' );
require_once( 'ringside/api/bo/Comments.php' );

/**
 * Get all the comments (maxed by size) for a given XID.
 *
 * ** Hash Param List **
 * XID - The comments unique ID
 *
 * AID - The Application to act upon as an override to the application in context.
 * The application caller must be a default app for this to work.
 * 
 * FIRST - The starting point of the return set.
 * If there are 25 comments in a thread they are sorted by CID in
 * descending order ( should be same as descending date).   The first
 * 10 results would be first - 0 , count 10.   Second 10 would be
 * first - 10, count 10. And so on.
 *
 * COUNT - the number of rows to return
 *
 * ** Formal Parameters **
 * UID in context
 * SESSION context
 *
 * @author Richard Friedman rfriedman@ringsidenetworks.com
 */
class CommentsGet extends Api_DefaultRest
{
   private $xid;
   private $aid;

   private $first = null;
   private $count = null;
   private $defaultCount = 10;
    
   /**
    * Validate Request.
    */
   public function validateRequest( ) {
      
      $this->xid = $this->getRequiredApiParam('xid');

      $first = $this->getApiParam( 'first' );
      if ( $first != null ) {
         $this->first = (integer) $first;
         $this->count = (integer) $this->getApiParam('count', $this->first + $this->defaultCount );
      } else {
         $this->first = 0;
         $this->count = $this->defaultCount;
      }

      $this->aid = $this->getApiParam('aid', $this->getAppId() );

   }

   /**
    * Reads comments from a thread
    *
    * @return array of comments [ cid, timestamp, uid ]
    */
   public function execute() {

      $this->checkDefaultApp( $this->aid );
      
      $response = array();

      $result = Api_Bo_Comments::getComments($this->xid, $this->aid, $this->first, $this->count);
      
      if ( empty( $result ) ) {
      	$response['result'] = '';
      } else {      	        
         $response['comments'] = $result;
      }
      return $response;
   }
}
?>
