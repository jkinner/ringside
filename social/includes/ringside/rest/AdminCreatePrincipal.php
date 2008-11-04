<?php
 /*
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
  */

/**
 * Creates a new principal after confirming the user want's a new principal.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
require_once('ringside/api/DefaultRest.php');
require_once('ringside/social/IdMappingService.php');

class AdminCreatePrincipal extends Api_DefaultRest {
	private $uid;
	private $snid;
	private $sid;
	private $aid;
	
	public function validateRequest( ) {
		$this->uid = $this->getUserId();
		$this->snid = $this->getRequiredApiParam('snid');
		$this->sid = $this->getRequiredApiParam('sid');
		$this->aid = $this->getApiParam('aid', $this->getAppId());
	}
	
	public function execute() {
		$mapper = Social_IdMappingService::create();
		error_log("Creating principal for {$this->snid}, {$this->sid} for app {$this->aid}");
		$pid = $mapper->createPrincipal($this->aid, $this->snid, $this->sid);
		return $pid;
	}
}
?>