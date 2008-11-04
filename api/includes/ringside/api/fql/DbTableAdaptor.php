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

define("FQL_OBJECT_PATH_DELIM", "$");

class DbTableAdaptor
{
   protected $m_configMap;
   
   protected $m_rootTableName;
   
   protected $m_rootObjectName;
   
   protected $m_keyField;
   
   /**
	 * Maps a complex value's path (i.e. users_affiliations_affiliation)
	 * to an FQL field name, which can then be looked up in $m_configMap.
	 */
   protected $m_pathToFieldMap;
   
   protected $m_fqlFunctionToSqlFunctionMap;
   

   public function __construct()
   {    
		$this->m_fqlFunctionToSqlFunctionMap = array("strlen" => "char_length",
																	"substr" => "substring",
																 	"strpos" => "locate");
   }   
   
   public function setConfigFile($fpath)
   {
   	$fd = fopen($fpath, "r");
		if ($fd === false) {
			throw new FQLException("[DbTableAdaptor] couldn't open' file '" . $fpath . "'.");
		}
		$txt = stream_get_contents($fd);
		fclose($fd);		 
		$this->setConfigJson($txt);
   }
   
   protected function setConfigJson($json)
   {
   	$data = json_decode($json, true);		
		if (is_array($data)) {
		
			$this->m_configMap = array();
			$this->m_pathToFieldMap = array();
			foreach ($data as $dval) {
				if (!array_key_exists("globalProperties", $dval)) {
					$this->m_configMap[$dval["name"]] = $dval;	
					$path = str_replace("/", FQL_OBJECT_PATH_DELIM, $dval["treePath"]);
					$this->m_pathToFieldMap[$path] = $dval["name"];										
				} else {
					$this->m_rootTableName = $dval["rootTableName"];
					$this->m_rootObjectName = $dval["rootObjectName"];
					$this->m_keyField = $dval["keyField"];
				}
			}			
			
		} else {
			throw new FQLException("[DbTableAdaptor] Invalid configuration JSON");
		}
   }
   
   protected function validateParsedStatement($parsedStatement)
   {
   	return;
   }
   
   protected function getExtraWhereSQL($vars)
   {
   	return "";
   }
    
   public function retrieveFields($engine, $parsedStatement, $vars = array())
   {
   	$fieldNames = $parsedStatement->getSelectFields();
   	$whereTokens = $parsedStatement->getWhereFields();
   	
   	$this->validateParsedStatement($parsedStatement);
   	
   	$selectFields = array();
   	$joins = array();
   	
   	//keeps track of joined tables to avoid duplicates
   	$iJoined = array();
   	$oJoined = array();   	
   
      foreach ($fieldNames as $field) {
			//add SELECT fields with any associated JOINs
      	$this->processSelectField($field, $selectFields, $joins, $iJoined, $oJoined, $vars);    	
      }
      
      //rummage through the WHERE tokens, doing things.
      $where = "";
      foreach ($whereTokens as $wtok) {      
      	$this->processWhereField($wtok, $vars, $where, $engine, $joins, $iJoined, $oJoined);     
      }
      
      //build SELECT clause
      $sql = "SELECT ";
      $first = true;
      foreach ($selectFields as $sfld) {
      	if (!$first) $sql .= ",";
      	else $first = false;
      	
      	$sql .= " $sfld"; 
      }
      //build FROM clause
      $sql .= " FROM " . $this->m_rootTableName;
      //build LEFT and OUTER join clauses
      foreach ($joins as $j) {
      	$sql .= " $j ";
      }      
      //build WHERE clause
      $sql .= " WHERE " . str_replace("\"", "'", $where);
      
      //append extra SQL (from subclasses)
      $sql .= $this->getExtraWhereSQL($vars);
      
      //print "\n$sql\n";
            
      $ds = mysql_query($sql, $engine->getDbConnection());
      if (!$ds) {
      	throw new FQLException("Could not execute mapped FQL->SQL query: " .
      								  mysql_error() . "\nSQL='$sql'");
      }
      
      //fill in an object with values returned from the query
      $resultObjects = array();      
      while ($row = mysql_fetch_assoc($ds)) {     		
      
      	//get key value
      	$kname = $this->m_rootObjectName . FQL_OBJECT_PATH_DELIM . $this->m_keyField;
      	if (!array_key_exists($kname, $row)) {
      		throw new FQLException("No key field '" . $this->m_keyField . "' found in result set.");
      	}
      	$keyValue = $row[$kname];
      	
      	if (!array_key_exists($keyValue, $resultObjects)) {
      		$resultObjects[$keyValue] = array();
      	}
      	      	
      	$this->fillInObject($resultObjects[$keyValue], $row);
      }
      $rootObj = array($this->m_rootObjectName => array());
      foreach ($resultObjects as $rkey => $robj) {
      	$rootObj[$this->m_rootObjectName][] = $robj[$this->m_rootObjectName]; 
      }
      
      return $rootObj;      
   }
   
