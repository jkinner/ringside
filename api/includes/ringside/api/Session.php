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

require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideSession.php');
require_once ('ringside/api/dao/tables/RingsideSessionTable.php');

/**
 * PHP session handling with Doctrine
 * 
 * Created on 12.03.2008
 * @license    http://www.opensource.org/licenses/cpl.php Common Public License 1.0
 */

class Session
{

	/**
	 * Open the session
	 *
	 * @return bool
	 */
	public static function open()
	{
		return true;
	}

	/**
	 * Close the session
	 * @return bool
	 */
	public static function close()
	{
		return true;
	}

	/**
	 * Read the session
	 * @param int session id
	 * @return string string of the sessoin
	 */
	public static function read($id)
	{
		$q = Doctrine_Query::create();
		$q->select('session_data')->from('RingsideSession')->where('session = ?');
		$data = $q->execute(array($id));
		
		if(count($data) > 0)
		{
			return $data[0]->session_data;
		}
		
		return '';
	}

	/**
	 * Write the session
	 * @param int session id
	 * @param string data of the session
	 */
	public static function write($id, $data)
	{
		$time = time();
		if(self::is_infinite($id))
		{
			$time = - 1;
		
		}
		
		$session = Doctrine_Manager::getInstance()->getConnectionForComponent('RingsideSession')->getTable('RingsideSession')->findOneBySession($id);
		if(!$session)
		{
			$session = new RingsideSession();
			
		}
		
		$session->session = $id;
		$session->session_expires = $time;
		$session->session_data = $data;
		$ret = $session->trySave();
		
		return $ret;
	}
	
	/*
	 $session = Doctrine::getTable('RingsideSession')->find($id);
		if(! $session)
		{
			$session = new RingsideSession();
		}
		
		$session->session = $id;
		$session->session_expires = $time;
		$session->session_data = $data;
		return  $session->trySave();
	 */

	/**
	 * Destoroy the session
	 * @param int session id
	 * @return bool
	 */
	public static function destroy($id)
	{
		$session = Doctrine::getTable('RingsideSession')->findOneBySession($id);
		return ($session!=null)?$session->delete():false;
	}

	/**
	 * Garbage Collector
	 * @param int life time (sec.)
	 * @return bool
	 * @see session.gc_divisor      100
	 * @see session.gc_maxlifetime 1440
	 * @see session.gc_probability    1
	 * @usage execution rate 1/100
	 *        (session.gc_probability/session.gc_divisor)
	 */
	public static function gc($max)
	{
		$q = Doctrine_Query::create();
		$q->delete('RingsideSession')->from('RingsideSession s')->where("s.session_expires < ? AND s.session_expires >= 0", array(time() - $max));
		return $q->execute();
	}

	public static function mark_infinite($id)
	{
		$session = Doctrine::getTable('RingsideSession')->findOneBySession($id);
		if ($session) {
			$session->session_expires = - 1;
			return $session->trySave();
		}
		return false;
	}

	public static function is_infinite($id)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideSession')->where("session = ? AND session_expires = '-1'");
		$sessions = $q->execute(array($id));
		
		if(count($sessions) > 0)
		{
			return true;
		}
		
		return false;
	}

}

//ini_set('session.gc_probability', 50);
// ini_set('session.save_handler', 'user');
//
// session_set_save_handler(array('Session', 'open'),
//                          array('Session', 'close'),
//                          array('Session', 'read'),
//                          array('Session', 'write'),
//                          array('Session', 'destroy'),
//                          array('Session', 'gc')
//                          );
//
// if (session_id() == "") session_start();
// //session_regenerate_id(false); //also works fine
// if (isset($_SESSION['counter'])) {
//     $_SESSION['counter']++;
// } else {
//     $_SESSION['counter'] = 1;
// }
// echo '<br/>SessionID: '. session_id() .'<br/>Counter: '. $_SESSION['counter'];
//
//
// And don't miss the table dump. ^^
//
// CREATE TABLE IF NOT EXISTS `sessions` (
//   `session` varchar(255) character set utf8 collate utf8_bin NOT null,
//   `session_expires` int(10) int NOT null default '0',
//   `session_data` text collate utf8_unicode_ci,
//   PRIMARY KEY  (`session`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
?>
