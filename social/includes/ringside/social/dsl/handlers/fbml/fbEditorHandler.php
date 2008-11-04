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
 * Creates a two column form.  <STRONG>NOTE:</STRONG>  Only submits data via POST
 * 
 * @author Ringside Networks
 * @tagName fb:editor
 * @tagRequired string action The POST URL for this form.
 * @tagOptional int width The width of the form in pixels, defaults to 424px.
 * @tagOptional int label width The width of the first column of the form in pixels, defaults to 75px.  <STRONG>NOTE:</STRONG> Cannot be 0, use 1 or greater.
 * @tagChild fb:editor-text
 * @tagChild fb:editor-textarea
 * @tagChild fb:editor-time 
 * @tagChild fb:editor-month 
 * @tagChild fb:editor-date 
 * @tagChild fb:editor-divider 
 * @tagChild fb:editor-buttonset 
 * @tagChild fb:editor-button 
 * @tagChild fb:editor-cancel 
 * @tagChild fb:editor-custom 
 * @return Rendered form with 2 columns
 */
class fbEditorHandler {

   private $wildText = '';
   
   private $form = array();
   private $formText = array();
   
   private $buttons = array();
   private $buttonText = '';
   
   private $cancel = null;
   private $cancelText = '';
   
   private $action = '';
   private $width = '';
   private $labelWidth = '';
   
   function addCustom( $label, $id, $text )  {
      
      $custom = array();
      $custom['type'] = 'custom';
      if ( $label == null ) {
         $label = '';
      }
      $custom['label']=$label;
      $custom['id']=$id;
      if ( $text == null ) {
         $text = '';
      }
      $custom['text']=$text;
      
      $this->form[] = $custom;
      $this->formText[] = "<tr><th style='width:{$this->labelWidth}px'>$label</th><td>$text</td></tr>";
      
   }
   
   function addText( $label, $name, $value, $maxLength, $class ) { 
      
      $text = array();
      $text['type'] = 'text';
      if ( $label == null ) {
         $label = '';
      }
      $text['label'] = $label;
      if ( $name == null ) {
         $name = '';
      }
      $text['name'] = $name;
      if ( $value == null ) {
         $value = '';
      }
      $text['value'] = $value;
      $text['maxlength'] = $maxLength;
      
      $textInput = "<input name='$name' value='$value'";
      if ( !empty( $maxLength )) {
         $textInput .= " maxlength='$maxLength'";
      }
      
      if(!empty($class))
      {
      	$textInput .= " class='$class'";
      }
      $textInput .= ' /> ';
      
      $this->form[] = $text;
      $this->formText[] = "<tr><th style='width:{$this->labelWidth}px'>$label</th><td>$textInput</td></tr>";
      
   }

   function addCheckBox( $label, $name, $value , $checked ) { 
      
      $text = array();
      $text['type'] = 'checkbox';
      if ( $label == null ) {
         $label = '';
      }
      $text['label'] = $label;
      if ( $name == null ) {
         $name = '';
      }
      $text['name'] = $name;
      if ( $value == null ) {
         $value = '';
      } 
      $text['value'] = $value;
      
      $checkText = '';
      if ( $checked != null && strcasecmp("true",$checked)==0 )   {
         $checkText = "checked";
      }
      $textInput = "<input type='checkbox' name='$name' $checkText value='$value' />";
      
      $this->form[] = $text;
      $this->formText[] = "<tr><th style='width:{$this->labelWidth}px'>$label</th><td>$textInput</td></tr>";
      
   }

   function addTextArea( $label, $name, $value, $rows ) { 
      
      $text = array();
      $text['type'] = 'text';
      if ( $label == null ) {
         $label = '';
      }
      $text['label'] = $label;
      if ( $name == null ) {
         $name = '';
      }
      $text['name'] = $name;
      if ( $value == null ) {
         $value = '';
      }
      $text['value'] = $value;
      $text['rows'] = $rows;
      
      $textInput = "<textarea name='$name' ";
      if ( !empty( $rows )) {
         $textInput .= "rows='$rows'";
      }
      
      $textInput .= '>'.$value.'</textarea>';
      
      $this->formText[] = "<tr><th style='width:{$this->labelWidth}px'>$label</th><td>$textInput</td></tr>";
      
   }
   
   function addButton( $value, $name, $class, $id, $src) {
      
      $button = array();
      $button['value']=$value;
      $button['name']=$name;
      $button['class']=$class;
      $button['id']=$id;
      $this->buttons[] = $button;
      
      $classAttrib="";
      if(!is_null($class)){
      	$classAttrib="class='$class' ";
      }

	  $idAttrib="";
      if(!is_null($id)){
      	$idAttrib="id='$id' ";
      }
      
      $srcAttrib = "";
      if(!is_null($src))
      {
      	$srcAttrib = "src='$src'";
      }
      
      if ( $name != null ) { 
         $this->buttonText .= "<input type='submit' value='$value' name='$name' $classAttrib $idAttrib $srcAttrib/>&nbsp;";
      } else {
         $this->buttonText .= "<input type='submit' value='$value' $classAttrib $idAttrib $srcAttrib/>&nbsp;";
      }

   }
   
   function setCancel( $value, $href ) {
      if ( $value == null ) { 
         $value = "Cancel";
      }
      if ( $href == null ) { 
         $href = "#";
      }

      $cancel = array();
      $cancel['value']=$value;
      $cancel['href'] = $href;
      $this->cancel = $cancel;
      $this->cancelText = " or <a href='$href'>$value</a> ";
   }

   function doStartTag( $application, $parentHandler, $args ) {

      $this->action = isset( $args['action'] ) ? $args['action'] : '';      
      $this->width = isset( $args['width'] ) ? $args['width'] : '425';      
      $this->labelWidth = isset( $args['labelwidth'] ) ? $args['labelwidth'] : '75';      
      
      return 'rs:payment-plans,fb:editor-buttonset,fb:editor-text,fb:editor-textarea,fb:editor-month,fb:editor-date,fb:editor-divider,fb:editor-custom,fb:editor-cancel,fb:editor-checkbox';
                  
   }

   function doBody($application, &$parentHandler, $args, &$body ) {
      if ( !empty( $body ) && strlen( trim( $body )) > 0 ) {
         $this->wildText = $body;      
      }
      return true;
   }
      
   /**
    * At this point valid child tags are collected, so should be able
    * to just print the form.
    *  
    *
    * @param unknown_type $application
    * @param unknown_type $parentHandler
    * @param unknown_type $args
    */
   function doEndTag( $application, $parentHandler, $args ) { 

      echo $this->printAsTable( $application );
      
   }

   private function printAsTable( $application ) {
      $out = '';
      
      $out .= '<form action="'.$this->action.'" method="post">';
      $out .= '<table class="editorkit" border="0" cellspacing="0" style="width:'.$this->width.'px">';
      $out .= ' <tr><td colspan="2">' . $this->wildText . '</td></tr>';
      foreach ( $this->formText as $text )
         $out .= $text;
      $out .= ' <tr><th style="width:'.$this->labelWidth.'px"></th><td>'.$this->buttonText.' '.$this->cancelText.'</td></tr>';
      $out .= '</table></form>';
      
      return $out;
   }
   
   public function getType()
   {
  		return 'block';
   }
   
}



?>