   protected function processSelectField(&$field, &$selectFields, &$joins, &$iJoined, &$oJoined, &$vars)
   {
   	if (is_string($field)) {   
   		$this->addRelevantSqlForField($field, $selectFields, $joins, $iJoined, $oJoined, $vars);
   		
   	} else {
   	
   		//process function
   		$func = $field;
   		$args = $func->m_args;
   		
   		//the string representing the function
   		$funcname = $func->m_name;
   		if (array_key_exists($funcname, $this->m_fqlFunctionToSqlFunctionMap)) {
   			$funcname = $this->m_fqlFunctionToSqlFunctionMap[$func->m_name];
   		}
   		$fstr = $funcname . "(";
   		
   		//change names of arg fields where needed
   		$first = true;
   		foreach ($args as $arg) {
   			if ($first) $first = false;
   			else $fstr .= ",";
   			
   			if (($arg[0] != "\"") && !is_numeric($arg)) {
   				//convert to SQL field and add relevant JOIN(s)
   				$fstr .= $this->addRelevantSqlForField($arg, $selectFields, $joins, $iJoined, $oJoined, $vars, true);   				
   			} else {
   				//replace double quotes with single quotes
   				$fstr .= str_replace("\"", "'", $arg); 
   			}
   		}   	
   		//add to select field
   		$selectFields[] = "$fstr) AS " . $this->m_rootObjectName . 
   								FQL_OBJECT_PATH_DELIM . "anon" . rand();
   	}
   }   
   
   
    protected function addRelevantSqlForField(&$field, &$selectFields, &$joins, &$iJoined, &$oJoined, &$vars, $forFunction= false)
    {
    	$selectFieldString = "";
    
    	$fpath = explode(".", $field);
      if (!array_key_exists($fpath[0], $this->m_configMap)) {
      	throw new FQLException("Invalid field specified: '" . $fpath[0] . "'");
      }
      $props = $this->m_configMap[$fpath[0]];      	
              	
      if ($props["type"] == "simple") {    
              	
      	if (!array_key_exists("sourceTable", $props)) {
       		//assume root table name
          	$sourceTable = $this->m_rootTableName; 
       	} else {
       		$sourceTable = $props["sourceTable"];
       	}
              	
       	//figure out SQL SELECT field name
       	$val = null;
       	$selectFieldString = $this->createSelectFieldFromProps($props, $vars, $sourceTable, $fpath, $val, !$forFunction);
      	if (!$forFunction) $selectFields[] = $selectFieldString; 
              		
       	//add LEFT (INNER) JOIN if needed
         $this->createInnerJoinFromProps($props, $joins, $iJoined, $vars, $sourceTable);
              		
    	} else if ($props["type"] == "complex") {      	
            		
       	$altTableName = isset($props["alternateTableName"]) ? $props["alternateTableName"] : "";       			
              	
       	//extract the properties for the field(s) specified
      	$valProps = $props["values"];
      	if (count($fpath) > 1) {
      		$leafName = $fpath[count($fpath)-1];
      		//reinitialize valProps to only the value specified      			
        		$valProps = array();
        		foreach ($props["values"] as $val) {
        			if (array_key_exists("subField", $val) && ($leafName == $val["subField"])) {
      				//if the leaf name is not actually a value, but an object (like
      				//work_history.location in the user table), make sure all it's values
        				//are added
         			$valProps[] = $val;
         		} else {
          			if ($leafName == $val["name"])  $valProps[] = $val;
          		}
          	}
        	}	
          		
        	//add SELECT fields for SQL
        	foreach ($valProps as $val) {
          			
        		$sourceTable = isset($val["sourceTable"]) ? $val["sourceTable"] : $props["sourceTable"];
        		if (($sourceTable == null) || (strlen($sourceTable) == 0)) {
        			$sourceTable = $this->m_rootTableName;
        		}
          			          						
          	//replace the source table name with an alias if specified in config file
        		//with the "alternateTableName" property
          	$stbl = (strlen($altTableName) > 0) ? $altTableName : $sourceTable;            
          	$selectFieldString = $this->createSelectFieldFromProps($props, $vars, $stbl, $fpath, $val, !$forFunction);
          	if (!$forFunction) $selectFields[] = $selectFieldString;	
        	}
          		
        	//add OUTER JOIN if needed
        	$this->createOuterJoinFromProps($props, $joins, $oJoined);
          		
        	//add LEFT (INNER) JOIN if needed
        	$this->createInnerJoinFromProps($props, $joins, $iJoined, $vars, null, $altTableName);
     	}
     	return $selectFieldString;
	}
   
   
   
   
   protected function createSelectFieldFromProps(&$props, &$vars, &$sourceTable, &$fpath, &$val = null, $includeAs = true)
   { 
   	$fldname = "";
   	
     	$sname = str_replace("/", FQL_OBJECT_PATH_DELIM, $props["treePath"]) . FQL_OBJECT_PATH_DELIM;
     	if ($val != null) {
 	   	$sname .= $val["name"];
     	} else {
     		$sname .= $fpath[0];	
     	}
   		       		  						
		if (isset($props["sourceField"]) && isset($props["sourceSQL"])) {
			throw new FQLException("Cannot specify both sourceField and sourceSQL for field '" . $fpath[0] . "'");
		}
		//determine field to SELECT, first try $props
      if (isset($props["sourceField"])) {
        	$sqlField = "$sourceTable." . $props["sourceField"];
      } else if (isset($props["sourceSQL"])) {
        	$sqlField = FQLUtils::replaceVariables($props["sourceSQL"], $vars);
      }

      //override if set in $val
      if ($val != null) {
        	if (isset($val["sourceField"]) && isset($val["sourceSQL"])) {
    			throw new FQLException("Cannot specify both sourceField and sourceSQL for field '"
    											. $fpath[0] . "', sub-value '" . $val["name"] . "'");
    		}
    		//determine field to SELECT, first try $val
         if (isset($val["sourceField"])) {
           	$sqlField = "$sourceTable." . $val["sourceField"];
         } else if (isset($val["sourceSQL"])) {
           	$sqlField = FQLUtils::replaceVariables($val["sourceSQL"], $vars);
      	}
      }

      $fldname = $sqlField;
      if ($includeAs) $fldname .= " AS $sname";
   	      
   	return $fldname;
   }		
   
