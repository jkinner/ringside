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

require_once('ringside/test/AbstractTrailMapTestCase.php');

class TrailMap3TestCase extends AbstractTrailMapTestCase
{
    var $m_appName = "trail_map_3";

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
// TODO we need to delete the app if one was added
//$this->get($this->getWebUrl() . "/canvas.php/developer/delete_app.php?api_key=CHANGEME");
        parent::tearDown();
    }

    public function testCreateAddDeleteDemoApplication()
    {
        $this->login();

        $this->assertClickable( "Developer", "Can not locate Developer link" );

        // Navigate into the Developer Link
        if ( $this->click( "Developer" ) === false )
        {
            $this->failException( "Could not click on developer Link");
        }

        // Nav into Add New Application
        $this->assertClickable("Add New Application...", "Could not locate link to 'Add New Application...'");
        if ( $this->click("Add New Application...") === false )
        {
            $this->failException("Could not navigate into Add New Application");
        }

        // Nav into Create Application
        $sql_results = $this->dbQuery("select count(*) from app where name='" . $this->m_appName . "'");
        if ($sql_results[0][0] == 1)
        {
           $this->failException("Already have " . $this->m_appName . " app");
        }
        
        $this->assertSubmit("Create Application", "Could not locate button to 'Create Application'" );
        $this->setField( "app_name", $this->m_appName);
        if ( $this->clickSubmit( "Create Application", "Could not click submit application") === false )
        {
            $this->failException( "Could not navigate into Create Application");
        }

        $sql_results = $this->dbQuery("select id from app where name='" . $this->m_appName . "'");
        $app_id = $sql_results[0][0];
        if (!($app_id > 0))
        {
           $this->failException("Failed to create " . $this->m_appName . " app");
        }
        
        // Nav into Edit Application Properties - set API/secret keys to our known hardcoded values
        $this->assertClickable( "Edit Application Properties", "Could not locate link to 'Edit Application Properties'");
        if ( $this->click( "Edit Application Properties" ) === false )
        {
            $this->failException( "Could not navigate into Edit Application Properties");
        }

        $this->setField("callback_url", $this->getDemoUrl() . $this->m_appName);
        $this->setField("canvas_url", $this->m_appName);
        $this->setField("api_key", "CHANGEME");
        $this->setField("secret_key", "CHANGEME");
        if ( $this->clickSubmit("Save Changes", "Could not click Save Changes") === false )
        {
            $this->failException("Could not save application properties");
        }

        // Nav into Application page
        $this->assertClickable("Applications", "Could not locate link to 'Applications'");
        if ( $this->click("Applications" ) === false )
        {
            $this->failException("Could not navigate into Applications");
        }

// TODO: everything above here works... pretty much everything below doesn't work
// need to find out why simpletest can't find the clickable button that is outside of any form
$sql_results = $this->dbQuery("select * from app where name='" . $this->m_appName . "'");
echo $this->getBrowser()->getContent() . "\n";
        
        // Go to the page that lets us add an application for the users
        $this->assertClickable("+ Add New Applications", "Could not locate button to '+ Add New Applications'" );
        if ( $this->click("+ Add New Applications" ) === false )
        {
            $this->failException("Could not navigate into + New Applications");
        }
        
        // click the link to add the app to the user's list of apps
        $this->assertClickable("Add this Application", "Missing Add this Application link(s)"); // note there can be more than one
        $link = $this->getBrowser()->getLink("Add this Application");
        $link = ereg_replace("=[0-9]+", "=" . $app_id, $link);

        $this->assertClickable("Add application!", "Could not locate link to 'Add application!'");
        if ( $this->click("Add application!") === false)
        {
            $this->failException("Could not add the application!");
        }
    }
}

?>