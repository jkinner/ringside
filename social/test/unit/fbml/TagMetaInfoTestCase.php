<?php
require_once 'ringside/social/dsl/TagMetaInfo.php';

class TagMetaInfoTestCase extends PHPUnit_Framework_TestCase
{

	public function testNaming()
	{
		$name = 'xySomeTagHandler';
		$this->assertEquals('xy', Social_Dsl_TagMetaInfo::getNamespaceFromClassName($name));
		$this->assertEquals('some-tag', Social_Dsl_TagMetaInfo::getNameFromClassName($name));		
		
		$name = 'AHandler';
		$this->assertTrue(Social_Dsl_TagMetaInfo::getNamespaceFromClassName($name) == NULL);
		$this->assertEquals('a', Social_Dsl_TagMetaInfo::getNameFromClassName($name));
	
		$name = 'ab:some-tag';
		$fnames = Social_Dsl_TagMetaInfo::getFilenamesFromTagName($name);
		$this->assertEquals('abSomeTag.php', $fnames[0]);
		$this->assertEquals('abSomeTagHandler.php', $fnames[1]);
		
		$farr = array('usr', 'local', 'goats', 'moregoats', 'someClass.php');
		$fpath = implode(DIRECTORY_SEPARATOR, $farr);
		$this->assertEquals('someClass', Social_Dsl_TagMetaInfo::getClassnameFromFileName($fpath));				
	}
}


?>