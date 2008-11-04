<?php

class d51PearPkg2Task_Dependencies_PEAR
{
    private $_minimum_version = false;
    private $_maximum_version = false;
    private $_recommended_version = false;
    private $_exclude_versions = false;
    
    public function __construct()
    {
        
    }
    
    public function __get($key)
    {
        static $valid = array(
            'minimum_version',
            'maximum_version',
            'recommended_version',
            'exclude_version',
        );
        
        if (in_array($key, $valid)) {
            $real_key = '_' . $key;
            return $this->$real_key;
        }
    }

    public function setMinimum_version($minimum_version)
    {
        $this->_minimum_version = $minimum_version;
    }
    
    public function setMaximum_version($maximum_version)
    {
        $this->_maximum_version = $maximum_version;
    }
    
    public function setRecommended_version($recommended_version)
    {
        $this->_recommended_version = $recommended_version;
    }
    
    public function setExclude_versions($exclude_version)
    {
        $this->_exclude_versions = explode(',', $exclude_version);
    }
} 