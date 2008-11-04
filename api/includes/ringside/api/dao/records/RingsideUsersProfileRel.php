<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideUsersProfileRel extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('users_profile_rel');
		$this->hasColumn('id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
		$this->hasColumn('user_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('status', 'enum', 23, array('alltypes' => array(0 => 'enum', 1 => 'integer'), 'ntype' => 'enum(\'Single\',\'In a Relationship\',\'Engaged\',\'Married\',\'It\'\'s Complicated\',\'In an Open Relationship\')', 'fixed' => false, 'values' => array(0 => 'Single', 1 => 'In a Relationship', 2 => 'Engaged', 3 => 'Married', 4 => 'It\'s Complicated', 5 => 'In an Open Relationship'), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('alternate_name', 'string', 100, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(100)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('significant_other', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('meeting_for', 'string', 200, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(200)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('meeting_sex', 'string', 3, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(3)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
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