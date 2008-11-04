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
include_once "LocalSettings.php";

$comments = false;
if(isset($_REQUEST['comments']))
	$comments = true;

$fbPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/api/facebook' );
$restPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/rest' );
$fbmlPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/social/dsl/handlers/fbml' );

print_files(get_ringside_rest() , $fbPath, "Facebook API", $comments);
print_files(get_ringside_rest() , $restPath , "Extension API", $comments);
print_files(get_ringside_fbml(), $fbmlPath, "Social DSL + FBML Handlers", $comments);

/**
 * Prints out a list of files and their comments
 *
 * @param unknown_type $rootDirs
 * @param unknown_type $relPath
 * @param unknown_type $title
 */
function print_files( $rootDirs, $relPath, $title, $comments)
{
	if($comments)
		echo "<div><h2>$title</h2>";
	else
		echo "<div class='api-list' style='float: left;'><h2>$title</h2>";
	
	foreach($rootDirs as $dir)
	{
		if ( !file_exists( $dir . $relPath  )) {
			continue;
		}

		$files = scandir( $dir . $relPath );
		echo '<ul>';
		foreach($files as $file)
		{
			if(!is_dir($file))
			{
				echo '<li><STRONG>'.get_file_name($file).'</STRONG></li>';
				if($comments)
				{
					echo '<li><STRONG>File Path:</STRONG> '.$dir.DIRECTORY_SEPARATOR.$relPath.DIRECTORY_SEPARATOR.$file.'</li>';
					echo '<li>'.getComments($dir.DIRECTORY_SEPARATOR.$relPath.DIRECTORY_SEPARATOR.$file).'</li>';
				}
			}
		}
		echo '</ul>';
	}
	echo '</div>';
}

function get_file_name($f)
{
	$pos = strrpos($f, '.php');
	if($pos > 0) // Then we know it's 4 chars
	{
		$name = substr($f, 0, strlen($f) - 4);

		$pos = strrpos($f, 'Handler');
		if($pos > 0)
		{
			$name = substr($f, 0, strlen($name) - 7);
		}
		return $name;
	}
	return $f;
}

/**
 * Return array of locations where REST calls would be.
 *
 * @return unknown
 */
function get_ringside_rest() {
	$fbPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/api/facebook' );
	$restPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/rest' );

	$rest = array();
	// using a relative path on the file
	$path = explode(PATH_SEPARATOR, get_include_path() );
	foreach ($path as $base) {
		$target = rtrim($base, '\\/') . DIRECTORY_SEPARATOR . $fbPath;
		if (is_dir($target)) {
			$rest[] = $base;
		} else {
			$target = rtrim($base, '\\/') . DIRECTORY_SEPARATOR . $restPath;
			if (is_dir($target)) {
				$rest[] = $base;
			}
		}
	}

	return $rest;
}

function get_ringside_fbml() {
	$fbmlDirs = array();

	// using a relative path on the file
	$fbmlPath = str_replace( "/", DIRECTORY_SEPARATOR, '/ringside/social/dsl/handlers/fbml' );
	$path = explode(PATH_SEPARATOR, get_include_path() );
	foreach ($path as $base) {
		$target = rtrim($base, '\\/') . DIRECTORY_SEPARATOR . $fbmlPath;
		if (is_dir($target)) {
			$fbmlDirs[] = $base;
		}
	}

	return $fbmlDirs;
}

function getComments($file_path)
{
	$source = file_get_contents($file_path);
	$tokens = token_get_all($source);
	foreach ($tokens as $token) {
		if (is_string($token)) {
			// simple 1-character token
			// ignore code tokens
		} else {
			// token array
			list($id, $text) = $token;

			switch ($id) {
				case T_CLASS:
					return;
					break;
				case T_DOC_COMMENT: // This is the only case we care about
					if(!beginsWith($text, '/*************************************'))
					{
						echo '<ul>';
						$head = getTag($text, '/**');
						$author = getTag($text, '@author');
						$return = getTag($text, '@return');
						$a = getTags($text, '@param');

						// Print the results
						if($head && strlen(trim($head)) > 0)
							echo "<li><STRONG>Header:</STRONG> $head</li>";
						if($return && strlen($return) > 0)
							echo "<li><STRONG>Return Value:</STRONG> $return</li>";
						if($author && strlen($author) > 0)
							echo "<li><STRONG>Author:</STRONG> $author</li>";

						echo '<ul>';
						foreach($a as $s)
						{
							echo "<li>$s</li>";
						}
						echo '</ul>';

						echo '</ul>';
					}
					break;

				default:
					// anything else -> ignore
					break;
			}
		}
	}
}

function getTag($str, $tag)
{
	$pos = strpos($str, $tag);
	if($pos === false)
	{
		return false;
	}else
	{
		// Now parse until the next tag @
		$sub = '';
		$str = substr($str, ($pos + strlen($tag)));
		$end = strpos($str, '@');
		if($end === false)
		{
			// No other tags, so parse until we get to */
			$end = strpos($str, '*/');
		}

		if($end === false)
		{
			$sub = $str;
		}else
		{
			$sub = substr($str, 0, $end);
		}

		$sub = str_ireplace('*', '', $sub);
		return $sub;
	}

	return false;
}

function getTags($str, $tag)
{
	$len = strlen($str);
	$pos = strpos($str, $tag);
	$results = array();

	$i = 0;
	while($pos)
	{
		$sub = '';
		$str = substr($str, ($pos + strlen($tag)));
		$end = strpos($str, '@');
		if($end === false)
		{
			// No other tags, so parse until we get to */
			$end = strpos($str, '*/');
		}

		if($end === false)
		{
			$sub = $str;
		}else
		{
			$sub = substr($str, 0, $end);
		}


		$sub = str_ireplace('*', '', $sub);
		if(isset($sub) && strlen($sub) > 0)
			$results[$i++] = $tag.' '.$sub;

		$pos = strpos($str, $tag);
	}

	return $results;
}

function beginsWith($str, $sub)
{
	return (strncmp($str, $sub, strlen($sub)) == 0);
}


?>
