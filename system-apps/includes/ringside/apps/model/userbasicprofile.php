<?php
/**
 *	Complete.
*/
class Userbasicprofile extends ActiveRecord {      
	public $table_name ="users_profile_basic";
	public $primary_keys = array("user_id");
	public $index_on = "user_id"; 
	public $belongs_to = "user"; 
	
}

?>