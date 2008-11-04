<?php

include 'config.php';

$connect = mysql_connect( $db_ip, $db_user, $db_pass );
if ( $connect === false ) {
   echo "<h1>Can not connect to the database.</h1>";
   echo( "Server : $db_ip " . "<br />");
   echo( "User : $db_user " . "<br />");
   echo( "Edit fbFootprints/config.php to adjust settings" . "<br />" );
   error_log ( "Database Connect Error " . mysql_error() . "<br />");
   return;
}

$db = mysql_select_db( $db_name, $connect );
if ( $db === false ) {
   if ( mysql_query('CREATE DATABASE ' . $db_name, $connect ) ) {
	  mysql_select_db( $db_name, $connect );
      echo( "Database " . $db_name . " creatd successfully ". "<br />" );
   } else {
      echo "<h1>Can not create the database.</h1>". "<br />";
      echo( "Database : $db_name " . "<br />" );
      echo( "Edit fbFootprints/config.php to adjust settings" . "<br />" );
      error_log ( "Database Select Error " . mysql_error(). "<br />" );
      return;
   }
      
   $sql = "CREATE TABLE `footprints` ( `from` int(11) NOT null default '0', `to` int(11) NOT null default '0', `time` int(11) NOT null default '0', KEY `from` (`from`), KEY `to` (`to`) )";
   if ( mysql_query($sql, $connect ) ) {
      echo( "Database table creatd successfully " . "<br />");
   } else {
      echo "<h1>Can not create table.</h1>". "<br />";
      echo( "Database : $db_name " . "<br />" );
      echo( "Edit fbFootprints/config.php to adjust settings" . "<br />" );
      error_log ( "Database Create Table Error " . mysql_error() . "<br />");
      return;
   }
      
} else {
   echo "<h1>Your database is already created for footprints, will not erase it</h1>";
}


?>