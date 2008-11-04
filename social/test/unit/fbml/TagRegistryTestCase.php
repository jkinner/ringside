<?php

require_once 'ringside/social/dsl/TagRegistry.php';

class TagRegistryTestCase extends PHPUnit_Framework_TestCase
{
	public function testDirectorySearch()
	{
		Social_Dsl_TagRegistry::removeCacheFile();
		
		$tagReg = Social_Dsl_TagRegistry::getInstance();
		
		//print_r($tagReg->getAllTagMetaInfo());
		//print_r($tagReg->getTidyConfiguration());
		
		$tmi = $tagReg->getTagMetaInfo('xy', 'awesome-tag');
		$this->assertNotNull($tmi);
				
		$tmi = $tagReg->getTagMetaInfo('ab', 'another-tag');
		$this->assertNotNull($tmi);		
		
		$tmi = $tagReg->getTagMetaInfo('fb', 'name');
		$this->assertNotNull($tmi);
		
		$tmi = $tagReg->getTagMetaInfo(NULL, 'br');
		$this->assertNotNull($tmi);
	}
	
	public function testTidyConfig()
	{
		Social_Dsl_TagRegistry::removeCacheFile();
		
		$tagReg = Social_Dsl_TagRegistry::getInstance();
		
		$tidyConfig = $tagReg->getTidyConfiguration();
		
		$btags = explode(',', $tidyConfig['new-blocklevel-tags']);
		$this->assertTrue(count($btags) > 0);
		$this->assertTrue(in_array('fb:editor', $btags));
		$this->assertTrue(in_array('fb:if-is-app-user', $btags));
		$this->assertTrue(in_array('fb:editor-textarea', $btags));
		$this->assertTrue(in_array('fb:editor-text', $btags));
		$this->assertTrue(in_array('fb:message', $btags));
		$this->assertTrue(in_array('fb:success', $btags));
		$this->assertTrue(in_array('fb:dashboard', $btags));
		$this->assertTrue(in_array('fb:share-button', $btags));
		$this->assertTrue(in_array('fb:if-user-has-added-app', $btags));
		$this->assertTrue(in_array('fb:editor-buttonset', $btags));
		$this->assertTrue(in_array('fb:tabs', $btags));
		$this->assertTrue(in_array('fb:wide', $btags));
		$this->assertTrue(in_array('fb:comments', $btags));
		$this->assertTrue(in_array('fb:editor-custom', $btags));
		$this->assertTrue(in_array('fb:editor-button', $btags));
		$this->assertTrue(in_array('fb:action', $btags));
		$this->assertTrue(in_array('fb:mobile', $btags));
		$this->assertTrue(in_array('fb:editor-checkbox', $btags));
		$this->assertTrue(in_array('fb:if-can-see', $btags));
		$this->assertTrue(in_array('fb:error', $btags));
		$this->assertTrue(in_array('fb:explanation', $btags));
		$this->assertTrue(in_array('fb:editor-cancel', $btags));
		$this->assertTrue(in_array('fb:narrow', $btags));
		$this->assertTrue(in_array('fb:tab-item', $btags));
		$this->assertTrue(in_array('fb:else', $btags));
		$this->assertTrue(in_array('fb:multi-friend-selector', $btags));
		$this->assertTrue(in_array('fb:header', $btags));
		$this->assertTrue(in_array('fb:js-string', $btags));
		$this->assertTrue(in_array('fb:request-form', $btags));
		$this->assertTrue(in_array('rs:link', $btags));
		$this->assertTrue(in_array('rs:social-dsl', $btags));
		$this->assertTrue(in_array('rs:if-has-paid', $btags));
		$this->assertTrue(in_array('rs:flavor', $btags));
		$this->assertTrue(in_array('rs:payment-form', $btags));
		$this->assertTrue(in_array('rs:script', $btags));
		$this->assertTrue(in_array('rs:style', $btags));
		
		$itags = explode(',', $tidyConfig['new-inline-tags']);
		$this->assertTrue(count($itags) > 0);		
		$this->assertTrue(in_array('fb:create-button', $itags));
		$this->assertTrue(in_array('fb:redirect', $itags));
		$this->assertTrue(in_array('fb:action', $itags));
		$this->assertTrue(in_array('fb:request-form-submit', $itags));
		$this->assertTrue(in_array('fb:profile-pic', $itags));
		$this->assertTrue(in_array('fb:name', $itags));
		$this->assertTrue(in_array('fb:help', $itags));
		$this->assertTrue(in_array('fb:userlink', $itags));
		$this->assertTrue(in_array('fb:title', $itags));
		$this->assertTrue(in_array('fb:google-analytics', $itags));
		$this->assertTrue(in_array('fb:user', $itags));
		$this->assertTrue(in_array('fb:profile-action', $itags));
		$this->assertTrue(in_array('fb:time', $itags));
		$this->assertTrue(in_array('fb:iframe', $itags));
		$this->assertTrue(in_array('rs:feed', $itags));
		$this->assertTrue(in_array('rs:authorize', $itags));
		$this->assertTrue(in_array('rs:link', $itags));
		$this->assertTrue(in_array('rs:payment-plans', $itags));
		$this->assertTrue(in_array('rs:meta', $itags));		
		$this->assertTrue(in_array('xy:awesome-tag', $itags));
		$this->assertTrue(in_array('ab:another-tag', $itags));
		$this->assertTrue(!in_array('NULL:br', $itags));
		
		$etags = explode(',', $tidyConfig['new-empty-tags']);
		$this->assertTrue(count($etags) > 0);
		$this->assertTrue(in_array('fb:redirect', $etags));
		$this->assertTrue(in_array('fb:editor-button', $etags));
		$this->assertTrue(in_array('fb:friend-selector', $etags));
		$this->assertTrue(in_array('fb:profile-pic', $etags));
		$this->assertTrue(in_array('fb:name', $etags));
		$this->assertTrue(in_array('fb:editor-cancel', $etags));
		$this->assertTrue(in_array('fb:time', $etags));
		$this->assertTrue(in_array('fb:iframe', $etags));
		$this->assertTrue(in_array('rs:feed', $etags));
		$this->assertTrue(in_array('rs:authorize', $etags));
		$this->assertTrue(in_array('rs:payment-plans', $etags));				
	}
}


?>