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
 * This class provides a utility to read and manage .ini configuration file settings.
 * This can preserve comments and newlines when writing out ini file contents.
 *
 * @author John Mazzitelli
 */
class M3_Util_IniSettings
{
    private static $COMMENT_CHARS = ';#';

    private $configFileName;
    private $useIncludePath;

    /**
     * Builds the class.  The file will be searched from within the PHP include path.
     *
     * @param $filename configuration file name, as found in include path
     * @param $useIncludePath if true (the default) finds the file somewhere in the include path
     */
    public function __construct($filename = 'ringside.ini', $useIncludePath = true)
    {
        $this->configFileName = $filename;
        $this->useIncludePath = $useIncludePath;
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
        if ($this->useIncludePath)
        {
            $_includePaths = explode(PATH_SEPARATOR, get_include_path() );
            foreach ($_includePaths as $_includePath)
            {
                $_pathName = rtrim($_includePath, '\\/') . '/' . $this->configFileName;
                if (is_file($_pathName))
                {
                    return $_pathName;
                }
            }
    
            reset($_includePaths);
            return rtrim($_includePaths[0], '\\/') . '/' . $this->configFileName;
        }
        else
        {
            return $this->configFileName;
        }
    }

    /**
     * Reads the configuration file and puts the multi-dimensional array data in $r.
     *
     * @param $r the results are placed in this variable as a multi-dimensional array
     * @param $valuesOnly if you only want to get the setting values only, pass in true.
     *                    the default is false, which will return the full structure including
     *                    newline and comment entries in the results array.
     *
     * @return true if successful, false otherwise
     */
    public function readIniFile(&$r, $valuesOnly = false)
    {
        $null = "";
        $r=$null;
        $first_char = "";
        $sec=$null;
        $num_comments = "0";
        $num_newline = "0";

        //Read to end of file with the newlines still attached into $f
        $f = @file($this->getConfigurationPathName());
        if ($f === false)
        {
            return false;
        }
        // Process all lines from 0 to count($f)
        for ($i=0; $i<@count($f); $i++)
        {
            $w=@trim($f[$i]);
            $first_char = @substr($w,0,1);
            if ($w)
            {
                if ((@substr($w,0,1)=="[") and (@substr($w,-1,1))=="]")
                {
                    $sec=@substr($w,1,@strlen($w)-2);
                    $num_comments = 0;
                    $num_newline = 0;
                }
                else if ((stristr(self::$COMMENT_CHARS, $first_char) == true))
                {
                    if (!$valuesOnly)
                    {
                        $r[$sec]["__Comment__".$num_comments]=$w;
                        $num_comments = $num_comments +1;
                    }
                }
                else
                {
                    // Look for the = char to allow us to split the section into key and value
                    $w=@explode("=",$w);
                    $k=@trim($w[0]);
                    unset($w[0]);
                    $v=@trim(@implode("=",$w));
                    
                    // trim end-of-line comments
                    $v = preg_replace('/[ \t]*[' . self::$COMMENT_CHARS . '][^\"\']*$/', '', $v);

                    // look for quotes
                    if ((@substr($v,0,1)=="\"") and (@substr($v,-1,1)=="\""))
                    {
                        $v=@substr($v,1,@strlen($v)-2);
                    }
                    $r[$sec][$k]=$v;
                }
            }
            else
            {
                if (!$valuesOnly)
                {
                    $r[$sec]["__Newline__".$num_newline]=$w;
                    $num_newline = $num_newline +1;
                }
            }
        }

        return true;
    }

    /**
     * Writes out the ini content as defined in the given data. Comments and
     * newlines are preserved, as long as the array passed in was retrieved
     * via readIniFile with the valuesOnly parameter set to false.
     *
     * $iniData is a multi-dimensional array, where the first dimension is keyed
     * on the names of the ini groups (with the global settings - those not in any
     * group - with an empty string key name). The second dimension are the name/value
     * pairs of ini settings.  If a name of an ini setting starts with '__Comment__',
     * the value will be output to the ini file as-is.  If a name of an ini setting
     * starts with '__Newline__', a newline character will be output.
     *
     * @param array $iniData the multi-dimensional array of ini data
     * 
     * @return true if successful, false otherwise
     */
    public function writeIniFile($iniData)
    {
        $content = "";

        foreach ($iniData as $key=>$elem)
        {
            if (is_array($elem))
            {
                if ($key != '')
                {
                    $content .= "[$key]\n";
                }
                 
                foreach ($elem as $key2=>$elem2)
                {
                    if ($this->beginsWith($key2,'__Comment__'))
                    {
                        $content .= "$elem2\n";
                    }
                    else if ($this->beginsWith($key2,'__Newline__'))
                    {
                        $content .= "\n";
                    }
                    else
                    {
                        $content .= "$key2=$elem2\n";
                    }
                }
            }
            else
            {
                $content .= "$key=$elem\n";
            }
        }

        if (!$handle = fopen($this->getConfigurationPathName(), 'wb'))
        {
            return false;
        }

        if (!fwrite($handle, $content))
        {
            return false;
        }

        fclose($handle);
        return true;
    }

    /**
     * Given the name of a section within the .ini file, this will take
     * all the settings in that section and define global variables based on them.
     * The name of the ini setting is the name of the global variable and its
     * value is setting value.
     * 
     * The setting value can have variable substitutions
     * as long as the variable is either already defined in this PHP execution
     * environment or was defined earlier in the .ini file itself. Example:
     * 
     * [globals]
     * myGlobal=one value here
     * myOtherVar=another $myGlobal
     * 
     * will result in the following global variables defined:
     * $myGlobal = one value here
     * $myOtherVar = another one value here
     * 
     * @param $section the name of the section in the .ini file whose name/value
     *                 settings will be converted to global variables
     */
    public function defineGlobals($section = 'globals')
    {
        $this->readIniFile($_definitions, true);
        
        foreach ($_definitions[$section] as $k => $v)
        {
            $_stmt = "\$GLOBALS['$k']=\"$v\";";
            eval($_stmt);
        }

        return;
    }

    private function beginsWith( $str, $sub )
    {
        return substr($str, 0, strlen($sub)) === $sub;
    }
}
?>