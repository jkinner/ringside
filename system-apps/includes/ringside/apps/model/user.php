<?php
require_once(dirname(__FILE__).'/inflector.php');
require_once(dirname(__FILE__).'/trax_exceptions.php');
require_once(dirname(__FILE__).'/active_record.php');
require_once(dirname(__FILE__).'/user.php');
require_once(dirname(__FILE__).'/userbasicprofile.php');
require_once(dirname(__FILE__).'/userprofilecontact.php');
require_once(dirname(__FILE__).'/userprofileecontact.php');
require_once(dirname(__FILE__).'/userprofilelayout.php');
require_once(dirname(__FILE__).'/userprofilenetwork.php');
require_once(dirname(__FILE__).'/userprofilepersonal.php');
require_once(dirname(__FILE__).'/userprofilerel.php');
require_once(dirname(__FILE__).'/userprofileschool.php');
require_once(dirname(__FILE__).'/userprofilework.php');
require_once(dirname(__FILE__).'/school.php');
require_once(dirname(__FILE__).'/status.php');
require_once(dirname(__FILE__).'/status_history.php');


class User extends ActiveRecord {
	public $has_one = array(
		'userbasicprofile' => array('foreign_key' => 'user_id','order' => 'user_id'),
		'userprofilecontact' => array('foreign_key' => 'user_id','order' => 'user_id'),
		'userprofilelayout' => array('foreign_key' => 'user_id','order' => 'user_id'),
		'userprofilepersonal' => array('foreign_key' => 'user_id','order' => 'user_id'),
		'userprofilerel' => array('foreign_key' => 'user_id','order' => 'user_id')
		);
		
	public $has_many = array(
		'userprofileecontact' => array('foreign_key' => 'user_id'),
		'userprofilenetwork' => array('foreign_key' => 'user_id'),
		'userprofileschool' => array('foreign_key' => 'user_id'),
		'userprofilework' => array('foreign_key' => 'user_id')
		);
}

// Bootstrap Active record with ringside settings
if(!defined("TRAX_ENV")){
	define("TRAX_ENV",'development');
	
	$config=array(
    'phptype'=>'mysql',
    'database' => RingsideApiConfig::$db_name,
    'hostspec' => RingsideApiConfig::$db_server,
   'username' => RingsideApiConfig::$db_username,
    'password' =>RingsideApiConfig::$db_password,
    'persistent' => 'true', 
	);
	

	User::$database_settings=array('development'=>$config);	
}

?>