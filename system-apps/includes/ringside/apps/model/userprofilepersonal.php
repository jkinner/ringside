<?php
/**
 *	Complete.
*/
class Userprofilepersonal extends ActiveRecord {   
	public $table_name ="users_profile_personal";
	public $primary_keys = array("user_id");
	public $index_on = "user_id"; 
	public $belongs_to = "User"; 
    function __get($key) {
		if($key=="id"){
			return $this->user_id;
		}
		return ActiveRecord::__get($key);
	}	   
}

?>