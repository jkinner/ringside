<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideM3MeasApiCall extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('m3_meas_api_call');
		$this->hasColumn('id', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
		$this->hasColumn('nid', 'string', 255, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(255)', 'fixed' => false, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('aid', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('uid', 'integer', 4, array('alltypes' => array(0 => 'integer'), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' => array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
		$this->hasColumn('api_name', 'string', 64, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(64)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('duration', 'float', null, array('alltypes' => array(0 => 'float'), 'ntype' => 'double', 'unsigned' => 0, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('created', 'timestamp', null, array('alltypes' => array(0 => 'timestamp'), 'ntype' => 'timestamp', 'values' => array(), 'primary' => false, 'default' => 'CURRENT_TIMESTAMP', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('modified', 'timestamp', null, array('alltypes' => array(0 => 'timestamp'), 'ntype' => 'timestamp', 'values' => array(), 'primary' => false, 'default' => '0000-00-00 00:00:00', 'notnull' => true, 'autoincrement' => false));
	}

	public function setUp()
	{
		parent::setUp();
		$this->actAs('Timestampable', array('created' => array('name' => 'created', 'type' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'disabled' => false, 'options' => array()), 'updated' => array('name' => 'modified', 'type' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'disabled' => false, 'options' => array())));
	}

}