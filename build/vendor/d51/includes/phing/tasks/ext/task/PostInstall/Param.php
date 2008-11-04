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

/**
 * Create a parameter for a PARAM group for a Post install task.
 * @author Richard Friedman
 */
class d51PearPkg2Task_PostInstall_Param
{
    private $_name = '';
    private $_type = null;
    private $_prompt = '';
    private $_default = null;
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        static $valid = array(
            'name',
            'type',
            'prompt',
            'default'
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key;
        }
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function setPrompt($prompt)
    {
        $this->_prompt = $prompt;
    }
    
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function setDefault($default)
    {
        $this->_default = $default;
    }
    
} 
