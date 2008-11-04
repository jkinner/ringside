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
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'/../config.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'/../quiz.class.php');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'/../answer.class.php');


try {
        $dm = Doctrine_Manager::getInstance();
        $dm->openConnection($db_url);

//      print join(";\n", Doctrine::generateSqlFromArray(
        Doctrine::createTablesFromArray(
                array(
                    'Quiz',
                    'Answer'
                )
        );
//      )).";\n";
        print "Done\n";
} catch (Exception $e) {
    print $e->getMessage()."\n";
    error_log($e->getTraceAsString());
}
?>