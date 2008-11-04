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

require_once('Doctrine/lib/Doctrine.php');

set_include_path(
    dirname(__FILE__).'/api/includes'.PATH_SEPARATOR.
    get_include_path()
);
## Database configuration setttings.
### Settings only required in social pass service deployed separate from Ringside instance.
$db_type = 'mysql';
$db_server = 'entourage:3306';
$db_name = 'ringside_mig';
$db_username = 'ringside_user';
$db_password = 'ringside';
    
### Setup your doctrine connection
### Settings only required in social pass service deployed separate from Ringside instance.
require_once ('Doctrine/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
//Doctrine::loadModels('ringside/api/dao/records/');
$dsn = "$db_type://$db_username:$db_password@$db_server/$db_name";

$manager = Doctrine_Manager::getInstance();
$conn = Doctrine_Manager::connection($dsn);
$conn->setAttribute('portability', Doctrine::PORTABILITY_ALL);

//Doctrine::generateMigrationsFromModels('/tmp/dm', '/ringside/workspace/ringside/api/includes/ringside/api/dao/records');
Doctrine::migrate('/tmp/dm');
echo 'Done.';
?>