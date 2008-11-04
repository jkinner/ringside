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

require_once( "ringside/api/cache/HtdocsProvider.php" );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/api/DefaultRest.php" );

/**
 * Photo upload takes an incoming file and persists it to the
 * img storage mechanism of choice (well not so much choice right now).
 *
 */
class AdminUpload extends Api_DefaultRest {
    
   /** The file to be working upon */
   private $m_upload;
   
   /** Temp File name */
   private $m_tmpFileName;
   
   /** Filename */
   private $m_filename;
   
   public function validateRequest( ) {
      
      $this->m_filename = $this->getApiParam( 'filename', '') ;
      $this->m_tmpFileName = $this->getApiParam( 'tmp_filename', '') ;
      
      if ( isset( $_FILES ) && count ($_FILES ) == 1 ) {
         if ( $this->checkUpload( $_FILES[0] ) === false ) {
            throw new OpenFBAPIException( FB_ERROR_MSG_PHOTO_INVALID . " : checkUpload failed.", FB_ERROR_CODE_PHOTO_INVALID );
         }
         $this->m_upload = $_FILES[0];         
         $this->m_tmpFileName = $_FILES[0]['tmp_name'];
         $this->m_filename = $_FILES[0]['name'];
      } else if (empty ($this->m_tmpFileName) && empty($this->m_filename) ) {
         throw new OpenFBAPIException( FB_ERROR_MSG_PHOTO_INVALID . " : no such file", FB_ERROR_CODE_PHOTO_INVALID );
      }

   }

   public function execute() {

      // TODO some metering about the number of files to upload. 
      
      $htdocs = new HtdocsProvider();      
      $result = $htdocs->setByUpload( $this->m_tmpFileName, $this->getAppId() ,  $this->m_filename );
      if ( $result === false) {
         throw new OpenFBAPIException( FB_ERROR_MSG_PHOTO_INVALID . " : File not uploaded", FB_ERROR_CODE_PHOTO_INVALID );
      }
      
      $reference = $htdocs->getReference(  $this->getAppId(), $this->m_filename );
      $response = array();
      $response['reference'] = $reference;
      
      return $response;
   }

   public function checkUpload( $upload ) {

      // Configuration - Your Options
      $allowed_filetypes = array('.jpg','.gif','.bmp','.png'); // These will be the types of file that will pass the validation.
      $max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).

      $filename = $upload['name']; // Get the name of the file (including file extension).
      $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.

      // Check if the filetype is allowed, if not DIE and inform the user.
      if(!in_array($ext,$allowed_filetypes)) {
         error_log( "Not proper extenstion ($upload) ( $filename ) ");
         return false;
      }

      // Just and extra check for images.
      try {
         $result = getimagesize( $upload['tmp_name'] );
         if ( $result === false ) {
            error_log( "Not really an image is it. ( $filename ) (".$upload['tmp_name'].") ");
            return false;
         }
      } catch ( Exception $e ){
         return false;
      }

      // Now check the filesize, if it is too large then DIE and inform the user.
      if(filesize($upload['tmp_name']) > $max_filesize)  {
         error_log( "File size greater than limits . ( $filename ) (".filesize($upload['tmp_name']).") ");
         return false;
      }

      return $result;
       
   }
    
}
?>
