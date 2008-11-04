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

require_once ('ringside/m3/event/IDispatcher.php');
require_once ('ringside/m3/event/IListener.php');
require_once ('ringside/m3/event/IEvent.php');

/**
 * A simple implementation that maintains the list of listeners and will dispatch events to those listeners.
 * You can use this as a superclass to any specific, specialized dispatchers. 
 */
class M3_Event_SimpleDispatcher implements M3_Event_IDispatcher
{
    private $listeners;

    public function __construct()
    {
        $listeners = array();
    }

    public function addListener(M3_Event_IListener& $rListener)
    {
        if (!empty($rListener))
        {
            $_hash = spl_object_hash($rListener);
            $this->listeners[$_hash] = $rListener;
        }
    }

    public function removeListener(M3_Event_IListener& $rListener)
    {
        if (!empty($rListener))
        {
            $_hash = spl_object_hash($rListener);
            if (isset($this->listeners[$_hash]))
            {
                unset($this->listeners[$_hash]);
            }
        }
    }
    
    public function removeAllListeners()
    {
        $this->listeners = array();
    }
    
    public function getAllListeners()
    {
        return $this->listeners;
    }

    public function dispatchEvent(M3_Event_IEvent $event)
    {
        foreach ($this->listeners as $listener)
        {
            $listener->eventTriggered($event);
        }
    }
}
?>