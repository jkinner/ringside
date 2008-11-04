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

$GLOBALS['homeSelected'] = 'true';
require_once("header.inc");
include("ringside/apps/cpanel/templates/tabitems.tpl");

require_once("ringside/m3/util/Settings.php");
require_once("ringside/m3/util/File.php");

function echo_ringside_info()
{
    $_settings = $GLOBALS['m3client']->inventoryGetVersionInfo();

    foreach ($_settings as $_group => $_versions)
    {
        echo <<<TOP
    	    <h2>$_group</h2>
    	    <ul>
TOP;
        foreach ($_versions as $_key => $_value)
        {
            if (is_array($_value))
            {
                echo "<li>{$_value['name']}={$_value['version']}</li>";                
            }
            else
            {
                echo "<li>$_key=$_value</li>";
            }
        }

        echo <<<BOTTOM
            </ul>
BOTTOM;
    }
    
}

function echo_errorlog()
{
    $_errorLogPath = M3_Util_Settings::getPhpErrorLogPathName();

    if ($_errorLogPath)
    {
        if (get_request_param("clearerrorlog"))
        {
            if (!M3_Util_File::truncateFile($_errorLogPath))
            {
                echo "<i>Count not truncate error log [$_errorLogPath]</i>";
            }
        }

        echo <<<EOL1
        <h2 align="center">$_errorLogPath</h2>
        <table align="center"><tr><td>
        <form action="index.php">
           <input type="hidden" name="show" value="errorlog"/>
           <input type="hidden" name="clearerrorlog" value="true"/>
           <input type="submit" value="Clear"/>
        </form>
        </td></tr></table>
EOL1;

        echo "<pre>";
        echo htmlentities(file_get_contents($_errorLogPath));
        echo "</pre>";    
    }
    else
    {
        echo "Cannot determine where the error log is";
    }
}

function echo_license()
{
    echo "<pre>";
    echo file_get_contents('license.txt', FILE_USE_INCLUDE_PATH);
    echo "</pre>";
}
?>

<fb:tabs>
    <fb:tab-item href='index.php?show=ringsideinfo'
                 title='Ringside Info'
                 selected='<?php echo (get_request_param('show', 'ringsideinfo') === 'ringsideinfo') ? 'true' : ''; ?>' />
    <fb:tab-item href='index.php?show=errorlog'
                 title='Error Log'
                 selected='<?php echo (get_request_param('show') === 'errorlog') ? 'true' : ''; ?>' />
    <fb:tab-item href='index.php?show=license'
                 title='License'
                 selected='<?php echo (get_request_param('show') === 'license') ? 'true' : ''; ?>' />
</fb:tabs>

<?php
if (get_request_param('show', 'ringsideinfo') === 'ringsideinfo')
{
    echo_ringside_info();
}
else if (get_request_param('show') === 'errorlog')
{
    echo_errorlog();
}
else if (get_request_param('show') === 'license')
{
    echo_license();
}
else
{
    echo_ringside_info();    
}
?>