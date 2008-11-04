<?php

class Userprofilework extends ActiveRecord {      
	public $table_name ="users_profile_work";
	public $primary_keys = array("id");
	public $index_on = "id"; 
	public $belongs_to = "user"; 
}

?>