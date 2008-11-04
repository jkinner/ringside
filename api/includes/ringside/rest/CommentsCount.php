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

require_once ("ringside/api/DefaultRest.php");
require_once ('ringside/api/bo/Comments.php');

/**
 * Get the count of comments for a given thread id. 
 *
 * ** Hash Param List **
 * XID - The comments unique ID
 * AID - The Application to act upon as an override to the application in context.
 * The application caller must be a default app for this to work.
 *
 * ** Formal Parameters **
 * UID in context
 * SESSION context
 *
 * @author Richard Friedman rfriedman@ringsidenetworks.com
 */
class CommentsCount extends Api_DefaultRest
{
	private $xid;
	private $aid;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		$this->xid = $this->getRequiredApiParam('xid');
		$this->aid = $this->getApiParam('aid', $this->getAppId());
	
	}

	/**
	 * Get the count of comments for a given thread.
	 *
	 * @return count of comments for a thread
	 */
	public function execute()
	{
		$this->checkDefaultApp($this->aid);
		
		$response = array();
		
		$count = Api_Bo_Comments::getCommentCount($this->xid, $this->aid);
		
		$response['result'] = "$count";
		
		return $response;
	}
}
?>
