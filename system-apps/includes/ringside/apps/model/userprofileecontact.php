<?php
/**
 *	Complete.
*/
class Userprofileecontact extends ActiveRecord {     
	public $table_name ="users_profile_econtact";
	public $primary_keys = array("id");
	public $index_on = "id"; 
	public $belongs_to = "users"; 
}

?>