	protected function createInnerJoinFromProps(&$props, &$joins, &$iJoined, &$vars, $sourceTable = null, $alternateTableName = "")
	{ 
   	//add LEFT JOIN
    	if (isset($props["innerJoinFieldLeft"])
     			&&  isset($props["innerJoinTableRight"])
     			&&  isset($props["innerJoinFieldRight"])) {
      			
    		$innerJoinTableLeft = ($sourceTable == null) ? $props["innerJoinTableLeft"] : $sourceTable;
    		$innerJoinFieldLeft = $props["innerJoinFieldLeft"];
    		$innerJoinTableRight = $props["innerJoinTableRight"];
    		$innerJoinFieldRight = $props["innerJoinFieldRight"];
      			      				
    		//only do duplicate tables if an alternate table join
      	//name was specified in the config file
      	if (!in_array($innerJoinTableLeft, $iJoined) || (strlen($alternateTableName) > 0)) {
      			
      		//replace the source table name with an alias if specified in config file
      		//with the "alternateTableName" property
      		$ijl = (strlen($alternateTableName) > 0) ? $alternateTableName : $innerJoinTableLeft;
      			
      		$joinSql= "LEFT JOIN $innerJoinTableLeft ";
      		if (strlen($alternateTableName) > 0) {
      			$joinSql .= "AS $alternateTableName ";
      		}
      		$joinSql .= "ON $ijl.$innerJoinFieldLeft=" .
      					   "$innerJoinTableRight.$innerJoinFieldRight";
      		if (isset($props["extraJoinSQL"])) {
      			$ejsql = FQLUtils::replaceVariables($props["extraJoinSQL"], $vars);
      			//parse join sql for variables
      			$joinSql .= " $ejsql ";    				
          	}
          	$joins[] = $joinSql;
          	$iJoined[] = $innerJoinTableLeft;
      	}
      }
	}   
	
