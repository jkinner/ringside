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
 * An enumeration that defines the types of datastores M3 can use at its
 * backing store for data. Call the static methods to create instances
 * of this enum.
 * 
 * @author John Mazzitelli
 */
class M3_Util_DatastoreEnum
{
    // today, we only support the DB enum - envision a "memory" type later
    const _DB = 'db';
    
    private $enumValue;

    public static function DB()   { return self::create(self::_DB); }

    public function isDB()   { return $this->enumValue === self::_DB; }

    /**
     * Returns an array of all valid enum values.
     * 
     * @return array of all enum string values that are valid
     */
    public static function getEnums()
    {
        return array(0 => self::_DB);
    }
    
    /**
     * Creates an enum instance given a string representation of the enum.
     * 
     * @param $value the string representation of the enum to create
     * 
     * @return M3_Util_DatastoreEnum that represents the given value string
     */
    public static function create($value)
    {
        $enums = self::getEnums();
        foreach ($enums as $e)
        {
            if ($value === $e)
            {
                return new M3_Util_DatastoreEnum($e);
            }
        }
        
        $_defaultValue = self::_DB;
        error_log("Invalid enum string [$value], defaulting to [$_defaultValue]");

        return new M3_Util_DatastoreEnum($_defaultValue);
    }

    /**
     * Returns the string value of this enum instance.
     */
    public function getValue()
    {
        return $this->enumValue;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    private function __construct($value)
    {
        $this->enumValue = $value;
    }
}
?>