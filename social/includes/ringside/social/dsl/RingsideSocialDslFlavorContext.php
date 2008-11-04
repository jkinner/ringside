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

require_once( 'ringside/social/dsl/TagRegistry.php');

class RingsideSocialDslFlavorContext {   
   var $flavors = array();
   var $do_output = array();
	
   public function isFlavor($flavor) {
   	// This needs an explicit return so tag handlers can return isFlavor('foo') on doStartTag
   	return $this->flavors?(array_search($flavor, $this->flavors)===false?false:true):false;
   }
   
   public function getFlavor() {
   	$size = sizeof($this->flavors);
   	return $size==0?null:$this->flavors[$size-1];
   }

   public function doOutput() {
   	return sizeof($this->do_output)>0?($this->do_output[sizeof($this->do_output)-1]?true:false):true;
   }
   
   public function setOutput($do_output) {
   	$this->do_output[sizeof($this->do_output)-1] = $do_output;
   }
   
   public function startFlavor($flavor) {
   	array_push($this->flavors, $flavor);
   	$trFlavs = Social_Dsl_TagRegistry::$flavors;
   	array_push($this->do_output, isset($trFlavs[$flavor]['start']) ? $trFlavs[$flavor]['start']:true);
   }
   
   public function endFlavor($flavor) {
   	$size = sizeof($this->flavors);
   	if ( $size == 0 ) {
   		error_log( "Severe: Attempted to end flavor '$flavor' but stack is empty" );
   		return;
   	}
   	if ( $this->flavors[$size-1] != $flavor ) {
   		error_log( "Warning: Flavor stack is out of sync when ending flavor $flavor (current flavor is ".$this->getFlavor().")");
   	}
   	array_pop($this->flavors);
   	array_pop($this->do_output);
   	
//   	error_log("Ended flavor $flavor (".sizeof($this->flavors).")");
   }
}
?>
