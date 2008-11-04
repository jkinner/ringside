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

require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ('ringside/api/ServiceFactory.php');
require_once ("ringside/api/bo/App.php");

class AdminSetAppProperties extends Api_DefaultRest
{
    private static $map = null;
    private $m_aid;
    private $m_apiKey;
    private $m_properties;
    private $m_canvasName;

    private static function loadMap()
    {
        if(self::$map == null)
        {
            self::$map['application_id'] = 'RingsideApp.id';
            self::$map['application_name'] = 'RingsideApp.name';
            self::$map['callback_url'] = 'RingsideApp.callback_url';
            self::$map['post_install_url'] = 'RingsideApp.postadd_url';
            self::$map['uninstall_url'] = 'RingsideApp.postremove_url';
            self::$map['ip_list'] = 'RingsideApp.ip_list';
            self::$map['email'] = 'RingsideApp.support_email';
            self::$map['description'] = 'RingsideApp.description';
            self::$map['use_iframe'] = 'RingsideApp.canvas_type';
            self::$map['desktop'] = 'RingsideApp.desktop';
            self::$map['logo_url'] = 'RingsideApp.logo_url';
            self::$map['is_mobile'] = 'RingsideApp.mobile';
            self::$map['default_fbml'] = 'RingsideApp.default_fbml';
            self::$map['default_column'] = 'RingsideApp.default_column';
            self::$map['edit_url'] = 'RingsideApp.edit_url';
            self::$map['sidenav_url'] = 'RingsideApp.sidenav_url';
            self::$map['attachment_action'] = 'RingsideApp.attachment_action';
            self::$map['attachment_callback_url'] = 'RingsideApp.attachment_callback_url';
            //self::$map['message_url'] = 'message_url';
            //self::$map['message_action'] = 'message_action';
            self::$map['about_url'] = 'RingsideApp.about_url';
            //self::$map['private_install'] = 'private_install';
            self::$map['installable'] = 'RingsideApp.deployed';
            self::$map['privacy_url'] = 'RingsideApp.privacy_url';
            //self::$map['help_url'] = 'help_url';
            //self::$map['see_all_url'] = 'see_all_url';
            self::$map['tos_url'] = 'RingsideApp.tos_url';
            self::$map['dev_mode'] = 'RingsideApp.developer_mode';
            //self::$map['preload_fql'] = 'preload_fql';
            self::$map['icon_url'] = 'RingsideApp.icon_url';
            self::$map['canvas_url'] = 'RingsideApp.canvas_url';
            self::$map['isdefault'] = 'RingsideApp.isdefault';
            	
            self::$map['api_key'] = 'RingsideAppKey.api_key';
            self::$map['secret_key'] = 'RingsideAppKey.secret';
            self::$map['author'] = 'RingsideApp.author';
            self::$map['author_url'] = 'RingsideApp.author_url';
            self::$map['author_description'] = 'RingsideApp.author_description';
        }
    }

    public function validateRequest()
    {
        $this->checkRequiredParam('properties');
        $this->checkOneOfRequiredParams(array('app_api_key', 'aid', 'canvas_url'));

        self::loadMap();

        $this->m_apiKey = $this->getApiParam('app_api_key');
        $this->m_aid = $this->getApiParam('aid');
        $this->m_canvasName = $this->getApiParam('canvas_url');
        $this->m_properties = json_decode($this->getApiParam('properties'), true);
        if(empty($this->m_properties) || ! is_array($this->m_properties))
        {
            throw new OpenFBAPIException("The properties must be specified as a valid json entry.", FB_ERROR_CODE_PARAMETER_MISSING);
        }
    }

    public function execute()
    {
    	$appService = Api_ServiceFactory::create('AppService');
        $aid = false;
        if ( null != $this->m_aid )
        {
            $aid = $this->m_aid;
        }
        elseif ( null != $this->m_apiKey )
        {
        	$aid = $appService->getNativeIdByApiKey($this->m_apiKey);
        }
        elseif ( null != $this->m_canvasName)
        {
            $ids = $appService->getNativeIdsByProperty('canvas_url', $this->m_canvasName);
            if (($ids != NULL) && (count($ids) > 0)) {
            	$aid = $ids[0];
            }
        }
         
        if (false !== $aid)
        {
            $this->checkDefaultApp($aid);
            	
            $dbProps = array();
            foreach($this->m_properties as $name=>$val)
            {
                if(isset(self::$map[$name]))
                {
                    $dbName = self::$map[$name];
                    $dbProps[$dbName] = $val;
                }else
                {
                    throw new OpenFBAPIException("No such property '$name'");
                }
            }
            $appService->updateApp($aid, $dbProps);
            return true;
        }
        throw new OpenFBAPIException("Could not find a valid application to update");
    }
}

?>
