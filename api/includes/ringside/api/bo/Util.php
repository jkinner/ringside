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
 * Static utilities that business objects and their clients may want to use.
 *
 * @author John Mazzitelli
 */

class Api_Bo_Util
{
    /**
     * Returns a string in the form "YYYY-MM-dd HH:mm:ss" that represents the current time.
     * 
     * @return string a timestamp in the form "YYYY-MM-dd HH:mm:ss" in the local time zone
     */
    public static function getCurrentTimestamp()
    {
        return self::getTimestamp(time());
    }

    /**
     * Returns a string in the form "YYYY-MM-dd HH:mm:ss" that represents the given number of seconds in the past.
     *
     * @param int $secondsPast the number of seconds in the past whose time is to be returned
     *  
     * @return string a timestamp in the form "YYYY-MM-dd HH:mm:ss" in the local time zone
     */
    public static function getPastTimestamp($secondsPast)
    {
        return self::getTimestamp(time() - $secondsPast);
    }

    /**
     * Returns a string in the form "YYYY-MM-dd HH:mm:ss" that represents the given epoch seconds.
     * 
     * @param int $time the time in epoch seconds
     *
     * @return string a timestamp in the form "YYYY-MM-dd HH:mm:ss" in the local time zone
     */
    public static function getTimestamp($time)
    {
        return strftime('%Y-%m-%d %H:%M:%S',$time);
    }

    /**
     * Converts a Collection of Doctrine objects into an array.
     *
     * @param $collection the doctrine objects in a collection
     *
     * @return array a plain array containing the objects
     */
    public static function convertCollectionToArray($collection, $deep = false)
    {
        $count = count($collection);
        if($count == 0)
        {
            return array();
        }
        else if($count == 1)
        {
            return $collection[0]->toArray($deep);
        }
        else
        {
            return $collection->toArray($deep);
        }
    }

    /**
     * This treats even one result as a list and return something like
     * array(1) {[0]=>array(17) {}};
     *
     * @param $collection
     * @param boolean $deep
     *
     * @return 
     */
    public static function convertCollectionAsListToArray($collection, $deep = false)
    {
        $count = count($collection);
        if($count == 0)
        {
            return array();
        }
        else if($count > 0)
        {
            return $collection->toArray($deep);
        }
    }
}
?>