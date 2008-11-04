<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideUsersProfilePersonal extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('users_profile_personal');
		$this->hasColumn('user_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('activities', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('interests', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('music', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('tv', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('movies', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('books', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('quotes', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('about', 'string', null, array('alltypes' => array(0 => 'string', 1 => 'clob'), 'ntype' => 'text', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
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