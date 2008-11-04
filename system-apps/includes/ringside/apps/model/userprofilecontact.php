<?php
/**
 *	Complete.
*/
class Userprofilecontact extends ActiveRecord { 
	public $table_name ="users_profile_contact";
	public $primary_keys = array("user_id");
	public $index_on = "user_id"; 
	public $belongs_to = "user"; 
}

?>