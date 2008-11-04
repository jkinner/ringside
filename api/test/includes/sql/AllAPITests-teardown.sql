<?php 
include_once("ringside/api/config/RingsideApiConfig.php");

$db_name = RingsideApiConfig::$db_name; 

$sqlDelete = <<<DELIMETER_SQL_CLEAN

use $db_name;

DELETE FROM comments;
DELETE FROM album;
DELETE FROM app_prefs;
DELETE FROM app_keys;
DELETE FROM cache_content;
DELETE FROM developer_app;
DELETE FROM events;
DELETE FROM events_members;
DELETE FROM feed;
DELETE FROM friends;
DELETE FROM groups_member;
DELETE FROM mail_messages;
DELETE FROM mail_box;
DELETE FROM mail;
DELETE FROM pages_app;
DELETE FROM pages_fans;
DELETE FROM pages_info;
DELETE FROM pages;
DELETE FROM photo_tag;
DELETE FROM photo;
DELETE FROM users_profile_work;
DELETE FROM users_profile_school;
DELETE FROM users_profile_rel;
DELETE FROM users_profile_personal;
DELETE FROM users_profile_networks;
DELETE FROM users_profile_layout;
DELETE FROM users_profile_econtact;
DELETE FROM pokes;
DELETE FROM shares;
DELETE FROM status;
DELETE FROM status_history;
DELETE FROM users_app;
DELETE FROM users_app_session;
DELETE FROM users_network;
DELETE FROM users_profile_basic;
DELETE FROM users_profile_contact;
DELETE FROM groups;
DELETE FROM schools;
DELETE FROM social_pay_gateways;
DELETE FROM social_pay_subscriptions_friends;
DELETE FROM social_pay_subscriptions;
DELETE FROM social_pay_plans;
DELETE FROM networks;
DELETE FROM default_app;
DELETE FROM developer_app;
DELETE FROM sessions;
DELETE FROM users;
DELETE FROM principal_map;
DELETE FROM principal;
DELETE FROM app;
DELETE FROM rs_trust_authorities;
DELETE FROM favorites;
DELETE FROM favorites_lists;
DELETE FROM items;
DELETE FROM ratings;


DELIMETER_SQL_CLEAN;

mysql_upload_string($sqlDelete) or die( "teardown MySQL error " . mysql_error() );

?>