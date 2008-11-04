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

/**
 * This class provides a utility to read and manage "configuration files"
 * that are written as .php files. These files are to be used to
 * contain configuration file settings.
 *
 * This can preserve comments, newlines and other PHP code found in the
 * "configuration file" when writing out the .php file contents as best it can.
 *
 * The purpose of this object is to be able to parse simple PHP files that contain
 * simple settings like:
 *
 * <code>
 *    // my variable setting
 *    $var = 1;
 *
 *    # this is a string
 *    $another = "this is a string";
 *
 *    \* C-style
 *       comment
 *     *\
 *    $GLOBALS['blah'] = $var;
 * </code>
 *
 * Essentially, all lines that do not start with $ are not parsed. Those lines with
 * $ in the first column are assumed to be assignment statements and are considered
 * "configuration settings".
 *
 * This object is not smart enough to parse C-style comments that span multiple
 * lines that have statement-like lines in the middle of it like this (using
 * backslach instead of forward slash so this can appear inside of this
 * documentation block):
 * \*
 *     $var = "1";
 *  *\
 * If you want to comment out sections of variable/value lines, do so using
 * // style comments or prefix the comment line with some other character like "*".
 *
 * Note that it is the callers responsibility to provide the enclosing "&lt?php ?>"
 * tag, either in the configuration file or in the array passed to writePhpFile.
 *
 * @author John Mazzitelli
 */
class M3_Util_PhpSettings
{
    const UNPARSED = '__Unparsed__';
    const NEWLINE = '__Newline__';

    private $configFileName;

    /**
     * Builds the class.  The file will be searched from within the PHP include path.
     *
     * @param $filename configuration file name, as found in include path
     */
    public function __construct($filename = 'LocalSettings.php')
    {
        $this->configFileName = $filename;
    }

    /**
     * Returns the filename of the configuration file, minus path information.
     * This is the filename passed to this object's constructor.
     *
     * @return the configuration file name as passed to the constructor
     */
    public function getConfigurationFileName()
    {
        return $this->configFileName;
    }

    /**
     * Returns the full pathname to the configuration file.
     * The include path is searched. If the file does not yet exist,
     * the first directory specified in the include path will be used
     * in the return path.
     *
     * @return string full path to file, as found in include path
     */
    public function getConfigurationPathName()
    {
        $_includePaths = explode(PATH_SEPARATOR, get_include_path() );
        foreach ($_includePaths as $_includePath)
        {
            $_pathName = rtrim($_includePath, '\\/') . '/' . $this->getConfigurationFileName();
            if (is_file($_pathName))
            {
                return $_pathName;
            }
        }

        reset($_includePaths);
        return rtrim($_includePaths[0], '\\/') . '/' . $this->getConfigurationFileName();
    }

    /**
     * Reads the configuration file and puts the associative array data in $results.
     * You can use the $results array to write out the configuration file again via writePhpFile.
     *
     * This does not interpret or evaluate the values, this will provide you with the actual
     * text located after the "=" in the declaration statements that are parsed, excluding
     * end-of-line whitespace and semicolon.  If you actually want to set the variables in
     * scope, use readPhpFileSetRuntimeValues().
     *
     * @param $results the results are placed in this variable as an associative array
     * @param $valuesOnly if you only want to get the setting values only, pass in true.
     *                    the default is false, which will return the full structure including
     *                    newline, unparsed and comment entries in the results array.
     *
     * @return true if successful, false otherwise
     *
     * @see writePhpFile()
     * @see writePhpFileWithRuntimeValues()
     * @see readPhpFileSetRuntimeValues
     */
    public function readPhpFile(&$results, $valuesOnly = false)
    {
        $results = array();
        $_firstChar = "";
        $_numUnparsed = 0;
        $_numNewlines = 0;

        //Read to end of file
        $_fileContent = @file($this->getConfigurationPathName());
        if ($_fileContent === false)
        {
            return false;
        }

        // Process all lines
        $_fileContentLength = @count($_fileContent);
        for ($i=0; $i < $_fileContentLength; $i++)
        {
            $_line = @rtrim($_fileContent[$i], "\n\r\t ;"); // strip end-of-statement semicolon and any preceding wspace

            if ($_line)
            {
                $_firstChar = @substr($_line,0,1);
                if ($_firstChar === '$')
                {
                    // Look for the = char to allow us to split the section into key and value
                    $_keyValue = @explode('=', $_line);

                    // take the variable name, without the leading dollar sign
                    $_varName = @ltrim(@trim($_keyValue[0]), '$');
                    $_varValue = @trim($_keyValue[1]);

                    /* I think we want to keep the value as-is, don't strip quotes, or semicolons
                     // if surrounded by double-quotes, strip them from the value
                     if ((@substr($_varValue,0,1)=='"') and (@substr($_varValue,-1,1)=='"'))
                     {
                     $_varValue=@substr($_varValue,1,@strlen($_varValue)-2);
                     }
                     */

                    $results[$_varName]=$_varValue;
                }
                else
                {
                    if (!$valuesOnly)
                    {
                        $results[self::UNPARSED . $_numUnparsed] = $_fileContent[$i];
                        $_numUnparsed++;
                    }
                }
            }
            else
            {
                if (!$valuesOnly)
                {
                    $results[self::NEWLINE . $_numNewlines] = "\n";
                    $_numNewlines++;
                }
            }
        }

        return true;
    }

