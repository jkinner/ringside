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

    require_once( 'LocalSettings.php' );
    require_once( "ringside/social/RingsideSocialServerRender.php");
    require_once("ringside/web/RingsideWebUtils.php");
    require_once("ringside/web/config/RingsideWebConfig.php");
    require_once("ringside/api/clients/RingsideApiClients.php");
    require_once("ringside/apps/developer/DeveloperAppUtils.php");
        
    
     require_once( 'LocalSettings.php' );
     require_once( "ringside/social/RingsideSocialServerTrust.php");
	       
/**
 * @required nid The network id  
 * @required method This should be set to app to work correctly
 * @required app Name of the application that is to be run [footprints, sportsnews, etc]
 * @optional width Width of the iframe
 * @optional height Height of the iframe 
*/ 
	
 
class RingsideSocialServerWidgetRender
{
	
	
    public function execute(&$params)
    {
        
        $scriptOut = "";  

        //Set the canvas for this app
        self::determineAppCanvasUrl($params);

        //Determine if we need to be inside an iframe or not
        //Probably need to configure this at a app level in the future instead of qs!
        if($params['sandboxed']=='false') 
        {
            $scriptOut = self::generateNonSandboxedCode($params);			    
        } else {
            $scriptOut = self::generateSandboxedCode($params);
        } 
       
        return $scriptOut;
        
    }	
        
    //Sets the canvas url for a given application app id
    private static function determineAppCanvasUrl ( &$params){
             
        //get the api_key for the app and retrieve the current canvas
        $admin_rest = RingsideSocialUtils::getAdminClient();                
        $appKey = isset($_REQUEST['api_key'])? $_REQUEST['api_key']: NULL;                               
        $props = $admin_rest->admin_getAppProperties( "canvas_url" , null, NULL, $appKey);
    
        if( $props != null){
          $params['app'] = $props["canvas_url"];
        } else {
            throw new Exception('unknown application key supplied: ' . $params['appKey'] );
        }
        
    }
   
          
    /// Code generateSandboxedCode generates the JavaScript containg an iframe with a url pointing towards render.
    private static function generateSandboxedCode (&$params){     	
        
        //Allow for custimaztaions to the iframe
        $width = "200px";
        $height = "400px";  	
        if($params['width'] != null) $width = $params['width'];
        if($params['height'] != null) $height = $params['height'];
    	
        
        //Determine the render url that the Iframe will be set to
        $renderUrl = RingsideApiClientsConfig::$socialUrl . "/render.php";
    	
        //Build the JavaScript that will output the iframe
        $iframeCode = "document.write(\"<iframe src='%s?%s&app=%s' style='width:%s;height:%s' frameborder='0'></iframe>\");";
        $scriptOut = sprintf($iframeCode, $renderUrl, $_SERVER['QUERY_STRING'], $params['app'], $width, $height);
        
        return $scriptOut;
            
    }
    
    
    ///Method generateNonSandboxedCode generates the JavaScript needed to spit out the HTML in a document.write statement.
    private static function generateNonSandboxedCode (&$params){

        //Get the rendered HTML code for the app
        $server = new RingsideSocialServerRender( );
        $responseHTML = $server->execute( $params );		

        //Need to clean up the code so we can document.wrte it out
        //No line breaks, escape \, and break up script tags
        $bad = array("\n", "\"","<script","</script>");
        $good   = array("", "\\\"","<sc\" + \"ript","</sc\" + \"ript>");	
        $newphrase = str_replace($bad, $good, $responseHTML);	

        //Generate the JavaScript that will write the HTML out onto the page
        //We are in script tags alread so no need for them here
        $scriptOut = 'document.write( "' . $newphrase . '" );';
		
        return  $scriptOut;   	   

    }
    
    

}

?>
