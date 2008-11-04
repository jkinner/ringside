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

INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url) VALUES ('$network_key','$network_key-name','$network_key-auth1','$network_key-login1','$network_key-canvas1','$network_key-web1','$network_key-social1','$network_key-authclass1','$network_key-postmap1');


/* Testing User Profiles */
INSERT INTO schools (id,name,school_type) VALUES (1,'Central High',1);
INSERT INTO schools (id,name,school_type) VALUES (2,'Temple University',2);
INSERT INTO schools (id,name,school_type) VALUES (3,'Georgia Tech',2);
INSERT INTO networks (id,name) VALUES (1,'Philadelphia');
INSERT INTO networks (id,name) VALUES (2,'Arts and Crafts');
INSERT INTO networks (id,name) VALUES (3,'Northeast High');
INSERT INTO users (id,username,password) VALUES (9999,'admin','');
INSERT INTO users (id,username,password) VALUES (8888,'user44','');
INSERT INTO users (id,username,password) VALUES (2,'user1','');
INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time, pic_url,pic_small_url,pic_big_url,pic_square_url  ) VALUES (2,'Test1','User1','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'At peace with the world','2006-07-09 00:00:00', 'http://no.such.url/pic.jpg','http://no.such.url/pic_small.jpg','http://no.such.url/pic_big.jpg','http://no.such.url/pic_square.jpg');

INSERT INTO users_profile_contact (user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current) VALUES (2,'','','48173 Main St.','Bowmont','NJ','USA','00181','',1,0),(2,'222-333-4444','111-222-3333','1234 56th St., Apt. 7','Eightown','NI','USA','87654','http://www.2coders1port.com',0,1);
INSERT INTO users_profile_networks (user_id, network_id) VALUES (2,1),(2,2),(2,3);
INSERT INTO users_profile_personal (user_id, activities, interests, music, tv, movies, books, quotes, about) VALUES (2,'Boating, pumpkin carving, procuring comestibles, etc. ','Music,sports,tv guide,nuclear physics','Miles Davis,Brittney Spears','Telemundo','Snakes on a plane','Snakes on a plane, the book','Nationalism is an infantile disease - Albert Einstien','About me - nothing!');
INSERT INTO users_profile_rel (user_id,status,alternate_name,significant_other,meeting_for,meeting_sex) VALUES (2,0,'',0,'Random Play,Whatever I can get','M,F');

INSERT INTO users_profile_school (`id` ,`user_id` ,`school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL , 2, 2, '', '1999', 'Communications,Philosophy'); 
INSERT INTO users_profile_school (`id` ,`user_id` ,`school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL , 2, 3, '', '2006', 'Rocket Science,Awesomeness'); 

INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL , '2', 'Spacely Sprockets', 'Sprocket Engineer', 'Now is the time on sprockets when we dance', 'Hairydelphia', 'PA', 'USA', 0, '2002-01-01', '2003-02-04');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL ,'2',  'McDonalds',         'Hamburgler',        'I steal hamburgers.',                        'New York',     'NY', 'USA',  0,'2002-01-01', NULL);

/** PAGE TESTING ID 2000 **/
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2000, 2100, 'PAGE ONE', 'MUSICIAN');
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2001, 2100, 'PAGE TWO_NO_APPS', 'BAND');
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2002, 2100, 'PAGE THREE_NO_FANS', 'MUSICIAN');

INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2000, 2200, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2000, 2201, 0 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2000, 2202, 1 );
INSERT INTO pages_app (page_id,app_id,enabled) VALUES ( 2002, 2201, 1 );

INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2000, 2100, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2000, 2101, 1, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2000, 2102, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2000, 2103, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2000, 2104, 0, 1 );

INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2100, 1, 0 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2103, 0, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2001, 2104, 0, 1 );

/** Page testing query **/
INSERT INTO pages (page_id,creator_id,name,type) VALUES ( 2050, 2100, 'CRAZYONLINE', 'ONLINE_STORE');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'website', 'http://mycrazystore.com/' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'overview', 'Crazy guys with crazy store' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'mission', 'A long mission with a nice vision' );
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2050, 'products', 'A b C and D ' );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2050, 2110, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2050, 2111, 1, 0 );

INSERT INTO pages (page_id,creator_id,name,pic_url, type) VALUES ( 2051, 2100, 'BIG BEAUTY', 'http://www.picit.com/image1.jpg', 'HEALTH_BEAUTY');
INSERT INTO pages_info( page_id, name, value ) VALUES ( 2051, 'founded', 'Early last year' );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'location', '$location2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'parking', '$parking2051', 1 );
INSERT INTO pages_info( page_id, name, value, json_encoded ) VALUES ( 2051, 'hours', '$hours2051', 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2051, 2110, 1, 1 );
INSERT INTO pages_fans ( page_id, uid, admin, fan  ) VALUES( 2051, 2111, 1, 0 );

/** Testing application classes. */
INSERT INTO app (id,callback_url,name,canvas_url,sidenav_url,isdefault, support_email, canvas_type,
	application_type, mobile, deployed, description, default_fbml, tos_url, icon_url,
	postadd_url, postremove_url, privacy_url, ip_list, about_url) 
VALUES ( 1200,'http://url.com/callback','Application 1200','app1200','app1200',0, 'jack@mail.com', 1, 1, 0, 1, 'Greatest appplication ever!', '<fb:name />',
 null, null, 'http://url.com/postadd', 'http://url.com/postremove', 'http://url.com/privacy', '12.34.56.78', 'http://url.com/about');
INSERT INTO app_keys (network_id, app_id, api_key, secret) VALUES ('$network_key',1200,'application-1200', 'application-1200');

INSERT INTO app (id,callback_url,name,canvas_url,sidenav_url,isdefault,support_email,canvas_type,application_type,mobile,deployed,description,default_fbml,
tos_url,icon_url,postadd_url,postremove_url,privacy_url,ip_list,about_url) 
VALUES ( 1201,'','Application 1201','app1201','app1201',0, 'jeff@mail.com', 1, 1, 0, 1, 'Greatest appplication ever!', '<fb:name />',
 null, null, 'http://url.com/postadd', 'http://url.com/postremove', 'http://url.com/privacy', '12.34.56.78' , 'http://url.com/about');
INSERT INTO app_keys (network_id, app_id, api_key, secret) VALUES ('$network_key',1201,'application-1201', 'application-1201');

INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url)
	VALUES ('testdata_key33','testdata_name33','testdata_auth33','testdata_login33','testdata_canvas33','testdata_web33','testdata_social33','testdata_authclass33','testdata_postmap33');
INSERT INTO rs_trust_authorities (trust_key,trust_name,trust_auth_url,trust_login_url,trust_canvas_url,trust_web_url,trust_social_url,trust_auth_class,trust_postmap_url)
	VALUES ('testdata_key44','testdata_name44','testdata_auth44','testdata_login44','testdata_canvas44','testdata_web44','testdata_social44','testdata_authclass44','testdata_postmap44');
INSERT INTO developer_app (user_id,app_id) VALUES (9999, 1200);
INSERT INTO app_keys (app_id,network_id,api_key,secret) VALUES (1200, 'testdata_key33', 'abcdef', 'fedcba');
INSERT INTO app_keys (app_id,network_id,api_key,secret) VALUES (1200, 'testdata_key44', 'zzzzz', 'aaaaa');


DELIMETER_SQL_INSERT;

mysql_upload_string($sql)  or die( " setup MySQL error " . mysql_error() );


?>