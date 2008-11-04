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
require_once ("ringside/api/bo/Photos.php");

/**
 * Photos.addTag API
 */
class PhotosAddTag extends Api_DefaultRest
{
	/** pid of photo being tagged */
	private $m_pid;
	
	/** The ID of the user being tagged. */
	private $m_tagUid;
	
	/** Some text identifying the person being tagged. */
	private $m_tagText;
	
	/** The horizontal position of the tag, as a percentage from 0 to 100, from the left of the photo. */
	private $m_x;
	
	/** The vertical position of the tag, as a percentage from 0 to 100, from the top of the photo. */
	private $m_y;
	
	/**
	 * A JSON-serialized array representing a list of tags to be added to the photo. If the tags 
	 * parameter is specified, the x, y, tag_uid, and tag_text parameters are ignored. Each tag 
	 * in the list must specify: "x", "y", and either the user id "tag_uid" or free-form 
	 * "tag_text" identifying the person being tagged. An example of this is the string 
	 * {"x":"30.0","y":"30.0","tag_uid":"1234567890"}, {"x":"70.0","y":"70.0","tag_text":"some person"} 
	 */
	private $m_tags;
	
	public function validateRequest()
	{
		$this->m_pid = $this->getRequiredApiParam('pid');
		
		$tags = $this->getApiParam('tags', null);
		if($tags != null)
		{
			$this->m_tags = $tags;
		}else
		{
			$this->assignX();
			$this->assignY();
			
			$tuid = $this->getApiParam('tag_uid', null);
			$ttxt = $this->getApiParam('tag_text', null);
			if($tuid == null && $ttxt == null)
			{
				throw new OpenFBAPIException("Must specify either tag_uid or tag_text.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
			}
			
			if($tuid != null && $ttxt != null)
			{
				throw new OpenFBAPIException("Cannot specify both tag_uid and tag_text.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
			}
			
			$this->m_tagUid = $tuid;
			$this->m_tagText = $ttxt;
		}
	}
	
	private function assignX()
	{
		$x = $this->getApiParam('x', null);
		if($x == null)
		{
			throw new OpenFBAPIException("X coordinate is missing.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$this->m_x = $x;
		
		if($this->m_x < 0 || $this->m_x > 100)
		{
			throw new OpenFBAPIException("The X coordinate must be between 0 and 100.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
		}
	}
	
	private function assignY()
	{
		$y = $this->getApiParam('y', null);
		if($y == null)
		{
			throw new OpenFBAPIException("Y coordinate is missing.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$this->m_y = $y;
		
		if($this->m_y < 0 || $this->m_y > 100)
		{
			throw new OpenFBAPIException("The Y coordinate must be between 0 and 100.", FB_ERROR_CODE_INCORRECT_SIGNATURE);
		}
	}
	
	private function executeOneTag()
	{
		if(isset($this->m_tagUid))
		{
			$subjectId = $this->m_tagUid;
			Api_Bo_Photos::createPhotoTag($this->getPid(), $subjectId, null, $this->getX(), $this->getY(), null);
		}else
		{
			$text = $this->m_tagText;
			Api_Bo_Photos::createPhotoTag($this->getPid(), null, $text, $this->getX(), $this->getY(), null);
		}
	}
	
	private function removeBegEndChars($buf)
	{
		$len = strlen($buf);
		if($len > 0)
		{
			$buf = substr($buf, 1);
		}
		
		$len = strlen($buf);
		if($len > 0)
		{
			$buf = substr($buf, 0, $len - 1);
		}
		
		return $buf;
	}
	
	private function removeCurlyBraces($oneTag)
	{
		// string is {"x":"30.0","y":"30.0","tag_uid":"1234567890"}
		$oneTag = trim($oneTag);
		$len = strlen($oneTag);
		if($len < 2)
		{
			throw new OpenFBAPIException("Invalid length of one tag: [" . $oneTag . "]", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$pos = strpos($oneTag, "{");
		if($pos != 0)
		{
			throw new OpenFBAPIException("Could not find starting '{' in tags string.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$pos = strrpos($oneTag, "}");
		if($pos != $len - 1)
		{
			throw new OpenFBAPIException("Could not find ending '}' in tags string.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		return substr($oneTag, 1, $len - 2);
	}
	
	private function explodeOneAttribute($oneAttribute)
	{
		// "x":"30.0"
		$buf = explode(":", $oneAttribute);
		
		return $buf;
	}
	
	/*
     * Explode a tag string into its elements.  A single tag string looks like:
     * {"x":"30.0","y":"40.0","tag_uid":"1234567890"}
     * {"x":"70.0","y":"70.0","tag_text":"some person"}
     */
	public function explodeOneTag($oneTag)
	{
		$oneArray = array();
		
		$buf = $this->removeCurlyBraces($oneTag);
		// now string looks like: "x":"30.0","y":"40.0","tag_uid":"1234567890"
		

		$elemArray = explode(",", $buf);
		// "x":"30.0"
		// "y":"40.0"
		// "tag_uid":"1234567890"
		foreach($elemArray as $value)
		{
			$oneAttributeArray = $this->explodeOneAttribute($value);
			$name = $this->removeBegEndChars($oneAttributeArray [0]);
			if(strcmp($name, "tag_text") == 0)
			{
				$val = $this->removeBegEndChars($oneAttributeArray [1]);
			}else
			{
				$val = $this->removeBegEndChars($oneAttributeArray [1]);
			}
			$oneArray [$name] = $val;
		}
		
		// now the array looks like
		// array( x=>30.0, y=>40.0, tag_uid->1234567890 ) or 
		// array( x=>30.0, y=>40.0, tag_text=>"some person" ) or 
		return $oneArray;
	}
	
	/*
     * Explode m_tags into an array.
     * {"x":"30.0","y":"30.0","tag_uid":"1234567890"}, {"x":"70.0","y":"70.0","tag_text":"some person"}
     */
	public function explodeTags()
	{
		$bufArray = explode("},", $this->m_tags);
		$len = count($bufArray);
		$tagArray = array();
		$i = 0;
		foreach($bufArray as $row)
		{
			$rowBuf = '';
			if($i < $len - 1)
			{
				$rowBuf = $row . "}";
			}else
			{
				$rowBuf = $row;
			}
			$tagArray [$i] = $this->explodeOneTag($rowBuf);
			$i ++;
		}
		
		return $tagArray;
	}
	
	private function executeTags()
	{
		$tagsArray = $this->explodeTags();
		foreach($tagsArray as $row)
		{
			if(isset($row ["tag_uid"]))
			{
				$subjectId = $row ["tag_uid"];
				Api_Bo_Photos::createPhotoTag($this->getPid(), $subjectId, null, $row['x'], $row['y'], null);
			}else
			{
				$text = $row ["tag_text"];
				Api_Bo_Photos::createPhotoTag($this->getPid(), null, $text, $row['x'], $row['y'], null);
			}
		}
	}

	/**
	 * Execute the Photos.addTag method
	 *
	 * @return 1
	 */
	public function execute()
	{
		$retVal = "1";
		
		if(isset($this->m_tags))
		{
			$this->executeTags();
		}else
		{
			$this->executeOneTag();
		}
		
		return $retVal;
	}
	
	/**
	 * Get the pid of photo being tagged.
	 *
	 * @return unknown The pid of photo being tagged.
	 */
	public function getPid()
	{
		return $this->m_pid;
	}
	
	/**
	 * Get the ID of the user being tagged.
	 *
	 * @return unknown The ID of the user being tagged.
	 */
	public function getTagUid()
	{
		return $this->m_tagUid;
	}
	
	/**
	 * Get the text identifying the person being tagged.
	 *
	 * @return unknown The text identifying the person being tagged.
	 */
	public function getTagText()
	{
		return $this->m_tagText;
	}
	
	/** 
	 * Get the horizontal position of the tag, as a percentage from 0 to 100, from the left of the photo.
	 * 
	 * @return unknown The horizontal position of the tag, as a percentage from 0 to 100, from the left of the photo.
	 */
	public function getX()
	{
		return $this->m_x;
	}
	
	/** 
	 * Get the vertical position of the tag, as a percentage from 0 to 100, from the top of the photo.
	 * 
	 * @return unknown The vertical position of the tag, as a percentage from 0 to 100, from the top of the photo.
	 */
	public function getY()
	{
		return $this->m_y;
	}
	
	/**
	 * Get the tags.
	 * A JSON-serialized array representing a list of tags to be added to the photo. If the tags 
	 * parameter is specified, the x, y, tag_uid, and tag_text parameters are ignored. Each tag 
	 * in the list must specify: "x", "y", and either the user id "tag_uid" or free-form 
	 * "tag_text" identifying the person being tagged. An example of this is the string 
	 * {"x":"30.0","y":"30.0","tag_uid":"1234567890"}, {"x":"70.0","y":"70.0","tag_text":"some person"} 
	 */
	public function getTags()
	{
		return $this->m_tags;
	}
}

?>
