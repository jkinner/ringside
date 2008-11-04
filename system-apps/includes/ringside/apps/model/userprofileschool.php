<?php
/**
 *	Complete.
*/
class Userprofileschool extends ActiveRecord {      
	public $table_name ="users_profile_school";
	public $primary_keys = array("id");
	public $index_on = "id"; 
	public $belongs_to = "user"; 
	public $has_one = array(
		'school' => array('foreign_key' => 'school_id','order' => 'school_id'));
}

?>