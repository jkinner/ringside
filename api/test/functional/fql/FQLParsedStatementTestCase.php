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

require_once("PHPUnit/Framework.php");
require_once( "ringside/api/fql/FQLParsedStatement.php");

class FQLParsedStatementTestCase extends PHPUnit_Framework_TestCase
{
	protected function setUp() 
   {
       
   }
   
   protected function tearDown()
   {   
   
   }
   
   public function testParserValidSimple()
   {
   	
   	$fql = "SELECT uid, (4+3), books, \"astr,ing\", somefunc(arg1), var1.var2 FROM user WHERE uid=1";
   	$parser = $this->expectPass($fql);
   
   	$sflds = $parser->getSelectFields();   	
		$this->assertEquals(6, count($sflds));
		$this->assertEquals("uid", $sflds[0]);
		$this->assertEquals("(4+3)", $sflds[1]);
		$this->assertEquals("books", $sflds[2]);
		$this->assertEquals("\"astr,ing\"", $sflds[3]);
		$this->assertEquals("somefunc(arg1)", $sflds[4]);
		$this->assertEquals("var1.var2", $sflds[5]);
		$this->assertEquals("user", $parser->getFromTable());
		
		$wflds = $parser->getWhereFields();
		$this->assertEquals(3, count($wflds));
		$this->assertEquals("uid", $wflds[0]);
		$this->assertEquals("=", $wflds[1]);
		$this->assertEquals("1", $wflds[2]);
		
   }
   
   public function testParserValidNested()
   {
   	$fql = "SELECT uid FROM user WHERE uid IN (SELECT uid FROM user WHERE uid=17001)";
		$parser = $this->expectPass($fql);
		
		$sflds = $parser->getSelectFields();   	
		$this->assertEquals(1, count($sflds));
		$this->assertEquals("uid", $sflds[0]);		
		
		$this->assertEquals("user", $parser->getFromTable());
		
		$wflds = $parser->getWhereFields();		
		$this->assertEquals(5, count($wflds));
		$this->assertEquals("uid", $wflds[0]);
		$this->assertEquals("in", $wflds[1]);
		$this->assertEquals("(", $wflds[2]);
		$ps = $wflds[3];
		$this->assertTrue(is_a($ps, "FQLParsedStatement"));
		$this->assertEquals(")", $wflds[4]);				
		
		$sflds = $ps->getSelectFields();
   	$this->assertEquals(1, count($sflds));
		$this->assertEquals("uid", $sflds[0]);
		
		$this->assertEquals("user", $ps->getFromTable());
		
		$wflds = $ps->getWhereFields();		
		$this->assertEquals(3, count($wflds));
		$this->assertEquals("uid", $wflds[0]);
		$this->assertEquals("=", $wflds[1]);
		$this->assertEquals("17001", $wflds[2]);
   }
   
   public function testParserTokenizedField()
   {
   	$fql = "SELECT userfromfield, userselectfield, userwherefield, userinfield FROM user WHERE uid=17001";
   	$ps = $this->expectPass($fql);   
   }
   
   public function testParserSelectFunctions()
   {
   	$fql = "SELECT uid,concat(arg1,\"arg2\",arg3,\"ar,g4\") FROM user where uid=17001";
   	$ps = $this->expectPass($fql);
   	
   	$sflds = $ps->getSelectFields();
   	$this->assertEquals(2, count($sflds));
		$this->assertEquals("uid", $sflds[0]);
		
		$func = $sflds[1];
		$this->assertTrue(is_a($func, "FQLFunction"));
		
		$this->assertEquals(4, count($func->m_args));
		$this->assertEquals("arg1", $func->m_args[0]);
		$this->assertEquals("\"arg2\"", $func->m_args[1]);
		$this->assertEquals("arg3", $func->m_args[2]);
		$this->assertEquals("\"ar,g4\"", $func->m_args[3]);
		
		
		$fql = "SELECT uid,concat(arg1,\"arg2\",arg3,\"ar,g4\"), thirdarg FROM user where uid=17001";
   	$ps = $this->expectPass($fql);
   	
   	$sflds = $ps->getSelectFields();
   	$this->assertEquals(3, count($sflds));
		$this->assertEquals("uid", $sflds[0]);
		
		$func = $sflds[1];
		$this->assertTrue(is_a($func, "FQLFunction"));
		
		$this->assertEquals(4, count($func->m_args));
		$this->assertEquals("arg1", $func->m_args[0]);
		$this->assertEquals("\"arg2\"", $func->m_args[1]);
		$this->assertEquals("arg3", $func->m_args[2]);
		$this->assertEquals("\"ar,g4\"", $func->m_args[3]);
		
		$this->assertEquals("thirdarg", $sflds[2]);
   }
   
