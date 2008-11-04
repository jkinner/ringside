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


require_once 'Salty/PEAR/Server/RemoteReleaseDeployer.php';

/**
 * Task wrapper for salty automated deployment to 
 * Chiara Server. 
 */
class SaltyTask extends Task {
    protected $taskName = 'salty';
    
    private $_uri = null;
    private $_admin = null;
    private $_username = null;
    private $_password = null;
    private $_file = null;
    
    /**
     * Handles initialization of this task
     */
    public function init()
    {
    }
    
    /**
     * Main entry point for task
     */
    public function main()
    {
       $d = new Salty_PEAR_Server_RemoteReleaseDeployer();

       $d->adminuri = $this->_uri;
       $d->adminpage = $this->_admin;
       $d->username = $this->_username;
       $d->password = $this->_password;

       if ($d->deployRelease( $this->_file )) {
          echo 'Success!';
       } else {
          echo 'Something went terribly wrong.';
       }
       
    }
    
    public function setUri($value) {
        $this->_uri = $value;
    }
    
    public function setAdmin($value) {
        $this->_admin = $value;
    }
    
    public function setUsername($value) {
        $this->_username = $value;
    }
    
    public function setPassword($value) {
        $this->_password = $value;
    }
    
    public function setFile($value) {
        $this->_file = $value;
    }
    
}
