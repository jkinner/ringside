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

include_once( 'ringside/social/dsl/handlers/fbml/fbTabsHandler.php' );
include_once( 'ringside/social/dsl/handlers/fbml/fbTabItemHandler.php' );

class fbMultiFriendSelectorHandler {

	public $actiontext;
	public $showborder;
	public $rows;
	public $max;
	public $exclude_ids;
	public $bypass;
	
	private $type;

	function doStartTag( $application, $parentHandler, $args ) {
		$obj = new ReflectionObject( $parentHandler );
		
        $this->type = $parentHandler->type;
		
		return true;
	}

	function doEndTag( $application, $parentHandler, $args ) {
		$client = $application->getClient();
        $uid = $application->getCurrentUser();
        $uids = $client->friends_get( $uid );
        $infos = $client->users_getInfo( $uids, 'first_name,last_name,pic,current_location' );
//$str = <<<heredoc
//<div id="multi-friend-selector">
//      <select id="group-selector">
//          <option>All Friends</option>
//          <option>Network Group 1</option>
//          <option selected="true">Network Group 2</option>
//          <option>Social Network 1</option>
//          <option>Social Network 2</option>
//      </select>
//        <div id="select-all">
//          <input type="checkbox" id="chk-select-all" value="select-all" />
//          <label for="chk-select-all">Select All</label>
//      </div>
//            <div id="search">
//          <input type="text" id="txt-search" />
//      </div>
//heredoc;
//      <!-- static html tabs output -->
//      <div class="tabs"><div class="tabs_left"><ul><li><a href="#" class="selected first">All Friends</a></li><li><a href="#" class=" last">Selected <span id="selectedCount">(0)</span></a></li></ul></div><div class="tabs_right"><ul></ul></div></div>
$str = <<<heredoc
<div id="multi-friend-selector">
heredoc;

        $tabhandler = new fbTabsHandler();
        $tabitem = new fbTabItemHandler();
        $args = array( 'href'=>'#', 'title'=>'View Friends', 'selected'=>'true' );
        $tabitem->doBody( null, $tabhandler, $args, null );
        
        $tabitem = new fbTabItemHandler();
        $selectedHtml='Selected <span id="selectedCount">(0)</span>';
        $args = array( 'href'=>'#', 'title'=>$selectedHtml, 'selected'=>'false' );
        $tabitem->doBody( null, $tabhandler, $args, null );
        
        $tabhandler->doEndTag( null, null, null );
        
//        $str .= <<<heredoc
//            <div id="friends-view-navigation">
//          <div id="alpha-select">
//              <a href="#">A</a> <a href="#">B</a> <a href="#">C</a> <a href="#">D</a> <a href="#">E</a> <a href="#">F</a>
//              <a href="#">G</a> <a href="#">H</a> <a href="#">I</a> <a href="#">J</a> <a href="#">K</a> <a href="#">L</a>
//              <a href="#">M</a> <a href="#">N</a> <a href="#">O</a> <a href="#">P</a> <a href="#">Q</a> <a href="#">R</a>
//              <a href="#">S</a> <a href="#">T</a> <a href="#">U</a> <a href="#">V</a> <a href="#">W</a> <a href="#">X</a>
//              <a href="#">Y</a> <a href="#">Z</a>
//          </div>
//          <div id="selected-tab">
//              <ul>
//                  <li><a href="#">Selected (0)</a></li>
//              </ul>
//          </div>
//      </div><!-- end friends-view-navigation -->
        $str .= <<<heredoc
        <div id="friends-view">
          <ul>
heredoc;
		foreach( $infos as $friend ) {
			$hometown = $friend[ 'current_location' ];
			$city = empty( $hometown[ 'city' ] ) ? '' : $hometown[ 'city' ];
		    $state = empty( $hometown[ 'state' ] ) ? '' : $hometown[ 'state' ];
		    $locationStr = empty( $city ) ? '' : $city;
		    $appendComma = ( !empty( $city ) && !empty( $state ) );
		    $locationStr .= $appendComma ? ', ' : '';
		    $locationStr .= empty( $state ) ? '' : $state;
		//	echo 'hometown: ' . print_r( $hometown, true );
			
		$str .= '<li><a href="#" onclick="onUpdateFbMultiFriendSelected(this)">';// class="friend-selected">
		$str .= '<span class="thumb-square" style="background-image:url(' . $friend[ 'pic' ] . ');">&nbsp;</span>' . "\n";
		$str .=                      '<span class="friend-name">' . $friend[ 'last_name' ] . ', ' . $friend[ 'first_name' ] . '</span><span class="friend-info">' . $locationStr . '</span>' . "\n";
		$str .=                      '<span style="display:none;" class="MultiFriendUid">' . $friend[ 'uid' ]. '</span>'."\n";
		
		
$str .= <<<heredoc
                  </a>
              </li>
heredoc;
}

$str .= <<<heredoc
              </ul>
      </div><!-- end friends-view -->
heredoc;
$str .= '<input style="float:right; margin:10px 0 0 0;" value="Send ' . $this->type . ' Request" onClick="#" type="submit" class="btn-submit" />' . "\n";
$str .= <<<heredoc
</div><!--  end multi-friend-selector -->
heredoc;

      echo $str;
	}
	
	function getType()
   	{
   		return 'block';   	
   	}  	
}
?>
