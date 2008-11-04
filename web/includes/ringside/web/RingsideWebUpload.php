<?php

include_once 'ringside/api/clients/RingsideApiClientsRest.php';
/**
 * Handle file uploads and offer them over to the Image caching server.
 */
class RingsideWebUpload {

   /**
    * Call the photo upload service, store the image and return the new URL.
    *
    * The associative array passed in must contain same parameters expected in a $_FILES entry.
    *   name
    *   type (mime type)
    *   size
    *   tmp_name
    *
    * @param string $upload entry in _FILES
    * @return mixed  false if the cacheUpload failed, REFERENCE to url if it worked. 
    */
   public static function cacheUpload( RingsideApiClientsRest $restClient, $upload ) {

      // Configuration - Your Options
      $allowed_filetypes = array('.jpg','.gif','.bmp','.png'); // These will be the types of file that will pass the validation.
      $max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).

      $filename = $_FILES[$upload]['name']; // Get the name of the file (including file extension).
      $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.

      // Check if the filetype is allowed, if not DIE and inform the user.
      if(!in_array($ext,$allowed_filetypes)) { 
         error_log( "Not proper extenstion ( $filename ) ");
         return false;
      }

      // Just an extra check for images.
      try {
         $result = getimagesize( $_FILES[$upload]['tmp_name'] );
         if ( $result === false ) {
            error_log( "Not really an image is it. ( $filename ) ");
            return false;
         }
      } catch ( Exception $e ){
         return false;
      }

      // Now check the filesize, if it is too large then DIE and inform the user.
      if(filesize($_FILES[$upload]['tmp_name']) > $max_filesize)  { 
         error_log( "File size greater than limits . ( $filename ) ");
         return false;
      }
       
      // Call the photo upload API on the server.
      try {
          $result = $restClient->move_upload( $_FILES[$upload]['tmp_name'], $filename );
          return $result;
          
      } catch ( Exception $e )  {
         error_log ( "EXCEPTION loading photo " . $e );
         return false;
      }
       
   }
}

?>