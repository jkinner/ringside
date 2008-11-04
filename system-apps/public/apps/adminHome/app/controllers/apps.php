<?php
//require_once "ringside_controller.php";

class Apps extends Ringside_Controller
{
  
  
  public function __construct($action=null)
  {
    parent::__construct($action,$this);
  }
  
  public function index()
  {
    if(isset($_GET['partial']))
     {
       $this->require_login = false;
       $partial = filter_var($_GET['partial'], FILTER_SANITIZE_STRING);
       $this->partial = $partial;
     }
  }


}




?>