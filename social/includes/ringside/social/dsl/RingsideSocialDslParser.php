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

/* This parser is specific to OpenFBWeb and hence needs a logged in user and an application object available. */
require_once( 'ringside/social/dsl/TagRegistry.php');

/**
 * This parser class is specific to OpenFBWeb.  The relationship
 * between Parser and handlers should be sufficiently separated
 * so a MW parser or other types could be writtent to proejct
 * FBML rendering into any site.
 *
 * The Handlers are only available for OpenFB since there is some
 * data for rendering tags which are not made available vis standard api
 * calls.  However, tags should be implemented to use standard fb apis if
 * possible, hence this could render FBML to any site for different networks.
 *
 * The interface with handlers is
 * Application interface which enables the tags to get access to a client and ask some information about the renderer
 * Handler - the parent handler if there is one.
 *
 * Handlers have no link to the parser calling them.
 * This parser must be called within the context of an OpenFBWeb user interaction.
 *
 * @autho Richard Friedman
 */
class RingsideSocialDslParser {

   private $application;
   private $redirect = null;
   
   public function __construct( $application ) {
      $this->application = $application;      
   }

   private static function _replace_meta_and_links($matches) {
		$result = preg_replace('/<link([^>]*)>/', '<rs:link$1>', preg_replace('/<meta([^>]*)>/', '<rs:meta$1>', $matches[0]));
		return $result;   	
   }
   
   /**
    * This section uses tidy to parse the file
    * Specify configuration
    * 
    * @param string $text string to parse. 
    */
   public function parseString( $text ) {
     $tidy = new tidy;
     $escaped_text = preg_replace(array(
     											'/<script([^>]*)>(.*?)<\/script>/s',
     											'/<link([^>]*)>(.*?)<\/link>/s',
     											'/<link([^>]*)\/>/s',
     											'/<style([^>]*)>(.*?)<\/style>/s'
     											), array(
     										 	'<rs:script$1><![CDATA[$2]]></rs:script>',
     										 	'<rs:link$1><![CDATA[$2]]></rs:link>',
     										 	'<rs:link$1/>',
     										 	'<rs:style$1><![CDATA[$2]]></rs:style>',
     										 	), $text);
     $escaped_text = preg_replace_callback('/<fb:share-button[^>]*>.*<\/fb:share-button>/s',
     													 array('RingsideSocialDslParser', '_replace_meta_and_links'),
     													 $escaped_text);

     	$textToParse = "<rs:social-dsl>$escaped_text</rs:social-dsl>";
     	$tagRegistry = Social_Dsl_TagRegistry::getInstance();     	    					
     	$tagRegistry->scanForNewTags($textToParse);								 
     	$tidy->parseString($textToParse, $tagRegistry->getTidyConfiguration());
     
//      error_log("Before tidy parse:");
//      error_log($escaped_text);
//      error_log("After tidy parse:");
//      error_log($tidy);
     	ob_start();
     
     	try {
      	$this->walk_nodes($tidy->root() );
     	} catch ( Exception $e ) {
     		ob_end_clean();
     		error_log($e->getMessage());
     		error_log($e->getTraceAsString());
     	}
     	$pre_text = ob_get_clean();     	
     	
      if ( empty($pre_text) ) {
     	   return $pre_text;
     	}
     	
     	// We can allow directly <rs:script tags, and replace them with standard script tags
     	// TODO review this and make this a handler? 
     	$final_text = preg_replace(array(
      		'/<rs:script([^>]*)>(.*?)<!\[CDATA\[(.*?)]]>(.*?)<\/rs:script>/s',
      		'/<rs:link([^>]*)>(.*?)<!\[CDATA\[(.*?)]]>(.*?)<\/rs:link>/s',
      		'/<rs:link([^>]*)><\/rs:link>/s',
      		'/<rs:link([^>]*)\/>/s',
      		'/<rs:style([^>]*)>(.*?)<!\[CDATA\[(.*?)]]>(.*?)<\/rs:style>/s',
      ), array(
      		'<script$1>$2$3$4</script>',
      		'<link$1>$2$3$4</link>',
      		'<link$1/>',
      		'<link$1/>',
      		'<style$1>$2$3$4</style>',
      		), $pre_text);
      $result = '';
      if ( strstr(strtolower($text), '<html>')) {
      	// If the input has the HTML wrapper, emit the whole result (minus the rs-div div tag)
      	$result = preg_replace('/<rs:social-dsl>(.*)<\/rs:social-dsl>/s', '$1', $final_text);
      } else {
      	$matches = array();
      	preg_match('/<rs:social-dsl>(.*)<\/rs:social-dsl>/s', $final_text, $matches);
      	if ( !empty( $matches ) && count($matches) > 1 ) 
      	{
      	     $result = $matches[1];
      	}
      }
      
      // Replace truly-empty rs:social-dsl node (tidy does this)
      $result = preg_replace('/<rs:social-dsl *\/>/', '', $result);
      return $result;
   }

