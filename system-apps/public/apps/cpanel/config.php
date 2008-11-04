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

$GLOBALS['configSelected'] = 'true';
require_once("header.inc");
include("ringside/apps/cpanel/templates/tabitems.tpl");

require_once("ringside/m3/util/Settings.php");

function echo_localsettings()
{
    $_settings = $GLOBALS['m3client']->configGetLocalSettings();

    if ($_settings)
    {
        echo <<<EOF1
        <table border="1">
           <tr>
              <th>Setting</th>
              <th>Value</th>
           </tr>
EOF1;

        foreach ($_settings as $_nameValues)
        {
            echo <<<EOF2
            <tr>
               <td>{$_nameValues['name']}</td>
               <td>{$_nameValues['value']}</td>
            </tr>
EOF2;
        }

        echo "</table>\n";
    }
    else
    {
        echo "There are no local settings defined.";        
    }
}

function echo_php_ini()
{
    $_settings = $GLOBALS['m3client']->configGetPhpIniSettings();

    if ($_settings)
    {
        echo <<<EOF1
        <table border="1">
           <tr>
              <th>Setting</th>
              <th>Value</th>
           </tr>
EOF1;
    
        foreach ($_settings as $_nameValues)
        {
            echo <<<EOF2
            <tr>
               <td>{$_nameValues['name']}</td>
               <td>{$_nameValues['value']}</td>
            </tr>
EOF2;
        }
    
        echo "</table>\n";
    }
    else
    {
        echo "Cannot read the php.ini file";
    }
}

function echo_php_info()
{
    ob_start();
    phpinfo();
    
    preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
    
    # $matches [1]; # Style information
    # $matches [2]; # Body information
    
    echo "<div class='phpinfodisplay'><style type='text/css'>\n",
        join( "\n",
            array_map(
                create_function(
                    '$i',
                    'return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );'
                    ),
                preg_split( '/\n/', $matches[1] )
                )
            ),
        "</style>\n",
        $matches[2],
        "\n</div>\n";
}

?>
<fb:tabs>
    <fb:tab-item href='config.php?show=localsettings'
                 title='Local Settings'
                 selected='<?php echo (get_request_param('show', 'localsettings') === 'localsettings') ? 'true' : ''; ?>' />
    <fb:tab-item href='config.php?show=phpini'
                 title='php.ini'
                 selected='<?php echo (get_request_param('show') === 'phpini') ? 'true' : ''; ?>' />
    <fb:tab-item href='config.php?show=phpinfo'
                 title='PHP Info'
                 selected='<?php echo (get_request_param('show') === 'phpinfo') ? 'true' : ''; ?>' />
</fb:tabs>
<?php

if (get_request_param('show', 'localsettings') === 'localsettings')
{
    echo_localsettings();
}
else if (get_request_param('show') === 'phpini')
{
    echo_php_ini();
}
else if (get_request_param('show') === 'phpinfo')
{
    echo_php_info();
}
else
{
    echo_localsettings();
}
?>