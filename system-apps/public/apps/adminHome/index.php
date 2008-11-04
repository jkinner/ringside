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
 require_once("LocalSettings.php");
 error_reporting(E_ALL);

 require_once "app/controllers/application_controller.php";
 require_once "app/controllers/ringside_controller.php";
 
 require_once "app/controllers/apps.php";
 require_once "app/controllers/communities.php";
 require_once "app/controllers/welcome.php";
  
  // Automatically find controllers
  // spl_autoload_register('admin_autoload');
  // function admin_autoload($class_name)
  // {
  //   $_path = "app/controllers/" . strtolower($class_name) . '.php';
  //   if(file_exists($_path))
  //     require_once $_path;
  // }

  //require_once "app/controllers/welcome.php";
  if (isset($_GET['apps']))            // show apps page
  {
    $action = new Apps();
  } 
  elseif (isset($_GET['communities'])) // show communities page
  {
    $action = new Communities();
  }
  else                                 // show welcome page
  {
    $action = new Welcome();
  }
  
  Application_Controller::$controllers = array("Apps","Communities","Welcome");
  
  //$action->setLocalClient( true );
  //$user = $action->client->require_login();

$action->render();
?>

<br><br>

<?php //print_r(Application_Controller::$controllers); ?>