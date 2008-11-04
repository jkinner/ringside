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

require_once( "ringside/api/fql/FQLException.php" );
require_once( "ringside/api/fql/FQLUtils.php" );

define("FQL_RMODE_SELECT", 100);		//parser is within a SELECT clause
define("FQL_RMODE_FROM", 101);		//parser is within a FROM clause
define("FQL_RMODE_WHERE", 102);		//parser is within a WHERE clause
define("FQL_RMODE_ORDERBY", 103);	//parser is within a ORDER BY clause

define("FQL_RMODE_INIT", 200);		//parser has just been initialized
define("FQL_RMODE_COMPLETE", 201);	//parsing has been completed
define("FQL_RMODE_ERROR", 202);		//parser has encountered an error

define("FQL_EXPECTS_NOTHING", 300);   	//no explicit expectation
define("FQL_EXPECTS_ENDQUOTE", 301);	//expecting end quote "
define("FQL_EXPECTS_FIELD", 302);		//expecting new field

define("FQL_KEYWORDS", "/select | from | where | and | or | in |" .
							  "now\(|rand\(|strlen\(|concat\(|substr\(|strpos\(|lower\(|upper\(|" .
							  ",|\(|\)|\>|\<|\=|\+|\-|\/|\"/");

class FQLParsedStatement
{	

	protected static $m_staticDataInitialized = false;

	protected static $m_restrictedSelectTokens;
	
	protected static $m_functionTokens;
	
	protected static $m_functionDataMap;
	

	protected $m_selectFields;
	
	protected $m_fromTable;
	
	protected $m_whereFields;


	protected $m_regionMode;
	
	protected $m_expects;
	
	protected $m_parsedTokens;
		
	protected $m_index;
	
	protected $m_error;
	
	protected $m_fql;
	
	
	public function __construct($fql)
	{	
		$this->m_fql = trim(strtolower($fql));	
		self::initializeStaticData();
		$this->parse();
	}
	
	protected static function initializeStaticData()
	{
		if (!self::$m_staticDataInitialized) {			
			self::$m_restrictedSelectTokens = array("select", "where", "from", "and", "or");
			self::$m_functionTokens = array("now(","rand(","strlen(","concat(","substr(","strpos(","lower(","upper(");
			
			self::$m_functionDataMap = array();
			self::$m_functionDataMap["now"] = array("numArgs" => 0);
			self::$m_functionDataMap["concat"] = array("numArgs" => -1);
			self::$m_functionDataMap["rand"] = array("numArgs" => 0);
			self::$m_functionDataMap["strlen"] = array("numArgs" => 1);
			self::$m_functionDataMap["substr"] = array("numArgs" => 3);
			self::$m_functionDataMap["strpos"] = array("numArgs" => 2);
			self::$m_functionDataMap["upper"] = array("numArgs" => 1);
			self::$m_functionDataMap["lower"] = array("numArgs" => 1);
			self::$m_staticDataInitialized = true; 
		}
	}
	
	public function getSelectFields()
	{
		return $this->m_selectFields;
	}
	
	public function getFromTable()
	{
		return $this->m_fromTable;
	}
	
	public function getWhereFields()
	{
		return $this->m_whereFields;
	}
	
	protected function init()
	{	
		$this->m_regionMode = FQL_RMODE_INIT;
		$this->m_expects = FQL_EXPECTS_NOTHING;		
		$this->m_index = 0;
		$this->m_error = "";
		$this->m_parsedTokens = FQLUtils::splitWithTokens(FQL_KEYWORDS, $this->m_fql);
		
		$this->m_selectFields = array();
		$this->m_fromTable = null;
		$this->m_whereFields = array();
	}
	
	protected function parse()
	{	
		$this->init();
		
		$buf = "";
		
		while (($this->m_regionMode != FQL_RMODE_COMPLETE) && ($this->m_regionMode != FQL_RMODE_ERROR)) {
		
			if ($this->m_index == count($this->m_parsedTokens)) {
				$this->m_regionMode = FQL_RMODE_COMPLETE;
				break;
			}
			
			$tpair = $this->m_parsedTokens[$this->m_index];
			
			if ($this->m_regionMode == FQL_RMODE_INIT) {
						
				//look for the SELECT clause				
				if ($tpair[0] == "select") {
					$this->m_regionMode = FQL_RMODE_SELECT;
				} else {
					$this->setError("Expected 'select' to start statement.");
					break;
				}			
									
			} else if ($this->m_regionMode == FQL_RMODE_SELECT) {
			
				//parse through select fields, until encountering an
				//error or the FROM token
				$this->parseSelectFields();					
				
			} else if ($this->m_regionMode == FQL_RMODE_FROM) {
			
				//retrieve the FROM table, move to WHERE clause
				if ($this->m_fromTable == null) {					
					$this->m_fromTable = trim($tpair[1]);
					$this->m_index++;
				} else {
					if ($tpair[0] == "where") {
						$this->m_regionMode = FQL_RMODE_WHERE;						
					} else {
						$this->setError("Invalid table specification in FROM clause.");
				 		break;
					}
				}
			
			} else if ($this->m_regionMode == FQL_RMODE_WHERE) {
			
				//parse through where clause
				$this->parseWhereFields();
				
			}
		}
		
		//print "\nrmode=" . $this->m_regionMode . "\nnerror=$this->m_error\n";
		if ($this->m_regionMode == FQL_RMODE_ERROR)  throw new FQLException("Parser exception encountered. " . $this->m_error);
		
	}
	
