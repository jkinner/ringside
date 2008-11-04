<?php

class Status extends ActiveRecord {
	public $table_name ="status";
	public $primary_keys = array("uid");
	public $index_on = "uid"; 
		
}


?>