   public function testWhereFunctions()
   {
   	$fql = "SELECT uid FROM user where uid=17001 AND concat(\"goats\",22,\"arg4\")=\"goats\"";
   	$ps = $this->expectPass($fql);
   	
   	$w = $ps->getWhereFields();
   	$this->assertEquals("uid", $w[0]);
   	$this->assertEquals("=", $w[1]);
   	$this->assertEquals(17001, $w[2]);
   	$this->assertEquals("and", $w[3]);
   	
   	$f = $w[4];
   	$this->assertTrue(is_a($f, "FQLFunction"));
   	$this->assertEquals("concat", $f->m_name);
   	$a = $f->m_args;
   	$this->assertEquals("\"goats\"", $a[0]);
   	$this->assertEquals("22", $a[1]);
   	$this->assertEquals("\"arg4\"", $a[2]);
   	
   	$this->assertEquals("=", $w[5]);
   	$this->assertEquals("\"", $w[6]);
   	$this->assertEquals("goats", $w[7]);
   	$this->assertEquals("\"", $w[8]);
   }
   
   public function testParserInvalid()
   {
   	$this->expectFailure("SEZECT uid FROM user WHERE uid=1",
   								"Parser exception encountered. Expected 'select' to start statement.");
   		
   	$this->expectFailure("SELECT uid, FROM user WHERE uid=1",
   								"Parser exception encountered. Cannot add zero-length field name. Expecting field before 'from'.");
   	
   	$this->expectFailure("SELECT uid,goats, FROM user WHERE uid=1",
   								"Parser exception encountered. Cannot add zero-length field name. Expecting field before 'from'.");
   	
   	$this->expectFailure("SELECT uid FRZM user WHERE uid=1",
   								"Parser exception encountered. Token 'where' not allowed in SELECT clause.");
   	
   	$this->expectFailure("SELECT uid FROM user,page WHERE uid=1",
   								"Parser exception encountered. Invalid table specification in FROM clause.");
   	
   	$this->expectFailure("SELECT uid goats FROM page WHERE uid=1",
   								"Parser exception encountered. Field names must be separated by a comma. Expecting field before 'from'.");
   	
   	$this->expectFailure("SELECT uid FROM user WHERE uid IN SELECT uid FROM user WHERE uid=2",
   								"Parser exception encountered. Nested SELECTs must be enclosed in parenthesis.");
   	
   	$this->expectFailure("SELECT uid FROM user WHERE uid IN (SELECT uid FROM user WHERE uid=2",
   								"Parser exception encountered. No closing parenthesis encountered in statement.");
   }
   
	public function testReplaceVariables()
	{
		$vars = array("var1" => "val1", "var2" => "val2");		
		$str = "This is var1: {var1}, this is var2: {var2}";		
		$nstr = FQLUtils::replaceVariables($str, $vars);
		$this->assertEquals("This is var1: val1, this is var2: val2", $nstr);
	}
   
   protected function expectPass($fql)
   {
   	$failed = false;
   	try {       	
       	$parser = new FQLParsedStatement($fql);
   	} catch (Exception $e) {   		
   		$failed = true;
   	}
   	if ($failed) $this->fail("Shouldn't have failed: " . $e->getMessage() . "\nFQL='$fql'");
   	return $parser;
   }
   
   protected function expectFailure($fql, $msg = "")
   {
   	$failed = false;
   	try {
       	$parser = new FQLParsedStatement($fql);
   	} catch (Exception $e) {
   		$failed = true;
   	}
   	if (!$failed) $this->fail("Should have failed: '$fql'");
   	if (strlen($msg) > 0) {
   		$this->assertEquals($msg, $e->getMessage());   
   	} else {
   		print "\n" . $e->getMessage() . "\n";
   	}
   }
   
}





?>
