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

require_once ('ringside/phing/TextContainer.php');

class DSN {

    private $_type = null;
    private $_server = null;
    private $_database = null;
    private $_username = null;
    private $_password = '';
   
    public function createType() {
       $value = new TextContainer();
       $this->_type = $value;
       return $value;
    }
        
    public function createServer() {
       $value = new TextContainer();
       $this->_server = $value;
       return $value;
    }
        
    public function createDatabase() {
       $value = new TextContainer();
       $this->_database = $value;
       return $value;
    }
    
    public function createUsername() {
       $value = new TextContainer();
       $this->_username = $value;
       return $value;
    }
    
    public function createPassword() {
       $value = new TextContainer();
       $this->_password = $value;
       return $value;
    }

    public function __get($key)
    {
        static $valid = array(
            'type',
            'database',
            'server',
            'username',
            'password'
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key->_toString();
        }
    }    

    private function check( $value, $message ) 
    {
       if ( empty ( $value ) ) 
       {
          throw new Exception( $message . " not configured " );
       }
    }
        
    public function _toString() 
    {
       
       $this->check( $this->_type , "Type of database (mysql|pgsql)");
       $this->check( $this->_database, "Database");
       $this->check( $this->_server, "Server");
       $this->check( $this->_username, "Username");

       $dsn = $this->_type . 
            "://" . $this->_username . 
            ":" . $this->_password . 
            "@" . $this->_server . 
            "/" . $this->_database;
       return $dsn;
    }
}
