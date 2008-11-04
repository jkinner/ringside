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

require_once ('ringside/m3/event/IEvent.php');

/**
 * This is the main event superclass implementation that all other events can
 * subclass. 
 * 
 * @author John Mazzitelli
 */
class M3_Event_SimpleEvent implements M3_Event_IEvent
{
    private $kind;
    private $occurred;
    
    /**
     * Creates the event.
     * 
     * @param $kind the type of response time this event represents
     * @param $when indicates when the event occurred; if not specified, the current time is used
     */
    public function __construct($kind, $when = null)
    {
        $this->kind = empty($kind) ? "unknown" : $kind;
        $this->occurred = empty($when) ? microtime(true) : $when;
    }
    
    public function getKind()     { return $this->kind; }
    public function getOccurred() { return $this->occurred; }
}
?>