<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideFeed extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('feed');
		$this->hasColumn('feed_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
		$this->hasColumn('type', 'integer', 2, array('alltypes' => array(0 => 'integer'), 'ntype' => 'smallint(5) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('templatized', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('title', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('title_data', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('body', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('body_data', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('body_general', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_1', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_1_link', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_2', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_2_link', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_3', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_3_link', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_4', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('image_4_link', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('priority', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('author_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('actor_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('target_ids', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
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