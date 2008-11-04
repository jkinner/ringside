<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsidePaymentGateway extends Doctrine_Record
{

	public function setTableDefinition()
	{
		$this->setTableName('social_pay_gateways');
		$this->hasColumn('type', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('subject', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
		$this->hasColumn('password', 'string', 45, array('alltypes' => array(0 => 'string'), 'ntype' => 'varchar(45)', 'fixed' => false, 'values' => array(), 'primary' => true, 'default' => '', 'notnull' => true, 'autoincrement' => false));
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