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

class Communities extends Ringside_Controller
{
  
  public function __construct($action=null)
  {
    parent::__construct($action,$this);
  }
  
  public function index()
  {
  //print url_for();
  
   $this->layout = "apps";
   
   if(isset($_GET['partial']))
   {
     $this->require_login = false;
     $partial = filter_var($_GET['partial'], FILTER_SANITIZE_STRING);
     $this->partial = $partial;
   }
  }
  
  public function show()
  {
    
  }
  
  public function create()
  {
    
  }
  
  public function edit()
  {
    
  }
  
  public function update()
  {
    
  }
  
  public function destroy()
  {
    
  }
  
  public function feed()
  {
    $this->renderPartial("feed");
  }

}




?>