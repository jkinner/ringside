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
require_once('ringside/social/dsl/RingsideSocialDslParser.php');
require_once('MockApplication.php');
/**
 * Tests for the DSL parser. Should NOT include tests of tags (which should be in
 * the appropriate directory under the directory containing this test.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsideSocialDslParserTest extends PHPUnit_Framework_TestCase {
	public function testSimpleStyle() {
		$a = new MockApplication();
		$p = new RingsideSocialDslParser( $a );
		$result = $p->parseString('<script>some script</script><style>some embedded style</style><script>More script</script>');
		$this->assertEquals("<script>some script\n</script><style>some embedded style\n</style><script>More script\n</script>", $result);
	}


	public function testVoomaxerStyle() {
		$a = new MockApplication();
		$p = new RingsideSocialDslParser( $a );
		$result = $p->parseString(<<<EOF
<link href="http://dev.fastlightbeautiful.com:4012/stylesheets/fb_style.css?1207684280" media="screen" rel="stylesheet" type="text/css" />

<style>
/* =LOGO */

.logo { width: 646px; height: 70px; display: block; background: url("http://dev.fastlightbeautiful.com:4012/images/facebook/logo.png") 0 0 no-repeat; text-indent: -5000px; margin-bottom: 10px; clear: both; } 

</style>
<script>

function submit_message_ajax(form_obj) {
  // set spinner
  document.getElementById('spinner').setStyle('display', 'block');

  var ajax = new Ajax();
  ajax.responseType = Ajax.FBML;
  var post_data = form_obj.serialize();
  ajax.post("http://dev.fastlightbeautiful.com:4012/messages?_method=post", post_data);
  ajax.ondone = function(data) {
   $('discussion_pane').setInnerFBML(data);
   document.getElementById('spinner').setStyle('display', 'none');
  }
  ajax.onerror = function(data) {
    element_for_error = form_obj.getPreviousSibling(); // the error div needs to be the previous sibling
    element_for_error.setInnerXHTML("<p>Must enter some text</p>");
    Animation(element_for_error).to('background', '#ffffff').from('background', '#ffff99').go();
    document.getElementById('spinner').setStyle('display', 'none');
  }
}

</script>
EOF
);
		$this->assertEquals(<<<EOF
<link href="http://dev.fastlightbeautiful.com:4012/stylesheets/fb_style.css?1207684280" media="screen" rel="stylesheet" type="text/css"/><style>
/* =LOGO */

.logo { width: 646px; height: 70px; display: block; background: url("http://dev.fastlightbeautiful.com:4012/images/facebook/logo.png") 0 0 no-repeat; text-indent: -5000px; margin-bottom: 10px; clear: both; } 


</style><script>

function submit_message_ajax(form_obj) {
  // set spinner
  document.getElementById('spinner').setStyle('display', 'block');

  var ajax = new Ajax();
  ajax.responseType = Ajax.FBML;
  var post_data = form_obj.serialize();
  ajax.post("http://dev.fastlightbeautiful.com:4012/messages?_method=post", post_data);
  ajax.ondone = function(data) {
   $('discussion_pane').setInnerFBML(data);
   document.getElementById('spinner').setStyle('display', 'none');
  }
  ajax.onerror = function(data) {
    element_for_error = form_obj.getPreviousSibling(); // the error div needs to be the previous sibling
    element_for_error.setInnerXHTML("<p>Must enter some text</p>");
    Animation(element_for_error).to('background', '#ffffff').from('background', '#ffff99').go();
    document.getElementById('spinner').setStyle('display', 'none');
  }
}


</script>
EOF
			, $result);
	}
}
?>