   /**
    * There are a few types of tags to consider
    * NO CHILDREN
    * CHILDREN unrelated to Parent
    * CHILDREN need parent
    * PARENT need children
    * PARENT with if/else
    * PARENT with loop
    *
    * @param TidyNode $node
    * @param Handler $parentHandler
    */
   function walk_nodes( $node, &$parentHandler = null )
   {
		$flavorNodeName = ($node->type == TIDY_NODETYPE_TEXT)?'#text':$node->name;

		$tagNamespace = NULL;
		$tagName = NULL;
		$narr = explode(':', $node->name);
		if (count($narr) > 1) {			
			$tagNamespace = $narr[0];
			$tagName = $narr[1];
		} else if (count($narr) > 0) {
			$tagName = $narr[0];
		}
		
		$tagRegistry = Social_Dsl_TagRegistry::getInstance();
		//HTML elements will have $tagNamespace == NULL, if a handler is
		//present and loaded it will still have tag meta info.
		$tagMetaInfo = $tagRegistry->getTagMetaInfo($tagNamespace, $tagName);
		$tagHasHandler = ($tagMetaInfo != NULL);
		$tagIsValidForFlavor = $tagRegistry->isValidForFlavor($flavorNodeName, $this->application->getFlavorContext()->getFlavor());
		//error_log("[parser] name='$tagName', ns='$tagNamespace' flavorName='$flavorNodeName', hasHandler=" . ($tagHasHandler ? 'true' : 'false') . " isValid=" . ($tagIsValidForFlavor ? 'true' : 'false'));
		if (($tagNamespace != NULL) && !$tagHasHandler && ($tagName != 'script') && ($tagName != 'style')) {
			//just keeping this warning here until we're sure TagRegistry changes
			//don't break anything...			
			error_log("[RingsideSocialDslParser] no handler found for $tagNamespace:$tagName!");
		}
		
      if (!empty($node->name) && $tagHasHandler && $tagIsValidForFlavor) {      	
         $handler = $tagMetaInfo->createTagHandlerInstance();         
         $body = $handler->doStartTag( $this->application, $parentHandler, $node->attribute );
         if ( $body === true ) {
            // returning true says call each child then call my doEndTag
            if ( $node->hasChildren() ) {
               foreach($node->child as $child) {
                  $this->walk_nodes($child, $handler);
               }
            }
            $body = $handler->doEndTag( $this->application, $parentHandler, $node->attribute );
         } else if ( $body === false ) {
            // return false says don't process me
             
         } else if ( is_array( $body ) ) {
            // returning an array says ONLY process these tags.
            if ( $node->hasChildren() ) {
               foreach($node->child as $child) {
                  if ( in_array( $child->name, $body ) ) {
                     $this->walk_nodes($child, $handler);
                  }
               }
            }
            $body = $handler->doEndTag( $this->application, $parentHandler, $node->attribute );
         } else {
            if ( $body == 'body' ) {
               // returning the text body says collect the body text and call the doBody handler with the text passed in
               $bodyValue = '';
               if ( $node->hasChildren() ) {
                  foreach($node->child as $child) {
                  	$value = $child->value;
                  	if ( $value[strlen($value)-1] == "\r" ) {
                  		// Trim right-hand carriage return on Windows
                     	$value = substr($value, 0, strlen($value) - 1);
                  	}
                  	
                  	$bodyValue .= $value;
                   }
               }
               $handler->doBody( $this->application, $parentHandler, $node->attribute, $bodyValue );
            	$handler->doEndTag( $this->application, $parentHandler, $node->attribute );
            } else if ( $body == 'print' ) {
               // return 'print' and we print the begin tag, then walk the children, then print end tag.
               $this->printNode( $node );
            } else if ( $body == 'redirect' ) {
               // tell the caller we got a redirect
               $this->redirect = $handler->doBody( $this->application, $parentHandler, $node->attribute, '' );
            } else if ( $body == 'mixed') {
            	
            	$bodyValue = '';
            	if ( $node->hasChildren() ) {
                 	foreach($node->child as $child) {
                 		
                 		$tagName = $child->name;
                 		if ((strlen(trim($tagName)) > 0) && $tagRegistry->hasHandler($tagName)) {
                 			//parse inline tag, append to body
                 			ob_start();
                 			$this->walk_nodes($child, $handler);
                 			$bodyValue .= ob_get_contents();
                 			ob_end_clean();
                 		} else {
                 			//append text to string
                 			$bodyValue .= $child->value;
                 		}
                  	}
             	}
             	
             	$result = $handler->doBody( $this->application, $parentHandler, $node->attribute, $bodyValue );
                $body = $handler->doEndTag( $this->application, $parentHandler, $node->attribute );    
            
            } else {
            
               // return a comma separate list of tags and we pre-process those tags
               // and then call doBody on parent walk other nodes call doEndTag on parent.
               // the do Body will also contain as text all nodes raw text that were not in the list.
               
               $preTags = explode ( ",", $body );
               $bodyValue = '';
               if ( $node->hasChildren() ) {
                  foreach($node->child as $child) {
                     if ( in_array( $child->name, $preTags ) ) {
                        $this->walk_nodes($child, $handler);
                     } else {
                     	$value = $child->value;
                     	if ( $value[strlen($value)-1] == "\r" ) {
                     		$value = substr($value, 0, strlen($value) - 1);
                     	}
                     	$bodyValue .= $value;
                     }
                  }
               }
               
               $result = $handler->doBody( $this->application, $parentHandler, $node->attribute, $bodyValue );
               if ( $result !== true ) {
                  if ( $node->hasChildren() ) {
                     foreach($node->child as $child) {
                        if ( !in_array( $child->name, $preTags ) ) {
                           $this->walk_nodes($child, $handler);
                        }
                     }
                  }
               }
               $body = $handler->doEndTag( $this->application, $parentHandler, $node->attribute );                
            }
         }
         
      } else {
      	$old_do_output = $this->application->getFlavorContext()->doOutput();
      	if (!$tagIsValidForFlavor) {
      		$this->application->getFlavorContext()->setOutput(false);
      	}
        $this->printNode($node);
      	if (!$tagIsValidForFlavor) {
      		$this->application->getFlavorContext()->setOutput($old_do_output);
      	}
      }
   }
   
