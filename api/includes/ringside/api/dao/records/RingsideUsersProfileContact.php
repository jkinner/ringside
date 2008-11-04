<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideUsersProfileContact extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('users_profile_contact');
		$this->hasColumn('user_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('home_phone', 'string', 20, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(20)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('mobile_phone', 'string', 20, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(20)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('address', 'string', 200, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(200)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('city', 'string', 100, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(100)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('state', 'string', 100, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(100)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('country', 'string', 100, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(100)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('zip', 'string', 15, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(15)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('website', 'string', 500, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(500)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('is_hometown', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('is_current', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
	}

	public function setUp()
	{
		parent::setUp();
		$this->actAs('Timestampable', array('created' =>  array('name'    =>  'created',
                                                                'type'    =>  'timestamp',
                                                                'format'  =>  'Y-m-d H:i:s',
                                                                'disabled' => false,
                                                                'options' =>  array()),
                                            'updated' =>  array('name'    =>  'modified',
                                                                'type'    =>  'timestamp',
                                                                'format'  =>  'Y-m-d H:i:s',
                                                                'disabled' => false,
                                                                'options' =>  array())));
	}

}