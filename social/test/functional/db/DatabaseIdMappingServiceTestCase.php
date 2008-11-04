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

require_once('ringside/api/config/RingsideApiConfig.php');
require_once('ringside/social/DatabaseIdMappingService.php');

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class DatabaseIdMappingServiceTestCase extends PHPUnit_Framework_TestCase
{
    private $s;
    private $p;

    public function setUp()
    {
        $this->s = new Social_DatabaseIdMappingService();
    }
    
    public function tearDown()
    {
        if ( isset($this->p) )
        {
            $this->s->deletePrincipal($this->p);
        }
    }
    
    public function testCreate()
    {
        $this->p = $this->s->createPrincipal(1234, 123, 789);
        $this->assertNotNull($this->p);
    }

    public function testDoNotCreateTwice()
    {
        $e = null;
        $this->p = $this->s->createPrincipal(1234, 123, 789);
        $this->assertNotNull($this->p);
        $p2 = $this->s->createPrincipal(1234, 123, 789);
        try
        {
            $this->assertNotNull($p2);
            $this->assertEquals($this->p, $p2);
        }
        catch ( Exception $exception )
        {
            // This exception should only happen if the principal IDs are NOT the same, so delete the second principal
            $this->s->deletePrincipal($p2);
            $e = $exception;
        }

        if ( isset($e) )
        {
            throw $e;
        }
    }
    
    public function testLink()
    {
        $this->p = $this->s->link(1234, 123, 789, 234, 678);
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 123, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(789, $subjects[$this->p]);
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 234, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(678, $subjects[$this->p]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 123, array(789));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[789]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 234, array(678));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[678]);
    }

    public function testLinkExistingPrincipal()
    {
        $this->p = $this->s->createPrincipal(1234, 123, 789);
        $p2 = $this->s->link(1234, 123, 789, 234, 678);

        try
        {
            $this->assertEquals($this->p, $p2);
        }
        catch ( Exception $e )
        {
            $this->s->deletePrincipal($p2);
            throw $e;
        }
        
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 123, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(789, $subjects[$this->p]);
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 234, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(678, $subjects[$this->p]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 123, array(789));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[789]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 234, array(678));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[678]);
    }

    public function testLinkExistingPrincipalReverse()
    {
        $this->p = $this->s->createPrincipal(1234, 234, 678);
        $p2 = $this->s->link(1234, 123, 789, 234, 678);

        try
        {
            $this->assertEquals($this->p, $p2);
        }
        catch ( Exception $e )
        {
            $this->s->deletePrincipal($p2);
            throw $e;
        }
        
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 123, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(789, $subjects[$this->p]);
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 234, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(678, $subjects[$this->p]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 123, array(789));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[789]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 234, array(678));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[678]);
    }
    
    public function testUnlink()
    {
        $this->p = $this->s->link(1234, 123, 789, 234, 678);
        
        $this->s->unlink(1234, 123, 789);
        // Ensure the link we deleted is no longer there, ...
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 123, array($this->p));
        $this->assertEquals(0, sizeof($subjects));
        $principals = $this->s->mapSubjectsToPrincipals(1234, 123, array(789));
        $this->assertEquals(0, sizeof($principals));
        // ... and the one we left is still valid.
        $subjects = $this->s->mapPrincipalsToSubjects(1234, 234, array($this->p));
        $this->assertEquals(1, sizeof($subjects));
        $this->assertEquals(678, $subjects[$this->p]);
        $principals = $this->s->mapSubjectsToPrincipals(1234, 234, array(678));
        $this->assertEquals(1, sizeof($principals));
        $this->assertEquals($this->p, $principals[678]);
    }

    public function testGetSubjectsBySubject()
    {
        $this->p = $this->s->link(1234, 123, 789, 234, 678);
        $subjects = $this->s->mapSubjectToSubjects(1234, 123, 789);
        
        $this->assertEquals(2, sizeof($subjects));
        $found789 = false;
        $found678 = false;
        foreach ( $subjects as $subject )
        {
            if ( $subject['nid'] == 123 && $subject['uid'] == 789 )
            {
                $found789 = true;
            }

            if ( $subject['nid'] == 234 && $subject['uid'] == 678 )
            {
                $found678 = true;
            }
        }
        
        $this->assertTrue($found789, "Expected to find subject '789' on network '123'");
        $this->assertTrue($found678, "Expected to find subject '678' on network '234'");
    }
    
}
?>