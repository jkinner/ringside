
INSERT INTO users (id,username,password) VALUES (1,'RingsideSocial', 'RingsideSocial' );
INSERT INTO users (id,username,password) VALUES (10,'admin', '$adminPassword' );
INSERT INTO users (id,username,password) VALUES (100000,'joe@goringside.net', '$everyPassword' );
INSERT INTO users (id,username,password) VALUES (100001,'jack@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100002,'jeff@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100003,'joel@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100004,'jane@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100005,'jill@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100006,'john@goringside.net','$everyPassword');
INSERT INTO users (id,username,password) VALUES (100007,'jon@goringside.net' , '$everyPassword' );
INSERT INTO users (id,username,password) VALUES (100008,'jared@goringside.net', '$everyPassword' );
INSERT INTO users (id,username,password) VALUES (100009,'june@goringside.net', '$everyPassword' );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100000,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100001 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100002 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100003 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100004 ,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100005 ,null ,1 ,0 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100009 ,100006 ,null ,1 ,0 );

INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100000 ,100001,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100000 ,100002,null ,1 ,2 );
INSERT INTO friends (from_id,to_id,created,access,status) VALUES (100000 ,100003,null ,1 ,2 );

INSERT INTO schools (id,name,school_type) VALUES (1,'Central High',1);
INSERT INTO schools (id,name,school_type) VALUES (2,'Temple University',2);
INSERT INTO schools (id,name,school_type) VALUES (3,'Georgia Tech',2);
INSERT INTO schools (id,name,school_type) VALUES (4,'CalState LA',2);

INSERT INTO networks (id,name) VALUES (1,'Philadelphia');
INSERT INTO networks (id,name) VALUES (2,'Arts & Crafts');
INSERT INTO networks (id,name) VALUES (3,'Northeast High');

INSERT INTO developer_app ( user_id, app_id ) VALUES ( 100000, 100101 );

INSERT INTO app (id,callback_url,name,canvas_url,sidenav_url,isdefault, canvas_type, icon_url, description) VALUES ( 100199,'$demoUrl/apps/stylecheck','Style Check','stylecheck','none',0, 1, '$demoUrl/images/icon-stylecheck.gif', 'Style check will check your style.');
INSERT INTO app_keys (network_id, app_id, api_key, secret) VALUES ('$socialApiKey', 100199, 'fbappskey','fbappssecret');

INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 100101, 100000, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 100210, 100000, 1 );
INSERT INTO users_app ( app_id, user_id, enabled ) VALUES ( 100101, 100001, 1 );

INSERT INTO users_profile_basic (user_id, first_name, last_name, dob, sex, political, religion, modified, timezone, status_message, status_update_time, pic_url  ) VALUES (100000,'Joe','Robinson','2007-12-31',0,0,'Scientologist','2008-01-01 00:00:00',-4,'At peace with the world','2006-07-09 00:00:00', '$webUrl/avatars/photo/bbickel.jpg' );
INSERT INTO users_profile_contact ( user_id, home_phone, mobile_phone, address, city, state, country, zip, website, is_hometown, is_current ) VALUES (100000,'','','48173 Main St.','Bowmont','NJ','USA','00181','',1,1);
INSERT INTO users_profile_networks (user_id, network_id) VALUES (100000,1);
INSERT INTO users_profile_personal (user_id, activities, interests, music, tv, movies, books, quotes, about) VALUES (100000,'Boating, pumpkin carving, procuring comestibles, etc. ','Music,sports,tv guide,nuclear physics','Miles Davis,Brittney Spears','Telemundo','Snakes on a plane','Snakes on a plane, the book','Nationalism is an infantile disease - Albert Einstien','About me - nothing!');
INSERT INTO users_profile_school (`id` ,`user_id`, `school_id`, `school_name` ,`grad_year` ,`concentrations`) VALUES (NULL, 100000,2,'',1999,'Communications,Philosophy');
INSERT INTO users_profile_work (`id` ,`user_id` ,`employer` ,`position` ,`description` ,`city` ,`state` ,`country` ,`current` ,`start_date` ,`end_date`) VALUES (NULL, 100000,'Spacely Sprockets','Sprocket Engineer','Now is the time on sprockets when we dance','Hairydelphia','PA','USA',1,'2002-01-01','2003-02-04');

INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100001,'Jack','Robinson' , '$webUrl/avatars/photo/brobinson.jpg' );
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100002,'Jeff','Robinson', '$webUrl/avatars/photo/wreichardt.jpg' );
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100003,'Joel','Robinson', '$webUrl/avatars/photo/jkinner.jpg');
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100004,'Jane','Robinson', '$webUrl/avatars/photo/girl_1.png');
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100005,'Jill','Robinson', '$webUrl/avatars/photo/stock1.jpg');
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100006,'John','Robinson', '$webUrl/avatars/photo/sconnolly.jpg');
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100007,'Jon','Robinson',  '$webUrl/avatars/photo/stock3.jpg' );
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100008,'Jared','Robinson','$webUrl/avatars/boy/boy_2.png');
INSERT INTO users_profile_basic (user_id, first_name, last_name, pic_url  ) VALUES (100009,'June','Robinson', '$webUrl/avatars/photo/stock2.jpg' );

INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_auth_class, trust_login_url, trust_web_url, trust_social_url, trust_canvas_url) VALUES ('$socialApiKey', 'Ringside', '$serverUrl', '', '$webUrl/login.php', '$webUrl', '$socialUrl', '$webUrl/canvas.php');
INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_auth_class, trust_login_url, trust_web_url, trust_social_url, trust_canvas_url) VALUES ('facebook','Facebook','http://api.facebook.com/restserver.php','', 'http://www.facebook.com/login.php', 'http://www.facebook.com', '', 'http://apps.facebook.com');
INSERT INTO users_network (user_id,network_id) VALUES ((SELECT id FROM users WHERE username='admin'), (SELECT trust_key FROM rs_trust_authorities WHERE trust_name='Ringside'));
INSERT INTO users_network (user_id,network_id) VALUES ((SELECT id FROM users WHERE username='admin'), (SELECT trust_key FROM rs_trust_authorities WHERE trust_name='Facebook'));

INSERT INTO users (id,username,password) VALUES (2,'anonymous', 'anonymous' );
INSERT INTO users_profile_basic (user_id, first_name, last_name ) VALUES (2,'anonymous','anonymous' );

INSERT INTO status (uid ,status ,aid) VALUES ('100000', 'Working' , 0);
INSERT INTO users_profile_rel (id ,user_id ,status ,alternate_name ,significant_other ,meeting_for ,meeting_sex) VALUES (NULL , '100000', 0, NULL , NULL , NULL , NULL);

INSERT INTO feed ( `type` , `templatized` , `title` ) VALUES ( '2', '0', 'Ringside Social Application Server has been successfully installed!' );

INSERT INTO users_profile (	`user_id`, `domain_id`, `first_name`, `last_name`, `dob`, `sex`, `political`,
							`religion`, `last_updated`, `timezone`, `status_message`, `status_update_time`,
							`pic_url`, `pic_big_url`, `pic_small_url`, `pic_square_url`)
	SELECT 					`user_id`, '$socialApiKey', `first_name`, `last_name`, `dob`, `sex`, `political`,
							`religion`, `modified`, `timezone`, `status_message`, `status_update_time`,
							`pic_url`, `pic_big_url`, `pic_small_url`, `pic_square_url`
	FROM `users_profile_basic`;
