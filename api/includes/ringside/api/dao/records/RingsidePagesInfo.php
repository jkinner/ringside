<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsidePagesInfo extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('pages_info');
		$this->hasColumn('page_id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('name', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('value', 'string', 1024, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(1024)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('json_encoded', 'integer', 1, array('alltypes' => array(0 => 'integer', 1 => 'boolean'), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '0', 'notnull' => true, 'autoincrement' => false));
	}

	public function setUp()
	{
		parent::setUp();
		$this->hasOne('RingsidePage', array('local' => 'page_id', 'foreign' => 'page_id'));
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