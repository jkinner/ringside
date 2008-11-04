<?php

include_once 'ringside/api/dao/records/RingsideKeyring.php';

class RingsideDomain extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('domains');
		$this->hasColumn('url', 'string', 255);
		$this->hasColumn('name', 'string', 255);
		$this->hasColumn('resize_url', 'string', 255);
	}

	public function setUp()
	{
		parent::setUp();
		$this->hasOne('RingsideUser', array('local' => 'id', 'foreign' => 'domain_id'));
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
