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
require_once 'ringside/social/dsl/RingsideSocialDslParser.php';
require_once 'MockApplication.php';
require_once 'MockClient.php';

class fbEditorTestCase extends PHPUnit_Framework_TestCase {

	public static function providerTestEditor() {

	   $action = array();
	   $action['action'] = "newshoe.php";
	   $action['labelwidth']= "100";
	   
	   $items = array();
	   $item = array();
	   $item['type']='custom';
	   $item['label']='Shoe Brand';
	   $item['text']= "<select name='brand'><option>nike</option><option>chess</option></select>" ;
      $items[] = $item;
      $item = array();
      $item['type']='text';
      $item['label']='Shoe Name';
      $item['name']='name';
      $item['value']='';
      $items[] = $item;
      
      $buttons = array();
      $button = array();
      $button['value'] = 'Add';
      $buttons[]= $button;
      
	   $case = '<fb:editor action="newshoe.php" labelwidth="100">
  <fb:editor-custom label="Shoe Brand"><select name="brand"><option>nike</option><option>chess</option></select></fb:editor-custom>
<fb:editor-text label="Shoe Name" name="name" value=""/>
<fb:editor-buttonset>
<fb:editor-button value="Add"/>
</fb:editor-buttonset>
</fb:editor>';

	   $expected = self::makeExpected( 'newshoe.php', null, '100', null, $items, $buttons, null );
	   
		return array(
        	array( $case, $expected )
      );
	}
   
   /**
    * @dataProvider providerTestEditor
    */
   public function testFbEditor ( $parseString , $expected ) {
        $ma = new MockApplication();
        $ma->client = new MockClient();
        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );

        $results = str_ireplace( "\r", "",  $results );
        $results = str_ireplace( "\n", "",  $results );
        $results = str_ireplace( "\t", "",  $results );
        $results = str_ireplace( " ", "", $results );
        $results = str_ireplace( "'", "\"",  $results );
        
        $expected = str_ireplace( "\r", "",  $expected );
        $expected = str_ireplace( "\n", "",  $expected );
        $expected = str_ireplace( "\t", "",  $expected );
        $expected = str_ireplace( " ", "", $expected );
        $expected = str_ireplace( "'", "\"",  $expected );
        