	protected function createOuterJoinFromProps(&$props, &$joins, &$oJoined)
	{
   	if (isset($props["outerJoinTableLeft"])
   			&&  isset($props["outerJoinFieldLeft"])
   			&&  isset($props["outerJoinTableRight"])
   			&&  isset($props["outerJoinFieldRight"])) {
      			
   		$outerJoinTableLeft = $props["outerJoinTableLeft"];
      	$outerJoinFieldLeft = $props["outerJoinFieldLeft"];
      	$outerJoinTableRight = $props["outerJoinTableRight"];
      	$outerJoinFieldRight = $props["outerJoinFieldRight"];
      			      				
      	if (!in_array($outerJoinTableLeft, $oJoined)) {
          	$joins[] = "LEFT OUTER JOIN $outerJoinTableLeft ON $outerJoinTableLeft.$outerJoinFieldLeft=" .
          				  "$outerJoinTableRight.$outerJoinFieldRight";
          	$oJoined[] = $outerJoinTableLeft;
      	}
      }
	}
	
	protected function processWhereField(&$wtok, &$vars, &$where, &$engine, &$joins, &$iJoined, &$oJoined)
   {
     	if (is_string($wtok)) {
      		
     		$wpath = explode(".", $wtok);
     		$wval = $wpath[0];
        	//convert names of known fields to their SQL equivalent
       	if (array_key_exists($wval, $this->m_configMap)) {
        		$props = $this->m_configMap[$wval];
          		
        		if ($props["type"] == "simple") {      	
          			 
        			$sourceTable = $props["sourceTable"];
           		$sourceField = $props["sourceField"];
           		
           		$this->createInnerJoinFromProps($props, $joins, $iJoined, $vars, $sourceTable);
           		
           		if (!isset($props["sourceSQL"])) {
           			$wrepSql = " $sourceTable.$sourceField ";
           		} else {
           			$wrepSql = $props["sourceSQL"];
           		}
         		
        		} else if ($props["type"] == "complex") {
        			$leafName = $wpath[count($wpath)-1];
          			
        			$valProps = null;
        			//find source table and source field for complex property
        			foreach ($props["values"] as $val) {
        				if ($val["name"] == $leafName) {
        					$valProps = $val;
        					break;
        				}	
        			}
        			if ($valProps == null) {
        				throw new FQLException("Cannot query based on field '$wtok' in WHERE clause.");
        			}
        			$sourceTable = isset($valProps["sourceTable"]) ?
        									$valProps["sourceTable"] : $props["sourceTable"];
        			$sourceField = isset($valProps["sourceField"]) ?
        									$valProps["sourceField"] : $props["sourceField"];

        			$wrepSql = " $sourceTable.$sourceField "; 
        		}  
        		$where .= $wrepSql;    		
        	} else {
        		if (($where[strlen($where)-1] != "\"") && ($wval != "\"")) {
        			//only append spaces for non-quoted items
        			$where .= " $wval ";
        		} else {
        			$where .= $wval;
        		}
        	}
    	} else {
    		if (is_a($wtok, "FQLParsedStatement")) {
    		
        		//execute and embed flatted result of nested statement      		
    			$result = $engine->queryParsedStatement($vars["APP_ID"], $vars["USER_ID"], $wtok);
    			$flatResult = $this->flattenResult($result);
    			if (count($flatResult) == 0) {
    				$flatResult[] = 'null';									 
    			}
          	$where .= implode(",", $flatResult);
          	
    		} else if (is_a($wtok, "FQLFunction")) {
    		
        		//process function
       		$func = $wtok;
       		$args = $func->m_args;
       		
       		//the string representing the function
       		$funcname = $func->m_name;
       		if (array_key_exists($funcname, $this->m_fqlFunctionToSqlFunctionMap)) {
       			$funcname = $this->m_fqlFunctionToSqlFunctionMap[$func->m_name];
       		}
       		$fstr = $funcname . "(";
       		
       		//change names of arg fields where needed
       		$first = true;
       		foreach ($args as $arg) {
       			if ($first) $first = false;
       			else $fstr .= ",";
       			
       			if (($arg[0] != "\"") && !is_numeric($arg)) {
       				//convert to SQL field and add relevant JOIN(s)
       				$fstr .= $this->addRelevantSqlForField($arg, $selectFields, $joins, $iJoined, $oJoined, $vars, true);   				
       			} else {
       				//replace double quotes with single quotes
       				$fstr .= str_replace("\"", "'", $arg); 
       			}
       		}   	
       		//add to where clause
       		$where .= "$fstr)";
    		}
      } 
   }   
   
   
   /**
	 * Converts an object into a comma separated list of
	 * text values.
	 */
   protected function flattenResult($obj)
   {
   	$vars = array();
   	if (is_array($obj)) {
   		foreach ($obj as $key => $val) {
   			//print "\nkey='$key', val='$val'\n";
   			$vars = array_merge($vars, $this->flattenResult($val));
   		}
   	} else {
   		$vars[] = $obj;
   	}
   	return $vars;
   }
   
