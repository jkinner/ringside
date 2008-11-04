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
 * A generic object that holds simple statistics.  This object holds
 * an array of the following sets of data:
 *    - count (the number of times something happened)
 *    - min   (the minimum value of something)
 *    - max   (the maximum value of something)
 *    - avg   (the average value of something)
 *
 * This object will recompute those values everytime you add a new value.
 * This object actually can hold an array of these stats, so you can hold
 * tabular data, rather than stats on just one "thing".
 * 
 * Note that this object is an aggregator, it will not store each individual
 * value added to a key's data set.  This object merely aggregates statistics
 * based on values that belong to a key's data set.
 *
 * @author John Mazzitelli
 */
class M3_Util_Stats
{
    const KEY = "key";

    const COUNT   = "count";
    const MIN     = "min";
    const MAX     = "max";
    const AVERAGE = "avg";

    private $allStats;

    /**
     * Builds the stats object, optionally pre-populating it with the given keyed stats.
     * If the $existing array is not null, it will assumed to be an associative array
     * keyed on "keys" (as known by this stats object) with values being
     * also associative arrays, keyed on the constants defined in this 
     * object (e.g. COUNT, AVERAGE, etc).
     * 
     * @param array $existing existing array of stats
     */
    public function __construct($existing = null)
    {
        $this->allStats = ($existing != null) ? $existing : array();
    }

    /**
     * Adds the given value to the data set identified by $key. This
     * will automtically update the stats associated with the key's data set.
     * 
     * @param $key identifies the set of stats the given value is to be added to
     * @param $value the new value to add
     */
    public function addValue($key, $value)
    {
        $_stats =& $this->allStats[$key];
        if (isset($_stats))
        {
            // see if this new value is a new min or new max
            if ($_stats[self::MIN] > $value)
            { 
                $_stats[self::MIN] = $value;
            }

            if ($_stats[self::MAX] < $value)
            { 
                $_stats[self::MAX] = $value;
            }

            // bump up the count by 1 to account for this new value
            $_count = ++$_stats[self::COUNT];
            
            // compute the running average
            $_stats[self::AVERAGE] = ((($_count - 1) * $_stats[self::AVERAGE]) + $value) / $_count;
        }
        else
        {
            // we don't have any stats for this data set yet - create the initial one
            $_stats = array( self::COUNT   => 1,
                             self::MIN     => $value,
                             self::MAX     => $value,
                             self::AVERAGE => $value );
        }

        return;
    }

    /**
     * Returns an array of all the keys stored in this object.
     * Each key has a set of count/min/max/avg stats associated with it.
     * Each key returned in the array can be passed to the rest of the methods
     * in this object to obtain stats associated with that key.
     * 
     * @return array set of keys that this object has stats for
     */
    public function getKeys()
    {
        return array_keys($this->allStats);
    }

    /**
     * Returns the number of values added to the data set associated with the given key.
     * 
     * @param $key identifies the data set whose count is to be returned
     * 
     * @return the number of values added to the key's data set
     */
    public function getCount($key)
    {
        $_stats = $this->getStatsForKey($key);
        return $_stats[self::COUNT];
    }

    /**
     * Returns the minimum value that was added to the data set associated with the given key.
     * 
     * @param $key identifies the data set whose minimum value is to be returned
     * 
     * @return the minimum value that was added to the key's data set
     */
    public function getMin($key)
    {
        $_stats = $this->getStatsForKey($key);
        return $_stats[self::MIN];
    }

    /**
     * Returns the maximum value that was added to the data set associated with the given key.
     * 
     * @param $key identifies the data set whose maximum value is to be returned
     * 
     * @return the maximum value that was added to the key's data set
     */
    public function getMax($key)
    {
        $_stats = $this->getStatsForKey($key);
        return $_stats[self::MAX];
    }

    /**
     * Returns the average value of all values added to the data set associated with the given key.
     * 
     * @param $key identifies the data set whose average value is to be returned
     * 
     * @return the average value of all values that were added to the key's data set
     */
    public function getAverage($key)
    {
        $_stats = $this->getStatsForKey($key);
        return $_stats[self::AVERAGE];
    }

    /**
     * Returns an array of count/min/max/average stats for the given key's data set.
     * 
     * @param $key identifies the data set whose stats are to be returned
     * 
     * @return array associative array containing the count/min/max/avg data
     */
    public function getStatsForKey($key)
    {
        $_stats = $this->allStats[$key];
        if (!isset($_stats))
        {
            $_stats = array( self::COUNT   => 0,
                             self::MIN     => 0,
                             self::MAX     => 0,
                             self::AVERAGE => 0 );
        }
        return $_stats;
    }
    
    /**
     * Returns the stats data in a multi-dimensional associative array.
     * 
     * The returned array is keyed on the data set key names, whose values are
     * associative arrays themselves, with keys to indicate the count/min/max/avg data.
     * 
     * @see COUNT
     * @see MIN
     * @see MAX
     * @see AVERAGE
     */
    public function getStatsArray()
    {
        return $this->allStats;
    }
    
    /**
     * Returns the stats data in a flat, non-associative array.
     * This is useful if you want to send the data to remote clients that do not
     * have access to this class definition.
     * 
     * The returned array is flat - its a list of arrays. But each internal array
     * element is an associative array itself, with key/count/min/max/avg data.
     * 
     * @see COUNT
     * @see MIN
     * @see MAX
     * @see AVERAGE
     * @see KEY
     */
    public function getStatsFlatArray()
    {
        $_flatArray = array();

        foreach ($this->allStats as $_key => $_stats)
        {
            $_flatArray[] = array(self::KEY     => $_key,
                                  self::COUNT   => $_stats[self::COUNT],
                                  self::MIN     => $_stats[self::MIN],
                                  self::MAX     => $_stats[self::MAX],
                                  self::AVERAGE => $_stats[self::AVERAGE]);
        }
        
        return $_flatArray;
    }
    
    /**
     * Converts the given flat array (produced by getStatsFlatArray) to
     * an associative array as if the array was returned by getStatsArray. This is useful
     * because its nice to iterate over an associative array whose keys are keys to the
     * tabular data.
     * 
     * This is static because you do not need the original Stats object to do the conversion;
     * all you need is a flat array and this can convert it to the associative array.
     *
     * @param $flatArray the array to convert
     * 
     * @return array the associative array
     */
    public static function unflattenArray($flatArray)
    {
        $_results = array();
        
        foreach ($flatArray as $_stats)
        {
        	$_results[$_stats[self::KEY]] = array(self::COUNT   => &$_stats[self::COUNT],
                                                  self::MIN     => &$_stats[self::MIN],
                                                  self::MAX     => &$_stats[self::MAX],
                                                  self::AVERAGE => &$_stats[self::AVERAGE]);
        }
        
        return $_results;
    }
}
?>