	protected function setError($msg)
	{
		$this->m_regionMode = FQL_RMODE_ERROR;
		$this->m_error = $msg;
	}
		
	protected function parseSelectFields()
	{			
		$buf = "";
	
		while (($this->m_regionMode != FQL_RMODE_ERROR) && ($this->m_regionMode != FQL_RMODE_FROM)) {
		
			$tpair = $this->m_parsedTokens[$this->m_index];
		
			if ($tpair[0] == "select") {
				//put the first field into the buffer
				$buf .= $tpair[1];
				$this->m_index++;
					
			} else if ($tpair[0] == ",") {
					
				if ($this->m_expects != FQL_EXPECTS_ENDQUOTE) {
				
					//if a function was not just added...
					$nflds = count($this->m_selectFields);
					if (($nflds == 0) || is_string($this->m_selectFields[$nflds-1])) {
    					//pop the field (contained in $buf) - add to list					
    					if (!$this->addSelectField($buf)) break;
					}
					$buf = $tpair[1];
        			$this->expects = FQL_EXPECTS_FIELD;
					    				
				} else {
					//add comma to field name as part of literal string
					$buf .= "," . $tpair[1];
				}
    			$this->m_index++;
						
			} else if ($tpair[0] == "from") {
			
				if ($this->addSelectField($buf)) {    				
					//move on to parse the FROM table on next iteration
    				$this->m_regionMode = FQL_RMODE_FROM;
    				$this->m_expects = FQL_EXPECTS_NOTHING;
				} else {
					$this->setError($this->m_error . " Expecting field before 'from'.");
					break;
				}

			} else if ($tpair[0] == "\"") { 
				
				if ($this->m_expects != FQL_EXPECTS_ENDQUOTE) {
					//the start of a literal string
					$this->m_expects = FQL_EXPECTS_ENDQUOTE;
					$buf .= "\"" . $tpair[1];
					$this->m_index++;
				} else {
					//the end of a literal string
					$this->m_expects = FQL_EXPECTS_NOTHING;
					$buf .= "\"";
					$this->m_index++;
				}
				 		
			} else if (in_array($tpair[0], self::$m_functionTokens)) {
			 
				//strip opening parenthesis from function token
				$fname = trim($tpair[0]);				
				$fname = substr($fname, 0, strlen($fname)-1);
				
				//parse this function or return with error 	
				$func = $this->parseFunction($fname);
				if ($func == null) break;				
				$this->m_selectFields[] = $func;				
				
				//check to see if we've encountered FROM
				$tpair = $this->m_parsedTokens[$this->m_index];
				if ($tpair[0] == "from") {
    				$this->m_regionMode = FQL_RMODE_FROM;
        			$this->m_expects = FQL_EXPECTS_NOTHING;
				}
			
			} else {
			
				//append any non-restricted tokens to the field name
				if (!in_array($tpair[0], self::$m_restrictedSelectTokens)) {
			 		$buf .= $tpair[0] . $tpair[1];
			 		$this->m_index++;
			 	} else {
			 		$this->setError("Token '" . $tpair[0] . "' not allowed in SELECT clause.");
			 		break;
			 	}
			 	
			}
		}
	}
	
	protected function addSelectField($str)
	{
		$fld = trim($str);
		if (strlen($fld) == 0) {
			$this->setError("Cannot add zero-length field name.");
			return false;
		}
		if (strpos($fld, " ") !== false) {
			$this->setError("Field names must be separated by a comma.");
			return false;
		}
		$this->m_selectFields[] = $fld;
		return true;
	}
	
	protected function parseFunction($funcName)
	{
		if (!array_key_exists($funcName, self::$m_functionDataMap)) {
			$this->setError("Unknown function '$funcName'.");
			return null;
		}
	
		$tpair = $this->m_parsedTokens[$this->m_index];				
		$str = $tpair[1];
		$this->m_index++;		
		$str .=  $this->parseUntilClosingParenthesis(false);			
		$argStr = trim($str);		
			
		$fdata = self::$m_functionDataMap[$funcName];		
		//print "\nparseFunction: funcName='$funcName', argStr='$argStr'\n";
		$args = $this->parseFunctionArgs($argStr);
		if ($fdata["numArgs"] > -1) {
			if (count($args) != $fdata["numArgs"]) {
				$this->setError("Incorrect number of arguments for function '$funcName'.");
				return null;		
			}
		}
		$func = new FQLFunction();
		$func->m_name = $funcName;
		$func->m_args = $args;
			
		return $func;
	}
	
