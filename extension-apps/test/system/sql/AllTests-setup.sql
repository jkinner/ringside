<?php 

require_once 'SuiteTestUtils.php';

$sql = <<<DELIMETER_SQL_INSERT

INSERT INTO users (id,username,password) VALUES (81000,'COMMENTS_USER_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (81001,'COMMENTS_USER_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (81002,'COMMENTS_USER_3','MD5HASH');

INSERT INTO app (id,callback_url,api_key,secret_key,name,canvas_url,sidenav_url,isdefault, support_email, canvas_type) VALUES ( 34000,'','comments','comment-secret','comments','','',1, '', 1);

INSERT INTO app (id,callback_url,api_key,secret_key,name,canvas_url,sidenav_url,isdefault, support_email, canvas_type) VALUES ( 34001,'','rackets','rackets-secret','rackets','','',0, '', 1);

DELIMETER_SQL_INSERT;

mysql_upload_string($sql)  or die( " setup MySQL error " . mysql_error() );

?>