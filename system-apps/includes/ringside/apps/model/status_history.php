<?php

class StatusHistory extends ActiveRecord {
	public $table_name ="status_history";
	public $primary_keys = array("id");
	public $index_on = "id"; 
}


?>