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

class FQLUtils
{
	
	/**
	 * Takes a string and replaces on any word encased in
	 * brackets ("{varname}") with the value of "varname"
	 * in the array $vars.
	 */
	public static function replaceVariables($str, $vars)
	{
		$arr = self::splitWithTokens("/{|}/", $str);
		
		$newstr = "";
		$closed = true;
		$buf = "";
		foreach ($arr as $tpair) {
		
			$tok = $tpair[0];
			$varname = $tpair[1];
					
			//print "\ntok='$tok', varname='$varname', buf='$buf'\n";
			if ($tok == "{") {
				if (!$closed) {
					throw new FQLException("Expected closing bracket '}' in expression '$str'");
				}				
				$closed = false;
				if (array_key_exists($varname, $vars)) {
					$buf .= $vars[$varname];
				} else {
					throw new FQLException("Unknown variable '$varname' in expression '$str'");
				}				
			} else if ($tok == "}") {
				$closed = true;
				$newstr .= $buf . $varname;
				$buf = "";
			} else {
				$newstr .= $tok . $varname;
			}
		}
		
		return $newstr;
	}
	
	/*
	 * This function splits the string ($str) into tokens
	 * contained in the regular expression $pattern. It returns
	 * an array of token-string pairs, where each pair contains
	 * a token contained in $pattern at index 0, and the non-pattern
	 * string immediately following the token at index 1.
	 */
	public static function splitWithTokens($pattern, $str)
   {
   	$tokarr = array();
   
		$arr = preg_split($pattern, $str, -1, PREG_SPLIT_OFFSET_CAPTURE);
		$indx = 0;
		foreach ($arr as $pair) {
			$nontok = $pair[0];
			$tok = trim(substr($str, $indx, $pair[1]-$indx));
			$indx = $pair[1]+strlen($nontok);
			
			if ((strlen($tok) > 0) || (strlen($nontok) > 0)) {
    			$tokarr[] = array($tok, $nontok);
    			//print "\n'$tok'=>'$nontok'\n";
			}
		}		
		
		return $tokarr;
   }



}

?>
