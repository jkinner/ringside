<?php
/**
 *	Complete.
*/
class School extends ActiveRecord {      
	public $table_name ="schools";
	public $primary_keys = array("id");
	public $index_on = "id"; 
	public $belongs_to = "usersprofileschool"; 
}

?>