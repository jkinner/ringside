<?php 

require_once 'SuiteTestUtils.php';

$sqlDelete = <<<DELIMETER_SQL_CLEAN

DELETE FROM app;
DELETE FROM pages_app;
DELETE FROM pages_info;
DELETE FROM pages_fans;
DELETE FROM pages;
DELETE FROM feed;
DELETE FROM mail_messages;
DELETE FROM mail_box;
DELETE FROM mail;
DELETE FROM pokes;
DELETE FROM shares;
DELETE FROM events_members;
DELETE FROM events;
DELETE FROM groups_member;
DELETE FROM groups;
DELETE FROM friends;
DELETE FROM users_profile_basic;
DELETE FROM users_profile_contact;
DELETE FROM users_profile_econtact;
DELETE FROM users_profile_networks;
DELETE FROM users_profile_personal;
DELETE FROM users_profile_rel;
DELETE FROM users_profile_school;
DELETE FROM users_profile_work;
DELETE FROM users_app; 
DELETE FROM photo_tag; 
DELETE FROM photo; 
DELETE FROM album; 
DELETE FROM schools;
DELETE FROM networks;
DELETE FROM app; 
DELETE FROM rs_items;
DELETE FROM rs_favorites;
DELETE FROM rs_favorites_lists;
DELETE FROM rs_ratings;
DELETE FROM rs_comments;
DELETE FROM users;

DELIMETER_SQL_CLEAN;

mysql_upload_string($sqlDelete) or die( "teardown MySQL error " . mysql_error() );

?>