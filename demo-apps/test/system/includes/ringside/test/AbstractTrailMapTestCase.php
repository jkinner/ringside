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
require_once('simpletest/autorun.php');
require_once('simpletest/web_tester.php');

require_once 'MDB2.php';

global $webUrl;
global $demoUrl;
global $db_type;
global $db_server;
global $db_name;
global $db_username;
global $db_password;
include_once('TestSettings.php');

/**
 * Superclass to all demo app web tests for common test functionality.
 *
 * @author John Mazzitelli
 */
abstract class AbstractTrailMapTestCase extends WebTestCase
{
    /**
     * Restarts the simpletest's browser (simulates closing and reopening browser).
     */
    public function setUp()
    {
        $this->restart();
    }

    /**
     * Logs a failure message and throws an exception. Use this to immediately
     * kill a test after a failure has been detected.
     *
     * @param string $message
     */
    protected function failException(string $message)
    {
        $this->fail($message);
        $e = new Exception($message);
        $this->exception($e);
        throw $e;
    }

    /**
     * Returns the URL to the web server that is being tested.
     *
     * @global $webUrl the URL to the server (will be included by this class)
     *
     * @return string the URL this test will use when communicating with the Ringside server
     */
    protected function getWebUrl()
    {
        global $webUrl;

        if (is_null($webUrl))
        {
            $this->failException("webUrl is not set - TestSettings.php missing?");
        }

        return $webUrl;
    }

    /**
     * Returns the URL to the web server's demo apps. All demo apps should be found
     * under here.
     *
     * @global $demoUrl the URL to the demo apps (will be included by this class)
     *
     * @return string the URL this test will use when communicating with the demo apps
     */
    protected function getDemoUrl()
    {
        global $demoUrl;

        if (is_null($demoUrl))
        {
            $this->failException("demoUrl is not set - TestSettings.php missing?");
        }

        return $demoUrl;
    }

    /**
     * Executes a query from the test database and returns the results.
     *
     * @param string $sql the select query to execute
     *
     * @return array results of all rows selected
     */
    protected function dbQuery($sql)
    {
        $mdb2 =& $this->dbGetMDB2Object();
        $result = $mdb2->query($sql);
        if (PEAR::isError($result))
        {
            $this->failException($result->getMessage().' - '.$result->getUserinfo());
        }

        $array = $result->fetchAll();
        $result->free();

        echo __FILE__ . ": Results of query '" . $sql . "' follows:\n";
        var_dump($array);

        return $array;
    }

    /**
     * Logs in to the Ringside server using the given username and password.
     *
     * @param string $username the username to log in as
     * @param string $password the user's password used to authenticate the login
     */
    protected function login($username='joe@goringside.net', $password='ringside')
    {
        $this->get($this->getWebUrl() . "/login.php");
        $this->setField('email', "$username");
        $this->setField('p', $password );
        $this->clickSubmit('Login!');

        $this->assertText('Welcome to Ringside');
        $this->assertText('Getting Started');
        $this->assertText('Documentation');
        $this->assertNoText('Instant Demo');
    }

    private function dbGetMDB2Object()
    {
        global $db_type;
        global $db_server;
        global $db_name;
        global $db_username;
        global $db_password;

        $dsn = "$db_type://$db_username:$db_password@$db_server/$db_name";
        $mdb2 =& MDB2::factory($dsn);

        if (PEAR::isError($mdb2))
        {
            $this->failException($mdb2->getMessage().' - '.$mdb2->getUserinfo());
        }

        return $mdb2;
    }
}

?>