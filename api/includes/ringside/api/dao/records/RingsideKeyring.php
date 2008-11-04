<?php

include_once 'ringside/api/dao/records/RingsideDomain.php';

class RingsideKeyring extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('keyrings');
		$this->hasColumn('entity_id', 'integer');
		$this->hasColumn('domain_id', 'integer');
		$this->hasColumn('api_key', 'string', 32, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(32)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('secret', 'string', 32, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(32)', 'fixed' => false, 'values' => array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->hasOne('RingsideApp', array('local' => 'entity_id', 'foreign' => 'id'));
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

?>