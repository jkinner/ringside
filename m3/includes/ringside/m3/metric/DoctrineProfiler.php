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

Doctrine::autoload('Doctrine_Connection_Profiler');
require_once('ringside/m3/util/File.php');
require_once('ringside/m3/util/Settings.php');

/**
 * An object that receives Doctrine events and stores the doctrine statistics to a file
 * for later aggregation and analysis.
 *
 * This follows the interface for Doctrine's event listener framework, not M3's.
 * As of Doctrine v0.10, these are the names of events this listener will receive:
 * 
 * query
 * exec
 * prepare
 * connect
 * close
 * error
 * execute
 * fetch
 * fetch all
 * begin
 * commit
 * rollback
 * create savepoint
 * rollback savepoint
 * commit savepoint
 * delete record
 * save record
 * update record
 * insert record
 * serialize record
 * unserialize record
 * 
 * @author John Mazzitelli
 */
class M3_Metric_DoctrineProfiler extends Doctrine_Connection_Profiler
{
    private $dataDirectory;
    private $md5FilesCreated;

    /**
     * Creates a doctrine profiler.
     */
    public function __construct()
    {
        parent::__construct();
        $this->dataDirectory = M3_Util_Settings::getDataDirectory() . '/doctrine-profiler';
        $this->md5FilesCreated = array();
    }

    public function __destruct()
    {
        $_metrics = array();
        
        foreach ($this as $event)
        {
            $_md5 = $this->createQueryMD5File($event->getQuery());
            $_eventName = $event->getName();
            $_eventData = $event->getElapsedSecs() . ((!empty($_md5)) ? ('|' . $_md5) :  '');
            @$_metrics[$_eventName] .= $_eventData . "\n";
        }

        foreach ($_metrics as $_name => $_data)
        {
            $_file = M3_Util_File::buildPathName($this->dataDirectory, $_name . '-metrics.dat');
            
            // TODO: figure a better way - at least make this configurable
            //       for now, don't blow up our file system, purge the data if its too big
            if (filesize($_file) > 10000000)
            {
                M3_Util_File::truncateFile($_file);
            }

            M3_Util_File::lockAndAppendFile($_file, $_data);
        }

        return;
    }

    /**
     * We only store MD5 hashs of queries in the metrics file so limit the size of that file.
     * We store the real query string in a file whose name is the MD5 of the query.
     * This creates that MD5 file if it does not yet exist.
     * 
     * @return this returns the MD5 of the query, or "" if $query is empty
     */
    private function createQueryMD5File($query)
    {
        if (empty($query))
        {
            return "";
        }

        $_md5 = md5($query);
        
        // first see if we haven't seen this query yet 
        if (!isset($this->md5FilesCreated[$_md5]))
        {
            // now see if the file isn't created yet
            $_pathname = M3_Util_File::buildPathName($this->dataDirectory, $_md5 . ".md5");
            if (!file_exists($_pathname))
            {
                M3_Util_File::lockAndAppendFile($_pathname, $query);
            }
            $this->md5FilesCreated[$_md5] = true;
        }

        return $_md5;
    }
}

?>