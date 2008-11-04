<?php 
include_once("ringside/api/config/RingsideApiConfig.php");
include_once("ringside/social/config/RingsideSocialConfig.php");
$location2051 = json_encode( array( 'street'=>'1 chuck drive', 'city'=>'chucktown', 'country'=>'usa' ) );
$hours2051 = json_encode( array( 'mon_1_open'=>1212312, 'mon_1_close'=>12123332 ) );
$parking2051 = json_encode( array( 'street'=>false, 'lot'=>true ) );
$db_name = RingsideApiConfig::$db_name;
$network_key = RingsideSocialConfig::$apiKey;

$sql = <<<DELIMETER_SQL_INSERT

use $db_name;

/**
 * Rules for writing data scripts. 
 * - For each testCase or specific group of test cases use your own ID spectrum. 
 * User Profile : 1
 * Pages : 2000
 * Friends : 3000
 * Events : 4000
 * Groups : 5000 
 * User : 6000
 * Fbml : 7000
 * Auth : 8000
 * Admin : 11000
 * Notifications : 12000
 * Feeds : 16000
 * Client API: 17000 
 * Comments : 34000
 * Payments: 36000
 *
 * - Outside of a set there should be no dependencies or ordering. 
 * - Remember to add new tablse to the -clean.sql script as well.
 */

INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url) VALUES ('$network_key','$network_key-name','$network_key-auth1','$network_key-login1','$network_key-canvas1','$network_key-web1','$network_key-social1','$network_key-authclass1','$network_key-postmap1');

/**
 * pages - 2000
 */
/** PAGE TESTING ID 2000 **/
INSERT INTO users (id,username,password) VALUES (2000,'PAGES_USER_ADMIN_FAN','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2001,'PAGES_USER_ADMIN','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2002,'PAGES_USER_FAN','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2003,'PAGES_USER_FAN','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2004,'PAGES_USER_FAN','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2009,'PAGES_USER_WITH_FRIENDS_NO_PAGES','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2010,'PAGES_USER_NOT_FRIEND_NO_PAGES','MD5HASH');
INSERT INTO users (id,username,password) VALUES (2011, 'PAGES_GETINFO_ADMIN_FAN' , 'PASS' );
INSERT INTO users (id,username,password) VALUES (2012, 'PAGES_GETINFO_FANOF_1AND3' , 'PASS' );
INSERT INTO users (id,username,password) VALUES (2013, 'PAGES_GETINFO_FANOF_1AND2_NOTFRIEND' , 'PASS' );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2009 ,2000 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2009 ,2001 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2009 ,2002 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2009 ,2003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2009 ,2004 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2010 ,2000 ,null ,1 ,0 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2002 ,2010 ,null ,1 ,0 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2011 ,2012 ,null ,1 ,2 );

INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2001, 2000, 'PAGE_FOUR', 'MUSICIAN');
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2200, 2000, 'PAGE_ONE', 'MUSICIAN');
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2201, 2000, 'PAGE_TWO_NO_APPS', 'BAND');
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2202, 2000, 'PAGE_THREE_NO_FANS', 'MUSICIAN');

INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2200, 2400, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2200, 2401, 0 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2200, 2402, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2202, 2401, 1 );

INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2200, 2000, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2200, 2001, 1, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2200, 2002, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2200, 2003, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2200, 2004, 0, 1 );

INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2100, 1, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2103, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2104, 0, 1 );

/* Page one for PAGE_INFO, normal fields, has fans, has app */
INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 2050, 2100, 'CRAZYONLINE', 'http://www.picit.com/image1.jpg', 'ONLINE_STORE');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'website', 'http://mycrazystore.com/' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'company_overview', 'Crazy guys with crazy store' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'mission', 'A long mission with a nice vision' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'products', 'A b C and D ' );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2050, 2011, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2050, 2012, 1, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2050, 2013, 0, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2050, 2401, 1 );
 
/* Page two for PAGE_INFO with json fields, has fans, has app */
INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 2051, 2100, 'BIG BEAUTY', 'http://www.picit.com/image1.jpg', 'HEALTH_BEAUTY');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2051, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'location', '$location2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'parking', '$parking2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'hours', '$hours2051', 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2051, 2011, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2051, 2012, 0, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2051, 2013, 0, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2051, 2401, 1 );

/* Page three for PAGE_INFO with json fields, has fans, but does NOT have app */
INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 2052, 2100, 'ROCK ON', 'http://www.picit.com/image1.jpg', 'BAND');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2052, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2052, 'location', '$location2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2052, 'parking', '$parking2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2052, 'hours', '$hours2051', 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2052, 2011, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2052, 2012, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2052, 2013, 0, 0 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2052, 2401, 0 );

/**
 * Friends - 3000
 */
INSERT INTO users (id,username,password) VALUES (3000,'FRIENDS_NO_FRIENDS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3001,'FRIENDS_ONE_FRIEND','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3002,'FRIENDS_THREE_FRIENDS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3003,'AMIGO_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3004,'AMIGO_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3005,'AMIGO_3','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3006,'FRIENDS_NOT_FRIENDS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3010,'ALIST_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3011,'ALIST_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3012,'ALIST_3','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3013,'ALIST_4','MD5HASH');

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3001 ,3003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3002 ,3003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3002 ,3004 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3006 ,3002 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3006 ,3001 ,null ,1 ,0 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3006 ,3003 ,null ,1 ,3 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3006 ,3005 ,null ,1 ,2 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3010 ,3011 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3012 ,3010 ,null ,1 ,2 );

