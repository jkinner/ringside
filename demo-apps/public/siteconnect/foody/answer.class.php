<?php
 /*
  * Ringside Networks, Harnessing the power of social networks.
  *
  * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
  * by the @authors tag or express copyright attribution
  * statements applied by the authors.  All third-party contributions are
  * distributed under license by Ringside Networks, Inc.
  *
  * This is free software; you can redistribute it and/or modify it
  * under the terms of the GNU Lesser General Public License as
  * published by the Free Software Foundation; either version 2.1 of
  * the License, or (at your option) any later version.
  *
  * This software is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * Lesser General Public License for more details.
  *
  * You should have received a copy of the GNU Lesser General Public
  * License along with this software; if not, write to the Free
  * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
  * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class Answer extends Doctrine_Record {
    public function setTableDefinition() {
        $this->setTableName('quiz_answer');
        $this->hasColumn('id', 'integer', 4, array('autoincrement' => true, 'notnull' => true, 'primary' => true));
        $this->hasColumn('quiz_id', 'integer', 4, array('notnull' => true));
        $this->hasColumn('quiz_item', 'integer', 4, array('notnull' => true));
        $this->hasColumn('score', 'integer', 1, array('notnull' => true));
    }
    
    public function setUp() {
        parent::setUp();
        $this->hasOne('Quiz as quiz', array('local' => 'quiz_id', 'foreign' => 'id'));
    }
}
?>