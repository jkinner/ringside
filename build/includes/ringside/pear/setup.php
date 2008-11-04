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

require_once 'PEAR/Config.php';

class ringside_pear_setup_postinstall
{
   var $_pkg;
   var $_ui;
   var $_config;
   var $_lastversion;
   var $_registry;

   var $_contents;
   var $_wwwDirectory;

   function init(&$config, &$pkg, $lastversion)
   {
      $this->_config      =& $config;
      $this->_registry    =& $config->getRegistry();
      $this->_ui          =& PEAR_Frontend::singleton();
      $this->_pkg         =& $pkg;
      $this->_lastversion =  $lastversion;

      $this->_wwwDirectory = $this->_config->get('www_dir');
      if ( $this->_wwwDirectory === false || !file_exists($this->_wwwDirectory)) {
         $this->_ui->log("Failed to find a web directory {$this->_wwwDirectory}");
         return false;
      } 
      
      $sfile = $this->_wwwDirectory . DIRECTORY_SEPARATOR . 'LocalSettings.php.sample';      
      $this->contents = file_get_contents($sfile, true);
      if ( $this->contents === false ) {
         $this->_ui->log("Failed to load '$sfile'" );
         return false;
      }
      $this->_ui->log("Loaded '$sfile'");
      return true;
   }

   function run($answers, $phrase)
   {
      switch ($phrase) {
         case 'Database':
            foreach( $answers as $answer=>$value ) {
               if ( $answer == 'RS_DB_PASSWORD'  && $value == 'NONE' ) { 
                  $value = '';
               }
               $this->contents = str_replace( $answer, $value, $this->contents );
            }
            break;
         case 'Urls':
            foreach( $answers as $answer=>$value ) {
               $this->contents = str_replace( $answer, $value, $this->contents );
            }

            $socialKey = substr( sha1( $this->createRandomText(20) . microtime() ) , 0, 8 );
            $this->contents = str_replace ( "socialApiKey = ''", "socialApiKey = '$socialKey'",$this->contents );
            $socialSecret = substr( sha1( $this->createRandomText(20) . microtime() ) , 1, 9 );
            $this->contents = str_replace ( "socialSecretKey = ''", "socialSecretKey = '$socialSecret'",$this->contents );
            $networkKey = substr( sha1( $this->createRandomText(20) . microtime() ) , 2,10 );
            $this->contents = str_replace ( 'RS_NETWORK_KEY', $networkKey, $this->contents );
            
            $sfile = $this->_wwwDirectory . DIRECTORY_SEPARATOR . 'LocalSettings.php';
            $this->_ui->log("Writing file - $sfile");
            $result = file_put_contents($sfile, $this->contents);
            return $result;        
      }
       
      return true;
   }

   function createRandomText( $length ) {

      $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
      $lChars = strlen( $chars );
      srand((double)microtime()*1000000);
      $i = 0;
      $pass = '' ;

      while ($i <= $length ) {
         $num = rand() % $lChars;
         $tmp = substr($chars, $num, 1);
         $pass = $pass . $tmp;
         $i++;
      }

      return $pass;
   }
}
?>
