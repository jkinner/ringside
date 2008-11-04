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

require_once('ringside/m3/util/Error.php');
require_once('ringside/m3/paging/PageList.php');

/**
 * Static utility methods that provide some basic file functionality that M3 needs.
 *
 * @author John Mazzitelli
 */
class M3_Util_File
{
    /**
     * Given a relative filename, this will search the include path and if the file
     * is found, will return the path to it.  The returned path will point to
     * the file as found in the include path but the path may not be an
     * absolute path.  If you want an absolute path, pass the returned
     * value to the realpath function.
     * 
     * If the file is not found, false is returned.
     * 
     * @return string path to the file, as found in include path,
     *                or false if the file is not found.
     */
    public static function findFileInIncludePath($filename)
    {
        $_includePaths = explode(PATH_SEPARATOR, get_include_path() );
        foreach ($_includePaths as $_includePath)
        {
            $_pathName = rtrim($_includePath, '\\/') . '/' . $filename;
            if (is_file($_pathName))
            {
                return $_pathName;
            }
        }
        
        return false;
    }
    
    /**
     * Retrieves a page of content from the given file, where a page is a given
     * set of rows of content and a "row" of content is a single line in the file,
     * terminated by an EOL character of "\n".
     *
     * This is potentially an expensive function since it will have to scan the entire
     * file to determine the total row count. If the caller already knows the total
     * row count, it should be passed into this method to avoid having to do this
     * total row scan.
     *
     * @param $pathname the name of the file whose contents are to be retrieved
     * @param $pc the page control to determine what is to be retrieved
     * @param $totalRowCount the total number of rows (aka lines) in the file, if known
     *
     * @return M3_Paging_PageList a page of file contents
     */
    public static function getFilePage($pathname, M3_Paging_PageControl $pc, $totalRowCount = null)
    {
        $_lineCount = 0;
        $_rows = array();

        if (is_readable($pathname) && filesize($pathname) > 0)
        {
            $_fd = fopen($pathname, "rb");

            if ($_fd)
            {
                if (flock($_fd, LOCK_EX))
                {
                    $_row = fgets($_fd);
                    if ($_row && $pc->getStartRow() == 0)
                    {
                        $_rows[] = $_row;
                    }

                    // keep going until we reach EOL
                    while (!feof($_fd))
                    {
                        $_lineCount++;

                        // we can abort if we know how many total rows there are and we've passed the end of the page
                        if ((!is_null($totalRowCount)) && ($_lineCount > $pc->getEndRow()))
                        {
                            break;
                        }

                        $_row = fgets($_fd);
                        if ($_row && $_lineCount >= $pc->getStartRow() && $_lineCount <= $pc->getEndRow())
                        {
                            $_rows[] = $_row;
                        }
                    }

                    fclose($_fd); // unlocks the file automatically
                }
                else
                {
                    error_log("Cannot lock the file [$pathname]- can't get line count: " . M3_Util_Error::getLastErrorMessage());
                }
            }
            else
            {
                error_log("Failed to open [$pathname]; cannot get its line count");
            }
        }

        return new M3_Paging_PageList($pc, $_rows, is_null($totalRowCount) ? $_lineCount : $totalRowCount);
    }

    /**
     * This will scan the given file and count the number of lines in the file, where
     * a line consists of string terminated with an EOL of "\n".
     *
     * Use this to count the lines in a potentially large file because this method
     * will not read the entire file in memory. If you know you have a small file,
     * you can simply do "count(file($pathname))" instead of calling this function.
     *
     * @param $pathname the file whose line count is to be retrieved
     *
     * @return int the number of lines in the file; will return 0 if the file is
     *             not readable, does not exist or has a filesize of 0
     */
    public static function getNumberOfLines($pathname)
    {
        $_lineCount = 0;

        if (is_readable($pathname) && filesize($pathname) > 0)
        {
            $_fd = fopen($pathname, "rb");

            if ($_fd)
            {
                if (flock($_fd, LOCK_EX))
                {
                    fgets($_fd); // need this here due to the nature of fgets and its EOF detection

                    while (!feof($_fd))
                    {
                        $_lineCount++;
                        fgets($_fd);
                    }

                    fclose($_fd); // unlocks the file automatically
                }
                else
                {
                    error_log("Cannot lock the file [$pathname]- can't get line count: " . M3_Util_Error::getLastErrorMessage());
                }
            }
            else
            {
                error_log("Failed to open [$pathname]; cannot get its line count");
            }
        }

        return $_lineCount;
    }

