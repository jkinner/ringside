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
 * Reads one or more context parameter lists from the input associative array, typically
 * $_GET, $_POST, $_COOKIE, or $_REQUEST. Each context that is read out of the original array
 * "claims" the parameter from the original array. The remaining context parameters can
 * be read by requesting the original context name.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

class ContextParameterReader {
	private $mainContext;
	private $params;
	private $availableParameters;
	private $contexts;
	
	public function __construct(array $params, $name = 'fb_sig') {
		$this->params = $params;
		$this->mainContext = $name;
		$availableParameters = $this->findContextParameters($params, $name);
		$this->availableParameters = $availableParameters;
	}
	
	public function findContextParameters($params, $name)
	{
		$availableParameters = array();
		foreach ( $params as $key => $value )
		{
			// Do NOT include the context name itself (e.g. fb_sig), it belongs to the "parent" context
			if ( 0 === strpos($key, $name.'_') )
			{
				 array_push($availableParameters, $key);
			}
		}
		
		return $availableParameters;
	}
	
	public function getMainContextName() {
		return $this->mainContext;
	}
	
	public function getContext($name = null)
	{
		if ( null === $name )
		{
			$name = $this->mainContext;
		}
		
		if ( isset($contexts[$name]) )
		{
			return $contexts[$name];
		}
		else
		{
			if ( $name === $this->mainContext )
			{
				$result = array();
				foreach ( $this->availableParameters as $availableParameter )
				{
					$availableParameterName = substr($availableParameter, strlen($name)+1);
					$result[$availableParameterName] = $this->params[$availableParameter];
				}
				
				return $result;
			}
			else
			{
				$result = array();
				$contextParameters = $this->findContextParameters($this->params, $name);
				foreach ( $contextParameters as $contextParameter ) {
					// Trim the context prefix from the parameter name
					$contextParameterName = substr($contextParameter, strlen($name)+1);
					$result[$contextParameterName] = $this->params[$contextParameter];
				}
				$this->contexts[$name] = $result;
				$this->availableParameters = array_diff($this->availableParameters, $contextParameters);
				
				return $result;
			}
		}
	}
}
?>