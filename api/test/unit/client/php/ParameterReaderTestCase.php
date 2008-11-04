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

require_once( 'ringside/application/ParameterReader.php');

class ParameterReaderTestCase extends PHPUnit_Framework_TestCase
{
	public static function provideContextParameters() 
	{
		return array(
			array(
				array(
					'fb_sig_user'			=>		'12345',
					'fb_sig_time'			=>		'1234656789',
					'fb_sig_network_key'	=>		'abc',
					'fb_sig_network_user'	=>	'987'
				),
				'fb_sig',
				array(),
				array(
					'fb_sig'					=> 	array(
															'user'		=>	'12345',
															'time'	=>	'1234656789',
															'network_key'		=>	'abc',
															'network_user'	=>	'987'
														)
				)
			),
		array(
				array(
					'fb_sig_user'			=>		'12345',
					'fb_sig_time'			=>		'1234656789',
					'fb_sig_network_key'	=>		'abc',
					'fb_sig_network_user'	=>	'987'
				),
				'fb_sig',
				array('fb_sig_network'),
				array(
					'fb_sig_network'		=> 	array(
															'key'		=>	'abc',
															'user'	=>	'987'
														),
					'fb_sig'					=> 	array(
															'user'		=>	'12345',
															'time'	=>	'1234656789'
														)
				)
			),
		array(
				array(
					'fb_sig'						=>		'abc12345',
					'fb_sig_user'				=>		'12345',
					'fb_sig_time'				=>		'1234656789',
					'fb_sig_network'			=>		'def98765',
					'fb_sig_network_key'		=>		'abc',
					'fb_sig_network_user'	=>		'987'
				),
				'fb_sig',
				array('fb_sig_network'),
				array(
					'fb_sig_network'		=> 	array(
															'key'		=>	'abc',
															'user'	=>	'987'
														),
					'fb_sig'					=> 	array(
															'user'			=>	'12345',
															'time'			=>	'1234656789',
															'network'		=>	'def98765',
															)
				)
			)
			);
	}
	
	/**
	 * @dataProvider provideContextParameters
	 */
	public function testParameterReader($params, $name, $contexts, $results) {
		$reader = new ContextParameterReader($params, $name);
		$context_results = array();
		foreach ( $contexts as $context )
		{
			 $context_results[$context] = $reader->getContext($context);
		}
		
		$resultKeys = array_keys($results);
		$contextKeys = $contexts;
		array_push($contextKeys, $name);
		sort($resultKeys);
		sort($contextKeys);
		
		$this->assertEquals($resultKeys, $contextKeys);
		foreach ( $contexts as $context ) {
			$this->assertEquals($results[$context], $context_results[$context], "Unexpected difference in context $context: %s");
		}
		$this->assertEquals($results[$name], $reader->getContext($name));
	}
}
?>