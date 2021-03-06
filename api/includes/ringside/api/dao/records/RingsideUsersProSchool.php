<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RingsideUsersProSchool extends Doctrine_Record
{

  public function setTableDefinition()
  {
    $this->setTableName('users_pro_school');
    $this->hasColumn('id', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' =>  array(), 'primary' => true, 'notnull' => true, 'autoincrement' => true));
    $this->hasColumn('profile_id', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(10) unsigned', 'unsigned' => 1, 'values' =>  array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('school_name', 'string', 100, array('alltypes' =>  array(  0 => 'string', ), 'ntype' => 'varchar(100)', 'fixed' => false, 'values' =>  array(), 'primary' => false, 'default' => '', 'notnull' => true, 'autoincrement' => false));
    $this->hasColumn('grad_year', 'integer', 4, array('alltypes' =>  array(  0 => 'integer', ), 'ntype' => 'int(4)', 'unsigned' => 0, 'values' =>  array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
    $this->hasColumn('concentrations', 'string', 500, array('alltypes' =>  array(  0 => 'string', ), 'ntype' => 'varchar(500)', 'fixed' => false, 'values' =>  array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
    $this->hasColumn('is_highschool', 'integer', 1, array('alltypes' =>  array(  0 => 'integer',   1 => 'boolean', ), 'ntype' => 'tinyint(1)', 'unsigned' => 0, 'values' =>  array(), 'primary' => false, 'notnull' => false, 'autoincrement' => false));
  }

  public function setUp()
  {
    parent::setUp();
    $this->hasOne('RingsideUsersProfile', array('local' => 'profile_id', 'foreign' => 'id'));
  }

}