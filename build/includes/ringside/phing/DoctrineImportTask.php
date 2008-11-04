<?php
/*******************************************************************************
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
 ******************************************************************************/

require_once ('ringside/phing/DSN.php');
require_once ('Doctrine/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
//Doctrine::loadModels('/ringside/api/dao/records');


/**
 * Task wrapper for DB Import
 */
class DoctrineImportTask extends Task
{
	
	public $_dsn = null;
	public $_dir = null;
	public $_options = array();

	/**
	 * Handles initialization of this task
	 */
	public function init()
	{
		$this->_options['singularize'] = true;
		//       $this->_options['packagesPrefix'] = 'ringfb';
		//       $this->_options['packagesPath'] = 'ringfb';
		//       $this->_options['packagesFolderName'] = 'ringfb';
		$this->_options['suffix'] = '.php';
		$this->_options['generateBaseClasses'] = true;
		$this->_options['generateTableClasses'] = true;
		$this->_options['baseClassPrefix'] = 'Base';
		$this->_options['baseClassesDirectory'] = 'generated';
		$this->_options['baseClassName'] = 'Doctrine_Record';
	}

	/**
	 * Main entry point for task
	 */
	public function main()
	{
		if(empty($this->_dir))
		{
			throw new Exception("Dir not configured ");
		}
		$manager = Doctrine_Manager::getInstance();
	
		// Set up validation
		$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
	
		$conn = Doctrine_Manager::connection($this->_dsn->_toString());
		
		$conn->setAttribute('portability', Doctrine::PORTABILITY_ALL);
		$conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
		Doctrine::generateModelsFromDb($this->_dir, array(), $this->_options);
	
	}

	public function setBaseClassPrefix($value)
	{
		$this->_options['baseClassPrefix'] = $value;
	}

	public function setPackagesPrefix($value)
	{
		$this->_options['packagesPrefix'] = $value;
	}

	public function setPackagesPath($value)
	{
		$this->_options['packagesPath'] = $value;
	}

	public function setPackagesFolderName($value)
	{
		$this->_options['packagesFolderName'] = $value;
	}

	public function createDsn()
	{
		$this->_dsn = new DSN();
		return $this->_dsn;
	}

	public function setDir($value)
	{
		$this->_dir = $value;
	}

}