INSERT INTO users (id,username,password) VALUES (3021,'FRIEND_WITHAPP1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3022,'FRIEND_WITHAPP2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3023,'FRIEND_NO_APP','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3024,'NOTFRIEND_WITHAPP','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3025,'FRIEND_WITHAPP3','MD5HASH');

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3021 ,3022 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3021 ,3023 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3021 ,3025 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (3022 ,3023 ,null ,1 ,2 );

INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 3100, 3021, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 3100, 3022, 1  );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 3100, 3024, 1  );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 3100, 3025, 1  );

 
/** 
 * Groups - 5000
 */
INSERT INTO users (id,username,password) VALUES (5000,'GROUP_CREATOR','MD5HASH');
INSERT INTO users (id,username,password) VALUES (5001,'GROUP_USER_NOGROUPS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (5002,'GROUP_USER_ONE_GROUP','MD5HASH');
INSERT INTO users (id,username,password) VALUES (5003,'GROUP_USER_THREE_GROUPS','MD5HASH');

INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (5100 ,'GROUP_ZERO' ,0 , 1, null ,5000 );
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (5101 ,'GROUP_ONE' ,0 , 1, null ,5000 );
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (5102 ,'GROUP_TWO' ,0 , 1, null ,5000 );

INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 5100 ,5002 ,0,1 ,1 ,0 , null );
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 5100 ,5003 ,0,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 5101 ,5003 ,0,1 ,0 ,0 , null );
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 5102 ,5003 ,0,0 ,1 ,0 , null );

/* Testing User Profiles */
INSERT INTO schools (id,name,school_type) VALUES (1,'Central High',1);
INSERT INTO schools (id,name,school_type) VALUES (2,'Temple University',2);
INSERT INTO schools (id,name,school_type) VALUES (3,'Georgia Tech',2),(4,'CalState LA',2);
INSERT INTO networks (id,name) VALUES (1,'Philadelphia');
INSERT INTO networks (id,name) VALUES (2,'Arts and Crafts');
INSERT INTO networks (id,name) VALUES (3,'Northeast High');
INSERT INTO users (id,username,password) VALUES (2,'user1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (3,'USER_WITH_MISSING_FIELDS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4,'SimpleUser','MD5HASH');

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2 ,3 ,now(), 1, 2 );

REPLACE INTO status (uid,aid,status,modified) VALUES (2, 17100, 'CooCoo for Coco Puffs',NOW());
INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time, pic_url,pic_small_url,pic_big_url,pic_square_url  ) VALUES (2,'Test1','User1','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'At peace with the world','2006-07-09 00:00:00', 'http://no.such.url/pic.jpg','http://no.such.url/pic_small.jpg','http://no.such.url/pic_big.jpg','http://no.such.url/pic_square.jpg');
INSERT INTO users_profile_contact (user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current) VALUES (2,'','','48173 Main St.','Bowmont','NJ','USA','00181','',1,0),(2,'222-333-4444','111-222-3333','1234 56th St., Apt. 7','Eightown','NI','USA','87654','http://www.2coders1port.com',0,1);
INSERT INTO users_profile_networks (user_id, network_id) VALUES (2,1),(2,2),(2,3);
INSERT INTO users_profile_personal (user_id, activities, interests, music, tv, movies, books, quotes, about) VALUES (2,'Boating, pumpkin carving, procuring comestibles, etc. ','Music,sports,tv guide,nuclear physics','Miles Davis,Brittney Spears','Telemundo','Snakes on a plane','Snakes on a plane, the book','Nationalism is an infantile disease - Albert Einstien','About me - nothing!');
INSERT INTO users_profile_rel (user_id,status,alternate_name,significant_other,meeting_for,meeting_sex) VALUES (2,0,'',0,'Random Play,Whatever I can get','M,F');

INSERT INTO users_profile_school (`id` ,`user_id`, `school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL,2,2, '', 1999,'Communications,Philosophy');
INSERT INTO users_profile_school (`id` ,`user_id`, `school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL,2,3, '', 2006,'Rocket Science,Awesomeness');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL , '2', 'Spacely Sprockets', 'Sprocket Engineer', 'Now is the time on sprockets when we dance', 'Hairydelphia', 'PA', 'USA', 0, '2002-01-01', '2003-02-04');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL ,'2',  'McDonalds',         'Hamburgler',        'I steal hamburgers.',                        'New York',     'NY', 'USA',  0,'2004-05-01', NULL);

INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time  ) VALUES (3,'Test1','User1','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'At peace with the world','2006-07-09 00:00:00');
INSERT INTO users_profile_contact (user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current) VALUES (3,'','','48173 Main St.','Bowmont','NJ','USA','00181','',1, 0);
INSERT INTO users_profile_contact (user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current) VALUES (3,'222-333-4444','111-222-3333','1234 56th St., Apt. 7','Eightown','NI','USA','87654','http://www.2coders1port.com',0, 1);
INSERT INTO users_profile_networks (user_id, network_id) VALUES (3,1),(3,2),(3,3);
INSERT INTO users_profile_personal (user_id, activities, interests, music, tv, movies, books, quotes, about) VALUES (3,'Boating, pumpkin carving, procuring comestibles, etc. ','Music,sports,tv guide,nuclear physics','Miles Davis,Brittney Spears','Telemundo','Snakes on a plane','Snakes on a plane, the book','Nationalism is an infantile disease - Albert Einstien','About me - nothing!');
INSERT INTO users_profile_rel (user_id,status,alternate_name,significant_other,meeting_for,meeting_sex) VALUES (3,0,'',0,'Random Play,Whatever I can get','M,F');

INSERT INTO users_profile_school (`id` ,`user_id`, `school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL,3, 2, '',1999,'Communications,Philosophy');
INSERT INTO users_profile_school (`id` ,`user_id`, `school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL,3, 3, '',2006,'Rocket Science,Awesomeness');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL, 3,'Spacely Sprockets','Sprocket Engineer','Now is the time on sprockets when we dance','Hairydelphia','PA','USA',1,'2002-01-01','2003-02-04');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL, 3,'McDonalds','Hamburgler','I steal hamburgers.','New York','NY','USA',0,'2004-05-01',NULL);

INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time  ) VALUES (4,'Four','UserFour','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'At peace with the world','2006-07-09 00:00:00');
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (2,4,null ,1 ,2 );

/** Testing User Auth */
INSERT INTO users (id,username,password) VALUES (8001,'joe','MD5HASH');
INSERT INTO users (id,username,password) VALUES (8002,'jack','MD5HASH');
INSERT INTO users (id,username,password) VALUES (8003,'jill','MD5HASH');
INSERT INTO users (id,username,password) VALUES (8004,'jacob','MD5HASH');
INSERT INTO users (id,username,password) VALUES (8005,'jacob','MD5HASH');

/** Testing user methods, other than getInfo **/
INSERT INTO users (id,username,password) VALUES (6001,'joe_has_app_is_enabled','MD5HASH');
INSERT INTO users (id,username,password) VALUES (6002,'jack_has_abb_not_enabled','MD5HASH');
INSERT INTO users (id,username,password) VALUES (6003,'jill_does_not_have_app','MD5HASH');

INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 6100, 6001, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 6100, 6002, 0  );

/** FBML TESTS (7000)**/

INSERT INTO users (id,username,password) VALUES (7001,'joe_has_fbml_and_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7002,'jack_has_emptyfbml_and_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7003,'jeff_no_fbml_has_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7004,'jill_no_fbml_no_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7011,'joe_has_fbml_and_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7012,'jack_has_emptyfbml_and_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7013,'jeff_no_fbml_has_app','MD5HASH');
INSERT INTO users (id,username,password) VALUES (7014,'jill_no_fbml_no_app','MD5HASH');

INSERT INTO users_app ( app_id, user_id, fbml) VALUES ( 7100, 7001, "helloworld" );
INSERT INTO users_app ( app_id, user_id, fbml ) VALUES ( 7100, 7002, "" );
INSERT INTO users_app ( app_id, user_id ) VALUES ( 7100, 7003  );

INSERT INTO users_app ( app_id, user_id, fbml) VALUES ( 7101, 7011, "helloworld" );
INSERT INTO users_app ( app_id, user_id, fbml ) VALUES ( 7101, 7012, "" );
INSERT INTO users_app ( app_id, user_id ) VALUES ( 7101, 7013  );

/**
 * EVENTS GET TESTS (4000) 
 */

INSERT INTO users (id,username,password) VALUES (4001,'EVENTS_USER_WITH_ONE_EVENT','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4002,'EVENTS_USER_NO_EVENTS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4003,'EVENTS_USER_WITH_PRIVATE_EVENTS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4004,'EVENTS_USER_WITH_SEVEN_EVENTS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4005,'EVENTS_CREATOR','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4006,'EVENTS_USER_WITH_PRIVATE_EVENTS_NO_FRIENDS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4007,'EVENTS_USER_TIME_EVENTS','MD5HASH');

INSERT INTO friends ( from_id, to_id, status ) values ( 4001,4002,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4001,4003,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4001,4004,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4001,4005,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4002,4003,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4002,4004,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4002,4005,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4003,4004,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4003,4005,2 );
INSERT INTO friends ( from_id, to_id, status ) values ( 4004,4005,2 );


INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4101, 'EVENTS_EVENT_PRIVATE', 0, "athome", "party", "hardy", 1197691200, 1197691400 , 4005, null, "on the bank", "mars", "ok", "us", 3 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4102, 'EVENTS_EVENT_OPEN_1', 0, "athome", "party", "hardy", 1197690200, 1197690400 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4103, 'EVENTS_EVENT_OPEN_2', 0, "athome", "party", "hardy", 1197688200, 1197688400 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4104, 'EVENTS_EVENT_OPEN_4', 0, "athome", "party", "hardy", 1197791200, 1197791400 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4105, 'EVENTS_EVENT_CLOSED_1', 0, "athome", "party", "hardy", 1197621200, 1197621400 , 4005, null, "on the bank", "mars", "ok", "us", 2 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4106, 'EVENTS_EVENT_CLOSED_2', 0, "athome", "party", "hardy", 1197631200, 1197631400 , 4005, null, "on the bank", "mars", "ok", "us", 2 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4107, 'EVENTS_EVENT_CLOSED_3', 0, "athome", "party", "hardy", 1197641200, 1197641400 , 4005, null, "on the bank", "mars", "ok", "us", 2 );

INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4110, 'EVENT_NOW', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-300, UNIX_TIMESTAMP()+300 , 4005, null, "on the bank", "mars", "ok", "us", 2 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4111, 'EVENT_YESTERDAY', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-300-1440, UNIX_TIMESTAMP()+300-1440 , 4005, null, "on the bank", "mars", "ok", "us", 2 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4112, 'EVENT_TOMORROW', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-300+1440, UNIX_TIMESTAMP()+300+1440 , 4005, null, "on the bank", "mars", "ok", "us", 2 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4001, 4102, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4003, 4101, 2 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4101, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4102, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4103, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4104, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4105, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4106, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4004, 4107, 4 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4101, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4102, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4103, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4104, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4105, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4106, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4005, 4107, 1 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4101, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4102, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4103, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4104, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4105, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4106, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4006, 4107, 1 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4007, 4110, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4007, 4111, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4007, 4112, 2 );

/** 
 * Testing Events Members ids 4x2x 
 */
INSERT INTO users (id,username,password) VALUES (4020,'EVENTS_MEMBMER_USER_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4021,'EVENTS_MEMBMER_USER_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4022,'EVENTS_MEMBMER_USER_3','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4023,'EVENTS_MEMBMER_USER_4','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4024,'EVENTS_MEMBMER_USER_5','MD5HASH');
INSERT INTO users (id,username,password) VALUES (4025,'EVENTS_MEMBMER_NO_EVENTS','MD5HASH');

INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4120, 'EVENT_WITH_NO_MEMBERS', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4121, 'EVENT_WITH_ONE_MEMBERS', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4122, 'EVENT_WITH_MANY_MEMBERS', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 4005, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4123, 'PRIVATE_EVENT_WITH_MEMBERS', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 4005, null, "on the bank", "mars", "ok", "us", 3 );
INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 4124, 'CLOSED_EVENT_WITH_MEMBERS', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 4005, null, "on the bank", "mars", "ok", "us", 2 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4020, 4121, 1 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4020, 4122, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4021, 4122, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4022, 4122, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4023, 4122, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4024, 4122, 1 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4020, 4123, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4021, 4123, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4022, 4123, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4023, 4123, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4024, 4123, 1 );

INSERT INTO events_members( uid, eid, rsvp ) values  ( 4020, 4124, 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4021, 4124, 2 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4022, 4124, 3 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4023, 4124, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 4024, 4124, 1 );

/**
 * Admin calls - 110xx
 */

/* Add user who has both apps */
INSERT INTO users (id,username,password) VALUES (11101,'USER_WITH_BOTH_APPS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (11102,'USER_WITH_ONE_APP_11000','MD5HASH');
INSERT INTO users (id,username,password) VALUES (11103,'USER_WITH_NO_APPS','MD5HASH');

INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 11000, 11101, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 11000, 11102, 1  );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 11001, 11101, 1  );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 11001, 11103, 0  );



/**
 * Notifications - 120xx 
 **/
INSERT INTO users (id,username,password) VALUES (12001,'NOTIF_USER_WITH_POKES','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12002,'NOTIF_USER_WITH_SHARES','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12003,'NOTIF_USER_WITH_MSGS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12004,'NOTIF_USER_WITH_NOTIFICATIONS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12005,'NOTIF_USER_MOSTRECENT_POKE','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12006,'NOTIF_USER_WITH_EVENTS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12007,'NOTIF_USER_WITH_FRIENDREQS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12008,'NOTIF_USER_WITH_GROUPS','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12009,'NOTIF_USER_WITH_INVITES','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12010,'NOTIF_USER_WITH_EVERYTHING','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12011,'NOTIF_USER_CREATOR','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12012,'NOTIF_USER_SENDFROM','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12013,'NOTIF_USER_SENDTO_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12014,'NOTIF_USER_SENDTO_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12015,'NOTIF_USER_SENDTO_NOTFRIEND','MD5HASH');
INSERT INTO users (id,username,password) VALUES (12016,'NOTIF_SEND_EMAIL_FROM','MD5HASH');

INSERT INTO users_profile_econtact ( user_id, contact_type, contact_value ) values ( 12012, 0, 'sendfrom@testme.com' );
INSERT INTO users_profile_econtact ( user_id, contact_type, contact_value ) values ( 12013, 0, 'sendto1@testme.com' );
INSERT INTO users_profile_econtact ( user_id, contact_type, contact_value ) values ( 12014, 0, 'sendto2@testme.com' );
INSERT INTO users_profile_econtact ( user_id, contact_type, contact_value ) values ( 12015, 0, 'sendnotfriend@testme.com' );
INSERT INTO users_profile_econtact ( user_id, contact_type, contact_value ) values ( 12016, 0, 'sendemailfrom@testme.com' );

INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12001, 12002, 1, 'poke', TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12001, 12003, 1, 'poke', TIMESTAMPADD(MINUTE,2,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12001, 12004, 1, 'poke', TIMESTAMPADD(MINUTE,3,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12001, 12005, 1, 'poke', TIMESTAMPADD(MINUTE,4,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12004, 12005, 1, 'poke', TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 12010, 12005, 1, 'poke', TIMESTAMPADD(MINUTE,1,now()) );

INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12101, 12002, 0, TIMESTAMPADD(MINUTE,1,now()), 'brown', 'www.brown.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12102, 12002, 0, TIMESTAMPADD(MINUTE,2,now()), 'blue', 'www.blue.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12103, 12002, 0, TIMESTAMPADD(MINUTE,3,now()), 'green', 'www.green.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12104, 12002, 0, TIMESTAMPADD(MINUTE,4,now()), 'pink', 'www.pink.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12105, 12004, 0, TIMESTAMPADD(MINUTE,5,now()), 'red', 'www.red.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 12107, 12010, 0, TIMESTAMPADD(MINUTE,6,now()), 'yellow', 'www.yellow.com' );

INSERT INTO mail ( mail_id, uid ) VALUES( 12090, 12003 );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12090, 12003, 0, TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12090, 12005, 0, TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12090, 12003, "My Message", TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12090, 12005, "My first response", TIMESTAMPADD(MINUTE,2,now())  );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12090, 12003, "Another response", TIMESTAMPADD(MINUTE,3,now())  );

INSERT INTO mail ( mail_id, uid ) VALUES( 12080, 12003 );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12080, 12003, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12080, 12004, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12080, 12010, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12080, 12003, "My Message", TIMESTAMPADD(MINUTE,41,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12080, 12004, "My first response", TIMESTAMPADD(MINUTE,42,now())  );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12080, 12003, "Another response", TIMESTAMPADD(MINUTE,43,now())  );

INSERT INTO mail ( mail_id, uid ) VALUES( 12070, 12003 );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12070, 12003, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 12070, 12002, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12070, 12003, "My Message", TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12070, 12002, "My first response", TIMESTAMPADD(MINUTE,2,now())  );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 12070, 12003, "Another response", TIMESTAMPADD(MINUTE,3,now())  );

 
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12006, 12901, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12006, 12902, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12006, 12903, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12006, 12904, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12009, 12905, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12009, 12906, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12009, 12907, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12010, 12905, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12010, 12906, 4 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 12010, 12907, 4 );

INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (12800 ,'ONE' ,0 , 1, null ,12011 );
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (12801 ,'TWO' ,0 , 1, null ,12011 );
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (12802 ,'THREE' ,0 , 1, null ,12011 );
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (12803 ,'FOUR' ,0 , 1, null ,12011 );

INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12800 ,12008 ,1 ,0 ,0 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12801 ,12008 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12802 ,12008 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12803 ,12008 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12800 ,12009 ,1 ,0 ,0 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12801 ,12009 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12802 ,12009 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12803 ,12009 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12800 ,12010 ,1 ,0 ,0 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12801 ,12010 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12802 ,12010 ,0 ,0 ,1 , null );
INSERT INTO groups_member (gid,uid,member,admin,pending,created) VALUES ( 12803 ,12010 ,0 ,0 ,1 , null );


INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12001 ,12007 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12002 ,12007 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12003 ,12007 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12004 ,12007 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12005 ,12007 ,null ,1 ,0 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12002 ,12009 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12003 ,12009 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12004 ,12009 ,null ,1 ,1 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12002 ,12010 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12003 ,12010 ,null ,1 ,1 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12004 ,12010 ,null ,1 ,1 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12012 ,12013 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12014 ,12012 ,null ,1 ,2 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12016 ,12013 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (12014 ,12016 ,null ,1 ,2 );

/**
 * Testing feeds from a data perspective only really needs 
 * friends information and user_app info.  Everything else is 
 * new inserts. 
 * Using 16000
 */
INSERT INTO users (id,username,password) VALUES (16001,'FEED_TMPLT_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16002,'FEED_TMPLT_ACTOR_NOT_FRIEND','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16003,'FEED_TMPLT_ACTOR_IS_FRIEND_NO_APP','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16004,'FEED_TMPLT_ACTOR_IS_FRIEND_WITH_APP','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16005,'FEED_TMPLT_TARGET_NOT_FRIEND1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16006,'FEED_TMPLT_TARGET_NOT_FRIEND2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16007,'FEED_TMPLT_TARGET_FRIEND1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16008,'FEED_TMPLT_TARGET_FRIEND2','MD5HASH');


INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16001 ,16003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16001 ,16004 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16001 ,16007 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16001 ,16008 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16004 ,16007 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16004 ,16008 ,null ,1 ,2 );

INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 16100, 16001, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 16100, 16004, 1 );

