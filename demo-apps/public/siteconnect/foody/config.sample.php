<?php
 /*
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
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

require_once('LocalSettings.php');
require_once('Doctrine/lib/Doctrine.php');

spl_autoload_register(array('Doctrine', 'autoload'));

$quiz_questions = array(
    // Item URL: http://www.foodnetwork.com/recipes/the-cookworks/blueberry-buttermilk-pancakes-recipe2/index.html
    array(
        'id'			=>    1,
        'url'	      =>    'http://www.foodnetwork.com/recipes/the-cookworks/blueberry-buttermilk-pancakes-recipe2/index.html',
        'img'	      =>    'http://img.foodnetwork.com/FOOD/2004/01/12/ckwks_WK1A10_blueberrypncks_e.jpg',
        'name'       =>    'Blueberry Buttermilk Pancakes',
        'courtesy'	=>    'The Cookworks'
    ),
    array(
        'id'			=>    2,
    	  'url'	      =>    'http://www.foodnetwork.com/recipes/emeril-lagasse/thai-shrimp-curry-recipe/index.html',
        'img'	      =>    'http://img.foodnetwork.com/FOOD/2004/04/19/em1g65_thai_shrimp_curry_e.jpg',
        'name'       =>    'Thai Shrimp Curry',
        'courtesy'	=>    'Emeril Lagosse'
    ),
    array(
        'id'			=>    3,
    	  'url'	      =>    'http://www.foodnetwork.com/recipes/rachael-ray/greek-meatballs-in-wine-sauce-recipe/index.html',
        'img'	      =>    'http://img.foodnetwork.com/FOOD/2003/10/20/tm1b02_greek_meatballs_e.jpg',
        'name'       =>    'Greek Meatballs in Wine Sauce',
        'courtesy'	=>    'Rachael Ray'
    ),
    array(
        'id'			=>    4,
    	  'url'	      =>    'http://www.foodnetwork.com/recipes/throwdown-with-bobby-flay/bourbon-street-buffalo-wings-recipe/index.html',
        'img'	      =>    'http://img.foodnetwork.com/FOOD/2007/07/06/BT0211_Bourbon_Street_Buffalo_Wings_e.jpg',
        'name'       =>    'Bourbon Street Buffalo Wings',
        'courtesy'	=>    'Bobby Flay'
    ),
    array(
        'id'			=>    5,
    	  'url'	      =>    'http://www.foodnetwork.com/recipes/throwdown-with-bobby-flay/bourbon-street-buffalo-wings-recipe/index.html',
        'img'	      =>    'http://img.foodnetwork.com/FOOD/2004/02/25/bw2b07_hambugers1_e.jpg',
        'name'       =>    'Hamburgers',
        'courtesy'	=>    'Tyler Florence'
    ),
);

if ( isset($_REQUEST['fb_sig_nid']) && $_REQUEST['fb_sig_nid'] == 'foodnetwork.com' ) {
    $api_key = 'API_KEY';
    $secret = 'SECRET';
} else {
    error_log("Unknown network ".$_REQUEST['fb_sig_nid']." calling foody app");
}

$db_url="mysql://USER:PASSWD@HOST:PORT/foody";

$GLOBALS['facebook_config']['debug']= false;

?>