   protected function fillInObject(&$obj, $row)
   {
   	$pvalMap = array();   	
   
   	//group each returned field in a row by it's "parent path",
   	//the path delimited by FQL_OBJECT_PATH_DELIM of the field
   	//name up to the last node
   	foreach ($row as $path => $val) {	
   		
   		$val = $row[$path];
   		$apath = explode(FQL_OBJECT_PATH_DELIM, $path);   		
   		$leaf = $apath[count($apath)-1];
   		   		
   		$parent = implode(FQL_OBJECT_PATH_DELIM, array_slice($apath, 0, count($apath)-1));
   		
   		if ((strlen($leaf) > 4) && (substr($leaf, 0, 4) == "anon")) {
   			//handle anonymous result (result of function call)
   			if (!array_key_exists($parent, $pvalMap)) {
   				$pvalMap[$parent] = array();   				
   			}
   			if (!array_key_exists("anon", $pvalMap[$parent])) {
   				$pvalMap[$parent]["anon"] = array();
   			}
   			$pvalMap[$parent]["anon"][] = $val;
   		} else {
   			//handle simple or complex type
       		if (!array_key_exists($parent, $pvalMap)) {
       			$pvalMap[$parent] = array($leaf => $val);
       		} else {
       			$pvalMap[$parent][$leaf] = $val;
       		}   	
   		}
   	}
   	
   	//take the grouped-together values and explode them
   	//into a structured object
   	foreach ($pvalMap as $pname => $pval) {   	
   		$props = null;
   		if (array_key_exists($pname, $this->m_pathToFieldMap)) {   			
   			//complex type
   			$props = $this->m_configMap[$this->m_pathToFieldMap[$pname]];   			
   		}
   		$this->fillInObjectValue($obj, $pname, $pval, $props);
   	}
   }