   /**
    * Print a node and its children without parsing them.
    *
    * @param unknown_type $node
    */
   private function printNode( $node ) {
      $do_output = $this->application->getFlavorContext()->doOutput(); 
   	if ( $node->hasChildren() ) {
         if ( $do_output ) $this->printStartTag( $node );
         foreach($node->child as $child) {
            $this->walk_nodes( $child, $parentHandler );
         }
         if ( $do_output ) { $this->printEndTag( $node ); }
      } else {
      	$value = $node->value;
      	if ( $value[strlen($value)-1] == "\r" ) {
      		$value = substr($value, 0, strlen($value) - 1);
      	}
         if ( $do_output ) echo $value;
      }
   }
   
   /**
    * Print a start tag and its attributes.
    *
    * @param TidyNode $node
    */
   public function printStartTag( TidyNode $node ) {
      
      if ( !empty( $node->name ) ) {
         echo "<{$node->name}";
         if ( !empty( $node->attribute)) {
            foreach ( $node->attribute as $attrib=>$value ) {
               echo " $attrib=\"$value\"";
            }
         }
         echo ">";
      }
   }
   
   public function printEndTag( TidyNode $node ) {
      if ( !empty( $node->name ) ) {
         echo "</{$node->name}>";
      }
   }
   
   public function getRedirect() {
      return $this->redirect;
   }
   
}

?>
