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

class d51PearPkg2Task_GlobalReplacement extends ProjectComponent
{
    private $_type = null;
    private $_from = null;
    private $_to = null;
    
    public function __construct()
    {
        
    }
    
    public function setPath($path)
    {
        $this->_path = $path;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function setFrom($from)
    {
        $this->_from = $from;
    }
    
    public function setTo($to)
    {
        $this->_to = $to;
    }
    
    public function isValid()
    {
        $failed = array();
        $required_attributes = array( 'type', 'from', 'to');
        foreach ($required_attributes as $required_attribute) {
            $real_key = "_{$required_attribute}";
            if (is_null($this->$real_key)) {
                $this->log("{$required_attribute} attribute not set");
                $failed[] = $required_attribute;
            }
        }
        
        if (count($failed) > 0) {
            throw new d51PearPkg2Task_Replacement_MissingAttributeException(
                implode(', ', $failed) . ' attributes not set'
            );
        }
    }
    
    public function __get($key)
    {
        switch ($key) {
            case 'type' :
            case 'from' :
            case 'to' :
                $key = "_{$key}";
                return $this->$key;
        }
    }
    
    public function log($message)
    {
        parent::log('[d51pearpkg2-replacement] ' . $message);
    }
}

class d51PearPkg2Task_GlobalReplacement_MissingAttributeException extends Exception { } 
