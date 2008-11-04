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

abstract class Application_Controller
{
  
  // name of the controller extending this abstract class
  public $name = "";
  public $partial = false;
  public $layout = true;
  
  // current action
  private $action = "";
  
  // strings to replace with content in the layout
  public static $ACCEPTABLE_KEYS_FOR_LAYOUT = array("<% yield %>","<% yield%>","<%yield %>","<%yield%>");
  
  // need to know which controllers exist so we can
  // build routes appropriately
  // specify this in the root of the project in the index.php
  public static $controllers = array();
  
  public function __construct($action,$sub_class_object)
  {
    if($action)
      $this->action = $action;
    else
    {
      // first check to see if there are any GET
      // vars that exist in the methods available
      // if a GET var called "show" exists, then
      // render the show method
      $_keys_from_http_get = array_keys($_GET);
      $_library_of_methods = get_class_methods($this);
      $actions_to_render = array_intersect($_keys_from_http_get,$_library_of_methods);
      $actions_to_render = array_merge(array(),$actions_to_render); // have to reset array keys because array_intersect preserves them
      if(count($actions_to_render)>0)
      {
        $this->action = $actions_to_render[0];
      }
      else
      {
        $this->action = "index";
      }
    }
    
    // save the name of the class extending this abstract class
    $this->name = strtolower(get_class($sub_class_object));
  }
  
  public function url_for($controller = null, $action = null, $only_path = true)
  {
    $_path = $_SERVER['SCRIPT_NAME'];
    $_parameters = "?";
    
    if(!$only_path)
    {
      //$_path = "http://" . $_SERVER['HTTP_HOST'] . $_path;
      //$_path = "http://" . $_SERVER['HTTP_HOST'] . "/system-apps/apps/adminHome/";
      $_path = RingsideApiClientsConfig::$socialUrl . "/render.php?method=app&app=adminHome&path=index.php";
    }
    if($controller)
    {
      if(array_search(ucfirst($controller),self::$controllers)!==false)
        $_parameters .= "$controller";
      else
        throw new Exception("$controller Controller doesn't exist");
    }
    if($action)
    {
      if(array_search($action,get_class_methods($this)))
        $_parameters .= "&$action";
      else
        throw new Exception("$action Action doesn't exist");
    }
    
    return $_path.$_parameters;
    
  }
  
  public function render()
  {
    if($this->partial) // render a partial if we have one
    {
      print $this->renderPartial($this->partial);
    }
    else
    {
      print $this->renderPage();
    }
  }
  
  private function renderPage()
  {
    if($this->layout !== false)
    {
      if($this->layout !== true)
        $_layout = $this->getLayout($this->layout);
      else
        $_layout = $this->getLayout($this->name);
      $_content = $this->renderAction($this->action);
      $_view_output = $this->replaceKeyWithContentInLayout(self::$ACCEPTABLE_KEYS_FOR_LAYOUT,$_content,$_layout);
    }
    else
    {
      $_view_output = $this->renderAction($this->action);
    }
    
    // evaluate the current method of the current controller
    // aka the current page we are on
    // we may break out of this renderPage if it is determined
    // that the composition of the object has changed
    $this->evalActionMethod($this->partial, $this->layout);
    
    return $_view_output;
  }
  
  private function renderPartial($name)
  {
    
    $views_dir = dirname(dirname(__FILE__)) . "/views/{$this->name}/";
    $file_path = $views_dir . "_" . $name . ".php";
  
    $output=""; 
    if(file_exists($file_path))
    {
      ob_start();
      include($file_path);
      $output = ob_get_clean();
    }
    else
    {
      ob_get_clean();
      error_log("can't open file $file_path");
      $output = "can't open file $file_path";
    }
    return $output;
  }
  
  private function renderAction($action=null)
  {
    if(!$action)
      $action = $this->action;
    
    $views_dir = dirname(dirname(__FILE__)) . "/views/{$this->name}/";
    error_log($views_dir);
    $file_path = $views_dir . $action . ".php";
    
    return $this->readFileToString($file_path);

  }
  
  private function evalActionMethod($partial,$layout)
  {
    $current_action = $this->action;
    eval($this->$current_action()); // execute the method
    
    // if the composition of the object has changed 
    // (render a layout, or render a partial 
    // was specified in the action, restart the render method)
    if($this->partial!==$partial || $this->layout!==$layout)
    {
      $this->render();
      exit;
    }
  }
  
  private function getLayout($name)
  {
    //print $name;
    $layouts_dir = dirname(dirname(__FILE__)) . "/views/layouts/";
    $file_path = $layouts_dir . $name . ".php";
    return $this->readFileToString($file_path);
  }
  
  private function readFileToString($path)
  {
    $output="";  
    if(file_exists($path))
    {
      ob_start();
      include($path);
      $output = ob_get_clean();
    }
    else
    {
      ob_get_clean();
      error_log("Can't open file $path!");
      $output = "";
    }
    return $output;
  }
  
  private function replaceKeyWithContentInLayout($key,$content,$layout)
  {
    $count = 0;
    $output = str_replace($key, $content, $layout, $count);
    if($count > 0)
    {
      return $output;
    }
    else // if not <% yield %> string exists, just append the content to the bottom
    {
      return $layout . $content;
    }
  }

  
}




?>