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

/**
 * Tests Getting Started Trail #1.
 *
 * This is just the installer trail map - we don't actually run the installer,
 * but we do ensure the Ringside app is installed and running.
 *
 * @author John Mazzitelli
 */
class TrailMap1TestCase extends AbstractTrailMapTestCase
{
    public function testLoginPage()
    {
        $this->get( $this->getWebUrl() . "/login.php" );
        $browser = $this->getBrowser();

        $this->assertText('quick idea');
        $this->assertText('Create a New Profile');

        $label = 'Create a New Profile';
        $this->assertLink($label, $this->getWebUrl() . "/register.php", "Should have got register.php but got " . var_export( $browser->getLink($label), true));
    }

    public function testLoginFailed()
    {
        $this->get( $this->getWebUrl() . "/login.php");
        $browser = $this->getBrowser();
        $this->setField( "email", "joe@goringside.net" );
        $this->setField( "p", "ringsid" );
        $this->clickSubmit( "Login!");

        $this->assertText('Login Error');
    }

    public function testLoginGood()
    {
        $this->login();
    }
}

?>