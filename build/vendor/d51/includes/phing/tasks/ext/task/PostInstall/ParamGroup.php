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

require_once 'phing/tasks/ext/task/PostInstall/Param.php';

class d51PearPkg2Task_PostInstall_ParamGroup
{
    private $_name = '';
    private $_instructions = '';
    private $_params = array();
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'name' :
                return $this->_name;
            
            case 'description' :
                return $this->_description;
            
            case 'params' :
                return $this->_params;
        }
    }
    
    public function createParam()
    {
        $param = new d51PearPkg2Task_PostInstall_Param();
        $this->_params[] = $param;
        return $param;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function setInstructions($instructions)
    {
        $this->_instructions = $instructions;
    }
    
} 
