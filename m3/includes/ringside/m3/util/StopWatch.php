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
 * A simple stop watch that you can start and stop. Call getTime() to return
 * the epoch seconds the stopwatch has counted.
 * 
 * @author John Mazzitelli
 */
class M3_Util_StopWatch
{
    private $runningDuration; // total duration of time that the stop watch has been in the started state
    private $timeWhenStarted; // the point in time when the stopwatch was started
    private $started;
    private $stopped;
    
    public function __construct()
    {
        $this->reset();
    }
   
    /**
     * Starts the stop watch.  If the stop watch is already started, this does nothing.
     */
    public function start()
    {
        if (!$this->started)
        {
            $this->timeWhenStarted = microtime(true);
            $this->started = true;
            $this->stopped = false;
        }

        return;        
    }

    /**
     * Stops the stopwatch.  If the stop watch was already stopped, this does nothing.
     * If the stopwatch was started, this effectively pauses the internal timer/counter.
     * You can re-start the stop watch to continue where it left off.
     */
    public function stop()
    {
        if ($this->stopped)
        {
            return; // we're already stopped - no need to do anything
        }

        if ($this->started)
        {
            $this->runningDuration += microtime(true) - $this->timeWhenStarted;
            $this->timeWhenStarted = 0.0;
            $this->started = false;
            $this->stopped = true;
        }
        
        return;
    }
   
    /**
     * Clears the stop watch of all internal state.  The time is reset to 0.
     */
    public function reset()
    {
        // set both started and stopped to false, that indicates the stopwatch hasn't been used at all yet
        $this->runningDuration = 0.0;
        $this->timeWhenStarted = 0.0;
        $this->started = false;
        $this->stopped = false;
    }
   
    /**
     * Returns true if the stop watch has been started.
     * 
     * @return boolean true if started, false if it is has been stopped (i.e. paused) or if it has never been started at all
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Returns the time, in epoch seconds, that the stopwatch has counted.
     * More technically, it's the time the stopwatch has been in the "started" state.
     * The returned float gives you time with microsecond granularity (assuming your
     * operating system supports that granularity).
     *
     * @return float time the watch has counted in epoch seconds
     * 
     * @see isStarted()
     */
    public function getTime()
    {
        // if the stopwatch hasn't been used at all yet, immediately return 0
        if (!$this->started && !$this->stopped)
        {
            return 0.0;
        }

        if ($this->started)
        {            
            $_value = $this->runningDuration + (microtime(true) - $this->timeWhenStarted);
        }
        else
        {
            $_value = $this->runningDuration;
        }

        return $_value;
    }
}
?>