	protected function parseFunctionArgs($str)
	{
		$args = array();
		
		$buf = "";
		$inQuote = false;
		for ($k = 0; $k < strlen($str); $k++) {
	
			$addArg = false;	
			if ($str[$k] == "\"") {
				$inQuote = !$inQuote;
			}
					
			if ($str[$k] == ",") {
				if ($inQuote) {
					$buf .= $str[$k];				
				} else {
					$addArg = true;
				}
			} else {
				$buf .= $str[$k];
			}
			
			if ($k == (strlen($str)-1)) $addArg = true;
			
			if ($addArg) {
				$buf = trim($buf);
				if (strlen($buf) > 0) {									
					$args[] = $buf;
					$buf = "";					
				}
				$inQuote = false;
				$addArg = false;
			}			
		}
				
		return $args;
	}
	
	protected function parseWhereFields()
	{			
		while (($this->m_regionMode != FQL_RMODE_ERROR) && ($this->m_regionMode != FQL_RMODE_ORDERBY)) {
		
			if ($this->m_index >= count($this->m_parsedTokens)) {
				$this->m_regionMode = FQL_RMODE_COMPLETE;
				return;
			}
		
			$tpair = $this->m_parsedTokens[$this->m_index];
		
			if ($tpair[0] != "where") {
			
				if ($tpair[0] == "select") {			
    				//verify that previous token was an open-parenthesis
    				$prevtpair = $this->m_parsedTokens[$this->m_index-1];
    				if ($prevtpair[0] != "(") {
    					$this->setError("Nested SELECTs must be enclosed in parenthesis.");
    					return;
    				}
    				//read in nested SQL up to closing parenthesis
    				$str = $this->parseUntilClosingParenthesis();
    				if ($str == null) return;
    				
    				//parse statement and add to list of WHERE fields
    				$this->m_whereFields[] = new FQLParsedStatement($str);
    				$this->m_whereFields[] = ")";
				} else {
				
					if (in_array($tpair[0], self::$m_functionTokens)) {
						//parse function in WHERE clause
        				//strip opening parenthesis from function token
        				$fname = trim($tpair[0]);				
        				$fname = substr($fname, 0, strlen($fname)-1);
        				
        				//parse this function or return with error 	
        				$func = $this->parseFunction($fname);
        				if ($func == null) break;			
        				$this->m_whereFields[] = $func;
        				
        				//index will be re-incremented at end of iteration
        				$this->m_index--;
        								
					} else {
					
						//parse non-function and non-nested FQL token
						$t1 = trim($tpair[0]);
						if (strlen($t1) > 0) $this->m_whereFields[] = $t1;					
						$t2 = trim($tpair[1]);
						if (strlen($t2) > 0) $this->m_whereFields[] = $t2;
					}
				}
				
		   } else {
		   	$t = trim($tpair[1]);
				if (strlen($t) > 0) $this->m_whereFields[] = $t;
			}
			$this->m_index++;
		}
	}
	
	protected function parseUntilClosingParenthesis($addSpace = true)
	{
		$str = "";		
		$level = 1;
		while ($level > 0) {
		
			if ($level == 0) break;
		
			if ($this->m_index >= count($this->m_parsedTokens)) {
				$this->setError("No closing parenthesis encountered in statement.");
				return null;
			}
			
			$tpair = $this->m_parsedTokens[$this->m_index++];
			
			if ($tpair[0] == "(") {
				$level++;
			} else if ($tpair[0] == ")") {
				$level--;
			}
			//print "\nlevel=$level, index=" . $this->m_index . ", tpair[0]='" . $tpair[0] . "', tpair[1]='" . $tpair[1] . "', cnt=" . count($this->m_parsedTokens) . "\n"; 
			
			if ($level < 0) {
				$this->setError("Extra ')' found in statement.");
				return null;
			}			
			if ($addSpace) {
				$str .= $tpair[0] . " " . $tpair[1] . " ";
			} else {
				$str .= $tpair[0] . $tpair[1];
			}
		}		
		//remove closing parenthesis and possibly trailing spaces
		$trimAmt = $addSpace ? 4 : 1;
		return substr($str, 0, strlen($str)-$trimAmt);
	}
	
	
}

class FQLFunction
{
	public $m_name;	
	public $m_args;
	
	public function __construct()
	{
		$m_args = array();
	}
}

?>