    /**
     * Makes the directories for the $pathname, returns true if the directories
     * now exists, false on error. It is assumed all paths in the pathname are
     * to be created as directories - pass in "dirname($pathname)" if $pathname
     * points to a normal file you want to create.
     *
     * @param $pathname the path to create
     * @param $mode the mode to use when creating the directories; the
     *              default is 0644 which is read and write for owner, read for everybody else
     *
     * @return boolean returns TRUE if exists or made or FALSE on failure.
     */
    public static function mkdirRecursive($pathname, $mode = 0644)
    {
        is_dir(dirname($pathname)) || self::mkdirRecursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    /**
     * Removes the directory $pathname and all its child files/subdirectories.
     * Returns true if the directory was deleted, false on error.
     *
     * @param $pathname the path to delete
     *
     * @return boolean returns TRUE if deleted or FALSE on failure.
     */
    public static function rmdirRecursive($pathname)
    {
        if (is_dir($pathname) && !is_link($pathname))
        {
            if ($dh = opendir($pathname))
            {
                while (($sf = readdir($dh)) !== false)
                {
                    if ($sf == '.' || $sf == '..')
                    {
                        continue;
                    }
                    if (!self::rmdirRecursive($pathname.'/'.$sf))
                    {
                        error_log("Could not delete [$pathname/$sf]");
                        return false;
                    }
                }
                closedir($dh);
            }
            return rmdir($pathname);
        }
        return unlink($pathname);
    }

    /**
     * This will lock the given file and append $data to it, releasing the lock once finished.
     * This will ensure the file is created, creating directories if necessary.
     *
     * @param $pathname the file to lock and write to
     * @param $data the data to append to the file
     */
    public static function lockAndAppendFile($pathname, $data)
    {
        self::mkdirRecursive(dirname($pathname));
        $_fd = fopen($pathname, 'ab');
        if (flock($_fd, LOCK_EX))
        {
            if (!fwrite( $_fd, $data))
            {
                error_log("Cannot write to file [$pathname]- throwing out data [$data]: " . M3_Util_Error::getLastErrorMessage());
            }
            fclose($_fd); // unlocks it automatically for us
        }
        else
        {
            error_log("Cannot lock the file [$pathname]- throwing out data [$data]: " . M3_Util_Error::getLastErrorMessage());
        }
        return;
    }

    /**
     * This empties the given file, truncating it to a size of 0.
     *
     * @param $pathname the file to empty
     *
     * @return boolean Returns TRUE on success or FALSE on failure.
     */
    public static function truncateFile($pathname)
    {
        $_fd = fopen($pathname, 'wb+');
        $_result = ftruncate($_fd, 0);
        fclose($_fd);
        return $_result;
    }

    /**
     * Returns a full path to the system's temporary directory location.
     *
     * @return full path to temp dir
     */
    public static function getTempDir()
    {
        if ( function_exists('sys_get_temp_dir') )
        {
            return sys_get_temp_dir(); // this function was introduced in PHP 5.2.1
        }
        else
        {
            // Try to get from environment variable
            if (!empty($_ENV['TMP']))
            {
                return realpath($_ENV['TMP']);
            }
            else if (!empty($_ENV['TMPDIR']))
            {
                return realpath($_ENV['TMPDIR']);
            }
            else if (!empty($_ENV['TEMP']))
            {
                return realpath($_ENV['TEMP']);
            }
            else
            {
                // Detect by creating a temporary file
                // Try to use system's temporary directory as random name shouldn't exist
                $_tempFile = tempnam(md5(uniqid(rand(), true)), '');
                if ($_tempFile)
                {
                    $_tempDir = realpath(dirname($_tempFile));
                    unlink($_tempFile);
                    return $_tempDir;
                }
            }
        }

        // don't know where it is
        return false;
    }

    /**
     * Given a parent path and a child path, this appends them
     * and returns a string to make the two a single pathname.
     *
     * @param $parent the parent path that is prepended to the child path
     * @param $child the child path that is appended to the parent path
     *
     * @return aggregate pathname
     */
    public static function buildPathName($parent, $child)
    {
        // ?? perhaps add an optional param to throw an exception if the file doesn't exist
        $parent = rtrim($parent, '\\/');
        $child = ltrim($child, '\\/');
        return $parent . '/' . $child;
    }
}

?>