        $this->assertEquals(  $expected , $results , "$expected != $results" );
   }

   public function testFbEditorCustomAjaxy() {
   	$ma = new MockApplication();
      $ma->client = new MockClient();
        
      $parser = new RingsideSocialDslParser( $ma );

      $parseString = '<fb:editor action="register.php" width="600" labelwidth="450" > 
<fb:editor-text label="Full Name" name="name" value=""/> 
<fb:editor-text label="Email" name="email" value=""/> 
<fb:editor-custom> 
                     <input type="password" class="inputpassword" id="reg_passwd__" 
name="reg_passwd__" value="" 
onkeyup="update_strength(\'reg_passwd__\',\'reg_passwd__strength_display__\')"
autocomplete="off" /> 
<div style="display:block;" id="reg_passwd__strength_display__" class="tips">Password strength</div> 
</fb:editor-custom> 

<fb:editor-buttonset> 
<fb:editor-button name="submit_button" value="Submit" /> 
<fb:editor-cancel/> 
</fb:editor-buttonset> 
</fb:editor>';
      
      $results = $parser->parseString( $parseString );
   	
      $expected = '<form action="register.php" method="post">
  <table class="editorkit" border="0" cellspacing="0" style="width:600px">
 <tr><td colspan="2" /></tr>
<tr><th style="width:450px">Full Name</th><td><input name="name" value=""  /> </td></tr>
<tr><th style="width:450px">Email</th><td><input name="email" value=""  /> </td></tr>
<tr><th style="width:450px" /><td><input type="password" class="inputpassword" id="reg_passwd__" name="reg_passwd__" value="" onkeyup="update_strength(\'reg_passwd__\',\'reg_passwd__strength_display__\')" autocomplete="off" /><div style="display:block;" id="reg_passwd__strength_display__" class="tips">Password strength</div>
</td></tr>
 <tr><th style="width:450px" /><td><input type="submit" value="Submit" name="submit_button" />   or <a href="#">Cancel</a> </td></tr>
</table>
</form>';
      
      // For whatever reason, spacing and formatting matters; and introducing newlines makes the results of a failure readable.
   	$this->assertXmlStringEqualsXmlString( $expected, preg_replace(array(',</tr>,',',<table([^>]*)>,'), array("</tr>\n","<table\$1>\n"), str_replace('&nbsp;', ' ', $results) ) );
   }
   
   public function testFbEditorCustomHrule() {
   	$ma = new MockApplication();
      $ma->client = new MockClient();
        
      $parser = new RingsideSocialDslParser( $ma );

      $parseString = '<fb:editor action="edit_app.php" labelwidth="150">		
<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
</fb:editor>';
      
      $results = $parser->parseString( $parseString );
   	
      $expected = '<form action="edit_app.php" method="post">
  <table class="editorkit" border="0" cellspacing="0" style="width:425px">
 <tr><td colspan="2" /></tr>
<tr><th style="width:150px"></th><td><br />
<hr />
<br /></td></tr>
 <tr><th style="width:150px"></th><td> </td></tr>
</table>
</form>';
      
      // For whatever reason, spacing and formatting matters; and introducing newlines makes the results of a failure readable.
   	$this->assertXmlStringEqualsXmlString( $expected, preg_replace(array(',</tr>,',',<table([^>]*)>,'), array("</tr>\n","<table\$1>\n"), str_replace('&nbsp;', ' ', $results) ) );
   }
   
   public static function makeCase( $action, $items, $buttons, $cancel ) {
      
//      $actionAction = "action='{$action['action']}'" ;
//      $actionWidth = isset( $action['labelwidth'] ) ? "labelwidth='{$action['labelwidth']}'" : '';
//      $out = "<fb:editor $actionWidth $actionWidth >"
//
//      foreach ( $items as $item )
//  <fb:editor-custom label="Shoe Brand">
//</fb:editor-custom>
//<fb:editor-text label="Shoe Name" name="name" value=""/>
//<fb:editor-buttonset>
//<fb:editor-button value="Add"/>
//</fb:editor-buttonset>
//
//      $out .= "</fb:editor>";
//      return $out;      
   }
   
   public static function makeExpected( $action, $width, $labelWidth, $wildText, $items, $buttons, $cancel ) {
      
      if ( empty( $width ) ) {
         $width =  '425';
      }
      
      if ( empty( $labelWidth) ) {
         $labelWidth = '75';
      }
      
      if ( empty( $wildText ) ) { 
         $wildText = '';
      }
      
      $out = '';
      $out .= '<form action="'.$action.'" method="post">';
      $out .= '<table class="editorkit" border="0" cellspacing="0" style="width:'.$width.'px">';
      $out .= ' <tr><td colspan="2">' . $wildText . '</td></tr>';
      foreach ( $items as $item ) {
         if ( $item['type'] == 'text') { 
            $out .= self::getText( $item, $labelWidth );
         } else if ( $item['type'] == 'custom') {
            $out .= self::getCustomText( $item, $labelWidth );
         }
      }
      $buttonText = self::getButtonText( $buttons );
      $cancelText = self::getCancelText( $cancel );
      $out .= ' <tr><th style="width:'.$labelWidth.'px"></th><td>'.$buttonText.' '.$cancelText.'</td></tr></table>';
      $out .= '</form>';
      
      return $out;      
   }

   public static function getText( $item , $labelWidth)  { 
      
      $label = (isset($item['label'])?$item['label']:'');
      $name = (isset($item['name'])?$item['name']:'');
      $value = (isset($item['value'])?$item['value']:'');
      $maxlength = (isset($item['maxlength'])?$item['maxlength']:'');
      
      $textInput = "<input name='$name' value='$value' ";
      if ( !empty( $maxLength )) {
         $textInput .= "maxlength='$maxLength'";
      }
      $textInput .= ' /> ';
      
      return "<tr><th style='width:{$labelWidth}px'>$label</th><td>$textInput</td></tr>";
      
   }
   
   function getCustomText( $item , $labelWidth )  {
      
      $label = (isset($item['label'])?$item['label']:'');
      $id = (isset($item['id'])?$item['id']:'');
      $text = (isset($item['text'])?$item['text']:'');

      return "<tr><th style='width:{$labelWidth}px'>$label</th><td>$text</td></tr>";
      
   }   
   
   public static function getButtonText( $buttons ) {
      
      $buttonText = '';
      
      foreach ( $buttons as $button ) {
         $value = (isset($button['value'])?$button['value']:'');
         $name = (isset($button['name'])?$button['name']:null);

         if ( $name != null ) {
            $buttonText .= "<input type='submit' value='$value' name='$name' />&nbsp;";
         } else {
            $buttonText .= "<input type='submit' value='$value'  />&nbsp;";
         }

      }
      return $buttonText;
   }
   
   public static function getCancelText( $cancel) {

      if ( empty($cancel)) { 
         return '';
      }
      
      $value = (isset($button['value'])?$button['value']:'Cancel');
      $href = (isset($button['href'])?$button['href']:"#");
      
      return " or <a href='$href'>$value</a> ";
   }   
   
}
?>