   protected function fillInObjectValue(&$obj, $path, $nvPairs, $props = null)
   {
   	$apath = explode(FQL_OBJECT_PATH_DELIM, $path);
   	$rootName = $apath[0];
   	$lastName = $apath[count($apath)-1];   	
   	$curObj = &$obj;
   	foreach ($apath as $ename) {   		
   		if ($ename != $lastName) {   	
   			//make $curObj point to the next child element,
   			//creating it if necessary
       		if (!array_key_exists($ename, $curObj)) {
       			$curObj[$ename] = array();   			
       		}
       		$curObj = &$curObj[$ename];
   		} else {
   		
   			if (!isset($curObj[$ename]))  $curObj[$ename] = array();
   			$isList = false;
   			if ($props != null) {
   				$isList = isset($props["isList"]) ? $props["isList"] : false;
   			}
   			
   			if ($isList) {
   				if ($props["type"] == "complex") {
   				
       				//pre-process nvPairs array
       				foreach ($props["values"] as $valprops) {
       					$valname = $valprops["name"];
       					
       					//explode any non-exploded values
       					$explodeProp = isset($valprops["explode"]) ? $valprops["explode"] : false;
       					if ($explodeProp) {
       						$explodeName = isset($valprops["explodeName"]) ? $valprops["explodeName"] : "";
       						if (strlen($explodeName) == 0) {
       							throw new FQLException("Cannot explode '$valname' without explodeName property specified.");
       						}
       						if (array_key_exists($valname, $nvPairs)) {
    								$sval = $nvPairs[$valname];
    								$sarr = explode(",", $sval);
    								$nvPairs[$valname] = array($explodeName => $sarr);
       						}
       					}
       					
       					$subField = isset($valprops["subField"]) ? $valprops["subField"] : "";
       					//place values in a sub-field if requested
    	   				if (strlen($subField) > 0) {
    	   					if (array_key_exists($valname, $nvPairs)) {
        	   					$sval = $nvPairs[$valname];
        	   					unset($nvPairs[$valname]);
        	   					if (!isset($nvPairs[$subField])) {
        	   						$nvPairs[$subField] = array();
        	   					}
        	   					$nvPairs[$subField][$valname] = $sval;
    	   					}
    	   				}
       				}
   				}
   			
   				//check for duplicates before adding
   				if (!$this->arrayContainsEqualArray($curObj[$ename], $nvPairs)) {
   					$curObj[$ename][] = $nvPairs;
   				}
   			} else {
   				//overwrite any duplicates
   				foreach ($nvPairs as $nname => $nval) {
   					
   					//check to see if simple value is comma-separated and
   					//should be exploded into sub-fields
   					$explodeProp = false;
   					$explodeName = "";
   					if (array_key_exists($nname, $this->m_configMap)) {
   						$props = $this->m_configMap[$nname];
   						$explodeProp = isset($props["explode"]) ? $props["explode"] : $explodeProp;
   						if ($explodeProp) {
   							$explodeName = isset($props["explodeName"]) ? $props["explodeName"] : $explodeName;
   						}
   					}
   					
   					$finalVal = $nval;
   					if ($explodeProp && (strlen($explodeName) > 0)) {   						
   						$earr = explode(",", $nval);
   						$finalVal = array($explodeName => $earr);   						
   					}
   					$curObj[$ename][$nname] = $finalVal;
   					
   				}
   			}
   		}
   		//print "\npath='$path', ename='$ename', val='$val'\n";
   		//print_r($obj);
   	}
   }
   
   /**
	 * $arr1 is a list of associative arrays, this function
	 * checks to see if the associative array $arr2 is already
	 * contained inside of $arr1, returns true if it is, false otherwise.
	 */
   protected function arrayContainsEqualArray($arr1, $arr2)
   {
   	foreach ($arr1 as $indx => $val1) {
   		$areEqual = true;
   		//check to see if $val1 = $arr2
   		foreach ($arr2 as $key => $val2) {
   			//ignore array sub-elements of $arr2 (kind of a bug)
   			if (!is_array($val2)) {   		   			
       			if (isset($val1[$key])) {
       				if ($val1[$key] != $val2) $areEqual = false;
       			} else {
       				if (($val2 != null) || (!empty($val2))) {
       					$areEqual = false;
       				}
       			}
   			}
   		}
   		if ($areEqual)  return true;
   	}
   	return false;
   }
   
   
        
}

?>
