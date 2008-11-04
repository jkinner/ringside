<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideGroup extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('groups');
		$this->hasColumn('gid', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
		$this->hasColumn('name', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('nid', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('description', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('group_type', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('group_subtype', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('recent_news', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('office', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('website', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('email', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('street', 'string', 255, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(255)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('city', 'string', 255, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(255)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('show_related', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('discussion_board', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('wall', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('photos', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('photos_all', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('posted_items', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('posted_items_all', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('access_type', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('publicize', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('video', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('video_all', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('image', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('creator', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('state', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('country', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('pic_small', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('pic_big', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('latitude', 'float', null, array('alltypes' => array(0 => 'float'), 'ntype' => 'double', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('longitude', 'float', null, array('alltypes' => array(0 => 'float'), 'ntype' => 'double', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
	}

	public function setUp()
	{
		parent::setUp();
		$this->hasMany('RingsideGroupsMember', array('local' => 'gid', 'foreign' => 'gid'));
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