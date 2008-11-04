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

$GLOBALS['inventorySelected'] = 'true';
require_once("header.inc");
include("ringside/apps/cpanel/templates/tabitems.tpl");

function echo_api_inventory()
{
    $_apis = $GLOBALS['m3client']->inventoryGetApis();

    echo <<<EOF1
    <table border="1">
       <tr>
          <th>Namespace</th>
          <th>API Name</th>
       </tr>
EOF1;

    foreach ($_apis as $_namespace => $_infos)
    {
        echo <<<EOF2
        <tr>
           <td>$_namespace</td>
           <td>
              <table>
EOF2;
        foreach ($_infos as $_apiInfo)
        {
            echo "<tr><td>{$_apiInfo['api_name']}</td></tr>";
        }
        echo <<<EOF3
              </table>
           </td>
        </tr>
EOF3;
    }

    echo "</table>\n";
}

function echo_app_inventory()
{
    $_apps = $GLOBALS['m3client']->inventoryGetApplications();
    echo <<<EOF1
    <table border="1">
       <tr>
          <th>Application Name</th>
          <th>Description</th>
       </tr>
EOF1;

    foreach ($_apps as $_app)
    {
        echo <<<EOF2
        <tr>
           <td>{$_app['name']}</td>
           <td>{$_app['description']}</td>
        </tr>
EOF2;
    }

    echo "</table>\n";
}

function echo_domain_inventory()
{
    $_domains = $GLOBALS['m3client']->inventoryGetNetworks();
    echo <<<EOF1
    <table border="1">
       <tr>
          <th>Domain Name</th>
       </tr>
EOF1;

    foreach ($_domains as $_net)
    {
        echo <<<EOF2
        <tr>
           <td>{$_net['name']}</td>
        </tr>
EOF2;
    }

    echo "</table>\n";
}

function echo_tag_inventory()
{
    $_tags = $GLOBALS['m3client']->inventoryGetTags();
    echo <<<EOF1
    <table border="1">
       <tr>
          <th>Tag Namespace</th>
          <th>Tag Name</th>
       </tr>
EOF1;

    foreach ($_tags as $_tag)
    {
        echo <<<EOF2
        <tr>
           <td>{$_tag['tag_namespace']}</td>
           <td>{$_tag['tag_name']}</td>
        </tr>
EOF2;
    }

    echo "</table>\n";
}

?>
<fb:tabs>
    <fb:tab-item href='inventory.php?show=api'
                 title='APIs '
                 selected='<?php echo (get_request_param('show', 'api') === 'api') ? 'true' : ''; ?>' />
    <fb:tab-item href='inventory.php?show=applications'
                 title='Applications'
                 selected='<?php echo (get_request_param('show') === 'applications') ? 'true' : ''; ?>' />
    <fb:tab-item href='inventory.php?show=tags'
                 title='Tags'
                 selected='<?php echo (get_request_param('show') === 'tags') ? 'true' : ''; ?>' />
    <fb:tab-item href='inventory.php?show=domains'
                 title='Domains'
                 selected='<?php echo (get_request_param('show') === 'domains') ? 'true' : ''; ?>' />
</fb:tabs>
<?php

if (get_request_param('show', 'api') === 'api')
{
    echo_api_inventory();
}
else if (get_request_param('show') === 'applications')
{
    echo_app_inventory();
}
else if (get_request_param('show') === 'tags')
{
    echo_tag_inventory();
}
else if (get_request_param('show') === 'domains')
{
    echo_domain_inventory();
}
else
{
    echo_api_inventory();
}
?>