    /**
     * Reads the configuration file and puts the associative array data in $results.
     * You can use the $results array to write out the configuration file again via writePhpFile.
     *
     * This will also set the variables by evaluating the values, thus providing you with both
     * the actual text located after the "=" in the declaration statements that are parsed in
     * the given $results array plus setting the actual variables in scope.  If you do not
     * want to set the variables, but only want the actual text as found in the .php file,
     * then use readPhpFile(). If you don't care to get the actual text, then you could
     * simply do a standard php "include" to include the configuration file .
     *
     * Be careful where you call this - make sure all your settings are GLOBALS (i.e. the
     * configuration keys are things like "GLOBALS['foo']".  Otherwise, the variables
     * will only be set within the scope of this function and immediately go out of scope.
     * 
     * @param $results the results are placed in this variable as an associative array
     * @param $valuesOnly if you only want to get the setting values only, pass in true.
     *                    the default is false, which will return the full structure including
     *                    newline, unparsed and comment entries in the results array.
     *
     * @return true if successful, false otherwise
     *
     * @see writePhpFile()
     * @see writePhpFileWithRuntimeValues()
     * @see readPhpFile
     */
    public function readPhpFileSetRuntimeValues(&$results, $valuesOnly = false)
    {
        if (!$this->readPhpFile($results, $valuesOnly))
        {
            return false;
        }

        return include $this->getConfigurationFileName(); // just the filename, include will implicitly find it in include path
    }

    /**
     * Writes out the PHP content as defined in the given data. Comments,
     * unparsed lines and newlines are preserved, as long as the array passed
     * in was retrieved via readPhpFile with the valuesOnly parameter set to false.
     *
     * It is the responsibility of the caller to provide the start and end PHP
     * tags as appropriate ("&lt;?php" and "?>).
     *
     * $phpData is a one-dimensional associative array, where the key/values
     * are the variable names (minus the '$') and their values.
     * If a key starts with self::UNPARSED, the value will be output to the PHP
     * file as-is, with nothing added or removed (this includes newlines; that is
     * the value as given must include a newline).  If a key starts with
     * self::NEWLINE, only a newline character will be output.
     *
     * If you want the configuration file to write values of the actual variable
     * names (as seen during the time and scope of this method call), you will want
     * to use {@link writePhpFileWithRuntimeValues}).
     *
     * @param array $phpData the array of PHP data
     *
     * @return true if successful, false otherwise
     *
     * @see writePhpFileWithRuntimeValues()
     * @see readPhpFile()
     */
    public function writePhpFile($phpData)
    {
        $_content = "";

        foreach ($phpData as $_key=>$_value)
        {
            if ($this->beginsWith($_key, self::UNPARSED))
            {
                $_content .= "$_value";
            }
            else if ($this->beginsWith($_key, self::NEWLINE))
            {
                $_content .= "\n";
            }
            else
            {
                $_content .= "\$$_key=$_value;\n";
            }
        }

        if (!$_fd = fopen($this->getConfigurationPathName(), 'wb'))
        {
            return false;
        }

        if (!fwrite($_fd, $_content))
        {
            return false;
        }

        fclose($_fd);

        return true;
    }

    /**
     * This is the same as {@link writePhpFile()} except it writes out the
     * actual values of the variables, as seen by the runtime PHP environment
     * during the time and scope of this method call. In other words, this will ignore
     * the variable values in the given $phpData array; its keys will be used as the
     * variable names and the actual runtime values of those variables
     * are used.
     *
     * Be careful where you call this - make sure all your settings are GLOBALS (i.e. the
     * configuration keys are things like "GLOBALS['foo']".  Otherwise, the variables
     * will only be looked up within the scope of this function and will not be found.
     *
     * If you want the configuration file to write values of the variables
     * as defined in the values of the given array, you will want to use
     * {@link writePhpFile()}).
     *
     * @param array $phpData the array of PHP data, its variable values are ignored
     *
     * @return true if successful, false otherwise
     *
     * @see writePhpFile()
     * @see readPhpFile()
     */
    public function writePhpFileWithRuntimeValues($phpData)
    {
        // replace all the variable values with their actual runtime values
        foreach ($phpData as $_key=>&$_value)
        {
            if (!($this->beginsWith($_key, self::UNPARSED))
            && !($this->beginsWith($_key, self::NEWLINE)))
            {
                eval("\$_value = \$$_key;");
            }
        }

        // now write it out the normal way
        return $this->writePhpFile($phpData);
    }

    private function beginsWith( $str, $sub )
    {
        return substr($str, 0, strlen($sub)) === $sub;
    }
}
?>