/* feed get tests */
INSERT INTO users (id,username,password) VALUES (16010,'FEED_TMPLT_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16011,'FEED_TMPLT_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16012,'FEED_TMPLT_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16013,'FEED_TMPLT_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (16014,'FEED_TMPLT_USER_NOT_FRIEND','MD5HASH');
INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 16020, 16010, 'Feed Page', 'http://yahoo.com/image1.jpg', 'ONLINE_STORE');

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16010, 16011, null, 1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16010, 16012, null, 1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (16010, 16013, null, 1 ,2 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 16200, 16010, 1 );
INSERT INTO feed ( `author_id`, `type`, `templatized`, `title` ) VALUES ( '16010', '1', '0', 'This is the title of this feed publication #1' );
INSERT INTO feed ( `author_id`, `type`, `templatized`, `title` ) VALUES ( '16010', '2', '0', 'This is the title of this feed publication #2' );
INSERT INTO feed ( `author_id`, `type`, `templatized`, `title` ) VALUES ( '16011', '1', '0', 'This is the title of this feed publication #3' );
INSERT INTO feed ( `author_id`, `type`, `templatized`, `title` ) VALUES ( '16011', '2', '0', 'This is the title of this feed publication #4' );
INSERT INTO feed ( `author_id`, `type`, `templatized`, `title` ) VALUES ( '16011', '2', '0', 'This is the title of this feed publication #5' );
INSERT INTO feed ( `actor_id`, `type`, `templatized`, `title` ) VALUES ( '16020', '2', '1', 'This is the title of this feed publication #6' );
INSERT INTO feed ( `actor_id`, `type`, `templatized`, `title` ) VALUES ( '16020', '2', '1', 'This is the title of this feed publication #7' );
INSERT INTO feed ( `actor_id`, `type`, `templatized`, `title` ) VALUES ( '16020', '2', '1', 'This is the title of this feed publication #8' );

/**
 * Client API testing
 */
INSERT INTO users (id,username,password) VALUES (17001,'CLIENT_PHP_USER','MD5HASH');
INSERT INTO users (id,username,password) VALUES (17002,'CLIENT_PHP_USER2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (17003,'CLIENT_PHP_USER3','MD5HASH');
INSERT INTO users (id,username,password) VALUES (17004,'CLIENT_PHP_USER4','MD5HASH');
INSERT INTO users (id,username,password) VALUES (17005,'CLIENT_PHP_USER5','MD5HASH');
INSERT INTO users (id,username,password) VALUES (17006,'CLIENT_PHP_USER6','MD5HASH');

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (17001 ,17002 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (17001 ,17003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (17001 ,17004 ,null ,1 ,2 );

INSERT INTO users_app ( app_id, user_id, enabled, allows_status_update, allows_photo_upload, allows_create_listing,fbml) VALUES ( 17100, 17001, 1, 1, 0, 1, 'this is some fbml');
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 17100, 17002, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 17100, 17003, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 17100, 17005, 1 );


REPLACE INTO status (uid,aid,status,modified) VALUES (17001, 17100, 'CooCoo for Coco Puffs',NOW()); 
INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time, pic_url,pic_small_url,pic_big_url,pic_square_url  ) VALUES (17001,'Test1','User1','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'','2008-1-1 00:00:00', 'http://no.such.url/pic.gif','http://no.such.url/pic_big.jpg','http://no.such.url/pic_small.jpg','http://no.such.url/pic_square.jpg');

INSERT INTO users_profile_contact(user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current) VALUES (17001,'','','48173 Main St.','Bowmont','NJ','USA','00181','',0,1),(17001,'222-333-4444','111-222-3333','1234 56th St., Apt. 7','Eightown','NI','USA','87654','http://www.2coders1port.com', 1, 0);
INSERT INTO users_profile_networks (user_id, network_id) VALUES (17001,1),(17001,2),(17001,3);
INSERT INTO users_profile_personal (user_id, activities, interests, music, tv, movies, books, quotes, about) VALUES (17001,'Boating, pumpkin carving, procuring comestibles, etc. ','Music,sports,tv guide,nuclear physics','Miles Davis,Brittney Spears','Telemundo','Snakes on a plane','Snakes on a plane, the book','Nationalism is an infantile disease - Albert Einstien','About me - nothing!');
INSERT INTO users_profile_rel (user_id,status,alternate_name,significant_other,meeting_for,meeting_sex) VALUES (17001,0,'',0,'Random Play,Whatever I can get','M,F');

INSERT INTO users_profile_school (`id` ,`user_id` ,`school_id`,`school_name`,`grad_year` ,`concentrations`) VALUES (NULL, 17001,2,'', 1999,'Communications,Philosophy'),(NULL, 17001,3,'', 2006,'Rocket Science,Awesomeness');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL, 17001,'Spacely Sprockets','Sprocket Engineer','Now is the time on sprockets when we dance','Hairydelphia','PA','USA',1,'2002-01-01','2003-02-04'),(NULL, 17001,'McDonalds','Hamburgler','I steal hamburgers.','New York','NY','USA',0,'2004-05-01',NULL);

INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 17001, 17002, 1, 'poke1', TIMESTAMPADD(MINUTE,1,now()) );
INSERT INTO pokes (toid, fromid, enabled, name, modified ) VALUES ( 17001, 17003, 1, 'poke2', TIMESTAMPADD(MINUTE,2,now()) );

INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 17201, 17001, 0, TIMESTAMPADD(MINUTE,1,now()), 'brown', 'www.brown.com' );
INSERT INTO shares (shareid, uid, opened, created, subject, link ) VALUES ( 17202, 17001, 0, TIMESTAMPADD(MINUTE,2,now()), 'blue', 'www.blue.com' );

INSERT INTO mail ( mail_id, uid ) VALUES( 17300, 17001 );
INSERT INTO mail_box( mail_id, uid, deleted, last_opened ) VALUES ( 17300, 17001, 0, TIMESTAMPADD(MINUTE,20,now()) );
INSERT INTO mail_messages( mail_id, uid, fbml, created ) VALUES ( 17300, 17001, "My Message", TIMESTAMPADD(MINUTE,1,now()) );

INSERT INTO events( eid, name , nid, host, event_type, event_subtype, start_time, end_time, uid, created, location, city, state, country, access ) values  ( 17400, 'SOME_EVENT', 0, "athome", "party", "hardy", UNIX_TIMESTAMP()-60+1440, UNIX_TIMESTAMP()+60+1440 , 17001, null, "on the bank", "mars", "ok", "us", 1 );
INSERT INTO events_members( uid, eid, rsvp ) values  ( 17001, 17400, 4);
INSERT INTO events_members( uid, eid, rsvp ) values  ( 17002, 17400, 2);
INSERT INTO events_members( uid, eid, rsvp ) values  ( 17003, 17400, 3);
INSERT INTO events_members( uid, eid, rsvp ) values  ( 17004, 17400, 1);

INSERT INTO album (aid, cover_pid, owner, name,description,location) VALUES (17500, 0, 17001, 'test album 1', 'test album 1 description', 'antartica');
INSERT INTO album (aid, cover_pid, owner, name,description,location) VALUES (17501, 0, 17001, 'test album 2', 'test album 2 description', 'new z-land');

INSERT INTO photo (pid, aid, owner, src_small, src_big, src, link, caption) VALUES (17520, 17500, 17001, 'http://localhost/img1_small.jpg','http://localhost/img1_big.jpg','http://localhost/img1.jpg', 'http://localhost/img1_link.jpg', 'image 1');
INSERT INTO photo (pid, aid, owner, src_small, src_big, src, link, caption) VALUES (17521, 17500, 17001, 'http://localhost/img2_small.jpg','http://localhost/img2_big.jpg','http://localhost/img2.jpg', 'http://localhost/img2_link.jpg', 'image 2');
INSERT INTO photo (pid, aid, owner, src_small, src_big, src, link, caption) VALUES (17522, 17501, 17001, 'http://localhost/img3_small.jpg','http://localhost/img3_big.jpg','http://localhost/img3.jpg', 'http://localhost/img3_link.jpg', 'image 3');
INSERT INTO photo (pid, aid, owner, src_small, src_big, src, link, caption) VALUES (17523, 17501, 17001, 'http://localhost/img4_small.jpg','http://localhost/img4_big.jpg','http://localhost/img4.jpg', 'http://localhost/img4_link.jpg', 'image 4');
INSERT INTO photo (pid, aid, owner, src_small, src_big, src, link, caption) VALUES (17524, 17501, 17001, 'http://localhost/img5_small.jpg','http://localhost/img5_big.jpg','http://localhost/img5.jpg', 'http://localhost/img5_link.jpg', 'image 5');

INSERT INTO photo_tag (ptid, pid, subject_id, text, xcoord, ycoord) VALUES (17600, 17520, 17001, 'tag 1', 0, 0);
INSERT INTO photo_tag (ptid, pid, subject_id, text, xcoord, ycoord) VALUES (17601, 17520, 17001, 'tag 2', 100, 100);
INSERT INTO photo_tag (ptid, pid, subject_id, text, xcoord, ycoord) VALUES (17602, 17521, 17002, 'tag 3', 200, 200);

INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 17700, 17001, 'CRAZYONLINE', 'http://www.picit.com/image1.jpg', 'ONLINE_STORE');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 17700, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 17700, 'website', 'http://mycrazystore.com/' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 17700, 'company_overview', 'Crazy guys with crazy store' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 17700, 'mission', 'A long mission with a nice vision' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 17700, 'products', 'A b C and D ' );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 17700, 17001, 1, 1);
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 17700, 17002, 0, 1);
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 17700, 17005, 0, 1);
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 17700, 17100, 1 );
																																																																																			 
INSERT INTO groups (gid,name,nid,access_type,created,creator,description,group_type,group_subtype,recent_news,office,website,email,street,city,state,country,pic_small,pic_big,image,latitude,longitude) VALUES (17800 ,'group 1' ,0 , 1, NOW() ,17001, 'this is group 1', 'Awesome Group', 'Awesome sub-group', 'No news is good news', 'Suite 55', 'http://www.nowhere.com', 'nobody@nowhere.com', '123 4th St.', 'Nowherapolis', 'ZZ', 'France','http://localhost/smallpic.jpg','http://localhost/bigpic.jpg','http://localhost/pic.jpg',55.6,38.1);
INSERT INTO groups (gid,name,nid,access_type,created,creator ) VALUES (17801 ,'group 2' ,0 , 1, NOW() ,17002 );

INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17800, 17001, 0, 1, 1, 0, NOW());
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17800, 17002, 1, 1, 0, 0, NOW());
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17800, 17003, 0, 0, 0, 1, NOW());
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17800, 17004, 1, 0, 0, 1, NOW());

INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17801, 17002, 0, 1, 1, 0, NOW());
INSERT INTO groups_member (gid,uid,officer,member,admin,pending,created) VALUES ( 17801, 17001, 1, 1, 0, 0, NOW());


/**
 * Payments 
 * Using 36000
 */
INSERT INTO `social_pay_plans` ( `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36000', 'bronze', '1', 'months', '5.00', 'Get features!' );  
INSERT INTO `social_pay_plans` ( `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36000', 'silver', '1', 'months', '10.00', 'Get more features!' ); 
INSERT INTO `social_pay_plans` ( `aid`, `name`, `length`, `unit`, `price`, `description`, `num_friends` ) VALUES ( '36000', 'gold', '1', 'months', '15.00', 'Get all features!', '6' ); 

INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36001', 'subscription_user_username', 'ringside' );
INSERT INTO `users_profile_basic` ( `user_id`, `first_name`, `last_name` ) VALUES ( '36001', 'Jim', 'Jones' );
INSERT INTO `social_pay_plans` ( `id`, `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36010', '36050', 'bronze', '1', 'months', '5.00', 'Get features!' ); 

INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36100', 'admin', 'ringside' );
INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36101', 'subscription_user_friend', 'ringside' );

INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36070', 'subscription_user_username', 'ringside' );
INSERT INTO `social_pay_plans` ( `id`, `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36080', '36060', 'a', '1', 'months', '5.00', 'Get stuff!' ); 
INSERT INTO `social_pay_plans` ( `id`, `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36081', '36060', 'b', '1', 'months', '5.00', 'Get more stuff!' ); 
INSERT INTO `social_pay_plans` ( `id`, `aid`, `name`, `length`, `unit`, `price`, `description` ) VALUES ( '36082', '36060', 'c', '1', 'months', '5.00', 'Get everything!' ); 
INSERT INTO `social_pay_subscriptions` ( `uid`, `network_id`, `plan_id`, `aid`, `gateway_subscription_id` ) VALUES ( '36070', 'socialkey', '36080', '36060', 'fake' );

INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36071', 'subscription_user_friend', 'ringside' );
INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36072', 'subscription_user_friend', 'ringside' );
INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36073', 'subscription_user_friend', 'ringside' );
INSERT INTO `users` ( `id`, `username`, `password` ) VALUES ( '36074', 'subscription_user_not_friend', 'ringside' );
INSERT INTO `friends` ( `from_id`, `to_id`, `created`, `access`, `status` ) VALUES ( '36070', '36071', null, '1', '2' );
INSERT INTO `friends` ( `from_id`, `to_id`, `created`, `access`, `status` ) VALUES ( '36070', '36072', null, '1', '2' );
INSERT INTO `friends` ( `from_id`, `to_id`, `created`, `access`, `status` ) VALUES ( '36070', '36073', null, '1', '2' );
INSERT INTO `social_pay_plans` ( `id`, `aid`, `name`, `length`, `unit`, `price`, `description`, `num_friends` ) VALUES ( '36083', '36060', 'a', '1', 'months', '5.00', 'Get stuff!', '2' );


/**
 * Data for admin.getNetworkProperties
 */
INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url) VALUES ('testdata_key1','testdata_name1','testdata_auth1','testdata_login1','testdata_canvas1','testdata_web1','testdata_social1','testdata_authclass1','testdata_postmap1');
INSERT INTO users_network (user_id,network_id) VALUES (17001, 'testdata_key1');

INSERT INTO users (id,username,password) VALUES (100000,'admin','nopass');

/**
 * Data for admin.getAppKeys
 */
INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url)
	VALUES ('testdata_key333','testdata_name333','testdata_auth333','testdata_login333','testdata_canvas333','testdata_web333','testdata_social333','testdata_authclass333','testdata_postmap333');
INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url)
	VALUES ('testdata_key444','testdata_name444','testdata_auth444','testdata_login444','testdata_canvas444','testdata_web444','testdata_social444','testdata_authclass444','testdata_postmap444');
INSERT INTO developer_app (user_id,app_id) VALUES (17001, 17100);

/* 
 * Moved from extension apps.  Not sure what uses it
 */
INSERT INTO users (id,username,password) VALUES (81000,'COMMENTS_USER_1','MD5HASH');
INSERT INTO users (id,username,password) VALUES (81001,'COMMENTS_USER_2','MD5HASH');
INSERT INTO users (id,username,password) VALUES (81002,'COMMENTS_USER_3','MD5HASH');

DELIMETER_SQL_INSERT;

mysql_upload_string($sql)  or die( " setup MySQL error " . mysql_error() );

?>