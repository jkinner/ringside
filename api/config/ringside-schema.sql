
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS album;
DROP TABLE IF EXISTS app_prefs;
DROP TABLE IF EXISTS app_keys;
DROP TABLE IF EXISTS cache_content;
DROP TABLE IF EXISTS developer_app;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS events_members;
DROP TABLE IF EXISTS feed;
DROP TABLE IF EXISTS friends;
DROP TABLE IF EXISTS groups_member;
DROP TABLE IF EXISTS mail_messages;
DROP TABLE IF EXISTS mail_box;
DROP TABLE IF EXISTS mail;
DROP TABLE IF EXISTS pages_app;
DROP TABLE IF EXISTS pages_fans;
DROP TABLE IF EXISTS pages_info;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS photo_tag;
DROP TABLE IF EXISTS photo;
DROP TABLE IF EXISTS users_pro_work;
DROP TABLE IF EXISTS users_pro_school;
DROP TABLE IF EXISTS users_pro_contact;
DROP TABLE IF EXISTS users_pro_econtact;
DROP TABLE IF EXISTS users_profile_basic;
DROP TABLE IF EXISTS users_profile_work;
DROP TABLE IF EXISTS users_profile_highschool;
DROP TABLE IF EXISTS users_profile_school;
DROP TABLE IF EXISTS users_profile_rel;
DROP TABLE IF EXISTS users_profile_personal;
DROP TABLE IF EXISTS users_profile_networks;
DROP TABLE IF EXISTS users_profile_layout;
DROP TABLE IF EXISTS users_profile_contact;
DROP TABLE IF EXISTS users_profile_econtact;
DROP TABLE IF EXISTS pokes;
DROP TABLE IF EXISTS shares;
DROP TABLE IF EXISTS status;
DROP TABLE IF EXISTS status_history;
DROP TABLE IF EXISTS users_app;
DROP TABLE IF EXISTS users_app_session;
DROP TABLE IF EXISTS users_network;
DROP TABLE IF EXISTS users_profile;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS schools;
DROP TABLE IF EXISTS networks;
DROP TABLE IF EXISTS default_app;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS principal_map;
DROP TABLE IF EXISTS principal;
DROP TABLE IF EXISTS social_pay_gateways;
DROP TABLE IF EXISTS social_pay_subscriptions_friends;
DROP TABLE IF EXISTS social_pay_subscriptions;
DROP TABLE IF EXISTS social_pay_plans;
DROP TABLE IF EXISTS app;
DROP TABLE IF EXISTS developer_app;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS rs_trust_authorities;
DROP TABLE IF EXISTS rs_social_session_history;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS favorites_lists;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS suggestions;
DROP TABLE IF EXISTS PaymentPlans;
DROP TABLE IF EXISTS m3_meas_api_call;
DROP TABLE IF EXISTS `keyrings`;
DROP TABLE IF EXISTS domains;
DROP TABLE IF EXISTS friend_invitations;
DROP TABLE IF EXISTS better_friends;

CREATE TABLE `users` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `username` VARCHAR(45) DEFAULT ' ' NOT NULL, `password` VARCHAR(45) DEFAULT ' ' NOT NULL, `domain_id` BIGINT, `created` DATETIME, `modified` DATETIME, INDEX `domain_id_idx` (`domain_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_work` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `employer` VARCHAR(100), `position` VARCHAR(50), `description` TEXT, `city` VARCHAR(100), `state` VARCHAR(50), `country` VARCHAR(50), `current` BIGINT DEFAULT 1 NOT NULL, `start_date` DATE DEFAULT '1970-01-01' NOT NULL, `end_date` DATE, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_school` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `school_id` INT UNSIGNED DEFAULT 0 NOT NULL, `school_name` VARCHAR(100) DEFAULT ' ' NOT NULL, `grad_year` INT, `concentrations` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_rel` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `status` BIGINT, `alternate_name` VARCHAR(100), `significant_other` INT UNSIGNED, `meeting_for` VARCHAR(200), `meeting_sex` VARCHAR(3), `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_personal` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `activities` TEXT, `interests` TEXT, `music` TEXT, `tv` TEXT, `movies` TEXT, `books` TEXT, `quotes` TEXT, `about` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_networks` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `network_id` INT UNSIGNED DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`, `network_id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_layout` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `layout` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_econtact` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `contact_type` BIGINT, `contact_value` VARCHAR(100), `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_profile_contact` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `home_phone` VARCHAR(20), `mobile_phone` VARCHAR(20), `address` VARCHAR(200), `city` VARCHAR(100) DEFAULT ' ' NOT NULL, `state` VARCHAR(100) DEFAULT ' ' NOT NULL, `country` VARCHAR(100) DEFAULT ' ' NOT NULL, `zip` VARCHAR(15) DEFAULT ' ' NOT NULL, `website` TEXT, `is_hometown` TINYINT, `is_current` TINYINT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`, `city`, `state`, `country`, `zip`)) ENGINE = INNODB;
CREATE TABLE `users_profile_basic` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `first_name` VARCHAR(100), `last_name` VARCHAR(100), `dob` DATE, `sex` TINYINT, `political` BIGINT, `religion` VARCHAR(100), `timezone` INT, `status_message` VARCHAR(200), `status_update_time` TIMESTAMP, `pic_url` TEXT, `pic_big_url` TEXT, `pic_small_url` TEXT, `pic_square_url` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`)) ENGINE = INNODB;
CREATE TABLE `users_network` (`user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `network_id` VARCHAR(32) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`user_id`, `network_id`)) ENGINE = INNODB;
CREATE TABLE `users_app` (`app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `allows_status_update` TINYINT DEFAULT '0' NOT NULL, `allows_create_listing` TINYINT DEFAULT '0' NOT NULL, `allows_photo_upload` TINYINT DEFAULT '0' NOT NULL, `fbml` TEXT, `auth_information` TINYINT DEFAULT '0', `auth_profile` TINYINT DEFAULT '0', `auth_leftnav` TINYINT DEFAULT '0', `auth_newsfeeds` TINYINT DEFAULT '0', `enabled` TINYINT DEFAULT '0' NOT NULL, `profile_col` BIGINT DEFAULT 1 NOT NULL, `profile_order` INT UNSIGNED DEFAULT '0' NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx` (`user_id`, `app_id`), INDEX `app_id_idx` (`app_id`), INDEX `user_id_idx` (`user_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `users_app_session` (`aid` INT DEFAULT 0 NOT NULL, `uid` INT DEFAULT 0 NOT NULL, `infinite` TINYINT DEFAULT 0 NOT NULL, `session_key` VARCHAR(255) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`aid`, `uid`)) ENGINE = INNODB;
CREATE TABLE `suggestions` (`sid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `owneruid` INT UNSIGNED DEFAULT 0 NOT NULL, `api_key` VARCHAR(32) DEFAULT ' ' NOT NULL, `topic` VARCHAR(80) DEFAULT ' ' NOT NULL, `suggestion` VARCHAR(80) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`sid`)) ENGINE = INNODB;
CREATE TABLE `status` (`uid` INT UNSIGNED DEFAULT 0 NOT NULL, `status` TEXT NOT NULL, `aid` INT UNSIGNED DEFAULT 0 NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `status_history` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `status` TEXT NOT NULL, `cleared` TINYINT DEFAULT '0' NOT NULL, `aid` INT UNSIGNED DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`, `uid`)) ENGINE = INNODB;
CREATE TABLE `social_pay_subscriptions` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `network_id` VARCHAR(32) DEFAULT ' ' NOT NULL, `plan_id` INT UNSIGNED DEFAULT 0 NOT NULL, `aid` INT UNSIGNED DEFAULT 0 NOT NULL, `gateway_subscription_id` VARCHAR(64), `created` TIMESTAMP, `modified` TIMESTAMP, INDEX `plan_id_idx` (`plan_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `social_pay_subscriptions_friends` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `subscription_id` INT UNSIGNED DEFAULT 0 NOT NULL, `friend_id` INT UNSIGNED DEFAULT 0 NOT NULL, `friend_network_id` VARCHAR(32) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `social_pay_plans` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `aid` INT UNSIGNED, `network_id` INT UNSIGNED, `name` TEXT NOT NULL, `length` INT DEFAULT '1' NOT NULL, `unit` VARCHAR(6) DEFAULT 'months' NOT NULL, `price` DOUBLE NOT NULL, `num_friends` INT DEFAULT '0', `description` VARCHAR(255), `retired` TIMESTAMP, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `social_pay_gateways` (`type` VARCHAR(45) DEFAULT ' ' NOT NULL, `subject` VARCHAR(45) DEFAULT ' ' NOT NULL, `password` VARCHAR(45) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`type`, `subject`, `password`)) ENGINE = INNODB;
CREATE TABLE `shares` (`shareid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `link` TEXT NOT NULL, `subject` VARCHAR(255) DEFAULT ' ' NOT NULL, `opened` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`shareid`)) ENGINE = INNODB;
CREATE TABLE `sessions` (`session` VARCHAR(255) DEFAULT ' ' NOT NULL UNIQUE, `session_expires` INT DEFAULT '0' NOT NULL, `session_data` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `schools` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(100) DEFAULT ' ' NOT NULL, `school_type` BIGINT DEFAULT 2 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `rs_trust_authorities` (`trust_key` VARCHAR(32) DEFAULT ' ' NOT NULL UNIQUE, `trust_name` VARCHAR(255) DEFAULT ' ' NOT NULL, `trust_auth_url` VARCHAR(255) DEFAULT ' ' NOT NULL, `trust_login_url` VARCHAR(255) DEFAULT ' ' NOT NULL, `trust_canvas_url` VARCHAR(255) DEFAULT ' ' NOT NULL, `trust_web_url` VARCHAR(255) DEFAULT ' ' NOT NULL, `trust_social_url` VARCHAR(255), `trust_auth_class` VARCHAR(255), `trust_postmap_url` VARCHAR(255), `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `ratings` (`uid` INT UNSIGNED DEFAULT 0 NOT NULL, `item_id` VARCHAR(255) DEFAULT ' ' NOT NULL, `vote` DECIMAL(10,2) DEFAULT 0 NOT NULL, `app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx_idx` (`uid`, `item_id`, `app_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `principal` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `principal_map` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `principal_id` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `network_id` VARCHAR(255) DEFAULT ' ' NOT NULL, `app_id` INT UNSIGNED NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, INDEX `principal_id_idx` (`principal_id`), PRIMARY KEY(`id`, `app_id`)) ENGINE = INNODB;
CREATE TABLE `pokes` (`toid` INT UNSIGNED DEFAULT 0 NOT NULL, `fromid` INT UNSIGNED DEFAULT 0 NOT NULL, `enabled` TINYINT DEFAULT 0 NOT NULL, `name` VARCHAR(45) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`toid`, `fromid`)) ENGINE = INNODB;
CREATE TABLE `photo` (`pid` BIGINT NOT NULL AUTO_INCREMENT, `aid` BIGINT DEFAULT 0 NOT NULL, `owner` INT DEFAULT 0 NOT NULL, `src_small` VARCHAR(255) DEFAULT ' ' NOT NULL, `src_big` VARCHAR(255) DEFAULT ' ' NOT NULL, `src` VARCHAR(255) DEFAULT ' ' NOT NULL, `link` VARCHAR(255) DEFAULT ' ' NOT NULL, `caption` VARCHAR(255) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`pid`)) ENGINE = INNODB;
CREATE TABLE `photo_tag` (`ptid` BIGINT NOT NULL AUTO_INCREMENT, `pid` BIGINT DEFAULT 0 NOT NULL, `subject_id` INT, `text` VARCHAR(255), `xcoord` DOUBLE NOT NULL, `ycoord` DOUBLE NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, INDEX `pid_idx` (`pid`), PRIMARY KEY(`ptid`)) ENGINE = INNODB;
CREATE TABLE `pages` (`page_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `creator_id` INT UNSIGNED DEFAULT 0 NOT NULL, `name` VARCHAR(255) DEFAULT ' ' NOT NULL, `type` VARCHAR(45) DEFAULT ' ' NOT NULL, `pic_url` TEXT, `published` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`page_id`)) ENGINE = INNODB;
CREATE TABLE `pages_info` (`page_id` INT UNSIGNED DEFAULT 0 NOT NULL, `name` VARCHAR(45) DEFAULT ' ' NOT NULL, `value` TEXT NOT NULL, `json_encoded` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`page_id`, `name`)) ENGINE = INNODB;
CREATE TABLE `pages_fans` (`page_id` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `admin` TINYINT DEFAULT 0 NOT NULL, `fan` TINYINT DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`page_id`, `uid`)) ENGINE = INNODB;
CREATE TABLE `pages_app` (`page_id` INT UNSIGNED DEFAULT 0 NOT NULL, `app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `enabled` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`page_id`, `app_id`)) ENGINE = INNODB;
CREATE TABLE `networks` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(100) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `mail` (`mail_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `branch_id` INT UNSIGNED, `uid` VARCHAR(45) DEFAULT ' ' NOT NULL, `subject` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`mail_id`)) ENGINE = INNODB;
CREATE TABLE `mail_messages` (`message_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `mail_id` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `fbml` TEXT NOT NULL, `attach_fbml` TEXT, `isemail` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, INDEX `mail_id_idx` (`mail_id`), PRIMARY KEY(`message_id`)) ENGINE = INNODB;
CREATE TABLE `mail_box` (`mail_id` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `deleted` TINYINT DEFAULT '0' NOT NULL, `last_opened` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx` (`mail_id`, `uid`), INDEX `mail_id_idx` (`mail_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `m3_meas_api_call` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `nid` VARCHAR(255), `aid` INT UNSIGNED, `uid` INT UNSIGNED, `api_name` VARCHAR(64) DEFAULT ' ' NOT NULL, `duration` DOUBLE NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `items` (`item_id` VARCHAR(255) DEFAULT ' ' NOT NULL, `item_app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `item_data_type` INT UNSIGNED DEFAULT 0 NOT NULL, `item_url` TEXT NOT NULL, `item_refurl` TEXT NOT NULL, `item_status` VARCHAR(1) DEFAULT 'A' NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx` (`item_app_id`, `item_id`, `item_status`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `groups` (`gid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(45) DEFAULT ' ' NOT NULL, `nid` INT UNSIGNED DEFAULT '0' NOT NULL, `description` TEXT, `group_type` VARCHAR(45), `group_subtype` VARCHAR(45), `recent_news` TEXT, `office` VARCHAR(45), `website` TEXT, `email` TEXT, `street` VARCHAR(255), `city` VARCHAR(255), `show_related` TINYINT DEFAULT '0' NOT NULL, `discussion_board` TINYINT DEFAULT '0' NOT NULL, `wall` TINYINT DEFAULT '0' NOT NULL, `photos` TINYINT DEFAULT '0' NOT NULL, `photos_all` TINYINT DEFAULT '0' NOT NULL, `posted_items` TINYINT DEFAULT '0' NOT NULL, `posted_items_all` TINYINT DEFAULT '0' NOT NULL, `access_type` INT UNSIGNED DEFAULT 0 NOT NULL, `publicize` TINYINT DEFAULT '0' NOT NULL, `video` TINYINT DEFAULT '0' NOT NULL, `video_all` TINYINT DEFAULT '0' NOT NULL, `image` TEXT, `creator` INT UNSIGNED DEFAULT 0 NOT NULL, `state` VARCHAR(45), `country` VARCHAR(45), `pic_small` TEXT, `pic_big` TEXT, `latitude` DOUBLE, `longitude` DOUBLE, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`gid`)) ENGINE = INNODB;
CREATE TABLE `groups_member` (`gid` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `officer` TINYINT DEFAULT '0' NOT NULL, `member` TINYINT DEFAULT '0' NOT NULL, `admin` TINYINT DEFAULT '0' NOT NULL, `pending` TINYINT DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`gid`, `uid`)) ENGINE = INNODB;
CREATE TABLE `friends` (`id` BIGINT AUTO_INCREMENT, `from_id` VARCHAR(255), `to_id` VARCHAR(255), `domain_key` VARCHAR(255), `access` BIGINT, `status` BIGINT, `created` DATETIME, `modified` DATETIME, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `friend_invitations` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `inv_key` VARCHAR(32) NOT NULL UNIQUE, `from_id` INT UNSIGNED NOT NULL, `expires` INT UNSIGNED NOT NULL, `created` DATETIME, `modified` DATETIME, PRIMARY KEY(`id`, `inv_key`, `from_id`)) ENGINE = INNODB;
CREATE TABLE `feed` (`feed_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `type` SMALLINT UNSIGNED DEFAULT 0 NOT NULL, `templatized` TINYINT DEFAULT 0 NOT NULL, `title` TEXT NOT NULL, `title_data` TEXT, `body` TEXT, `body_data` TEXT, `body_general` TEXT, `image_1` TEXT, `image_1_link` TEXT, `image_2` TEXT, `image_2_link` TEXT, `image_3` TEXT, `image_3_link` TEXT, `image_4` TEXT, `image_4_link` TEXT, `priority` INT UNSIGNED, `author_id` INT UNSIGNED, `actor_id` INT UNSIGNED, `target_ids` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`feed_id`)) ENGINE = INNODB;
CREATE TABLE `favorites` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `item_id` VARCHAR(255) DEFAULT ' ' NOT NULL, `list_id` INT UNSIGNED, `app_list_id` INT UNSIGNED, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `status` VARCHAR(1) DEFAULT 'A' NOT NULL, `fbml` LONGBLOB, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `favorites_lists` (`name` VARCHAR(255) DEFAULT ' ' NOT NULL, `app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx` (`name`, `app_id`, `uid`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `events` (`eid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) DEFAULT ' ' NOT NULL, `tagline` VARCHAR(45), `nid` INT UNSIGNED DEFAULT 0 NOT NULL, `pic` TEXT, `host` VARCHAR(255) DEFAULT ' ' NOT NULL, `description` TEXT, `event_type` VARCHAR(255) DEFAULT ' ' NOT NULL, `event_subtype` VARCHAR(255) DEFAULT ' ' NOT NULL, `start_time` INT UNSIGNED DEFAULT 0 NOT NULL, `end_time` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `location` VARCHAR(255) DEFAULT ' ' NOT NULL, `street` VARCHAR(255), `city` VARCHAR(255) DEFAULT ' ' NOT NULL, `phone` VARCHAR(45), `email` VARCHAR(255), `state` VARCHAR(255) DEFAULT ' ' NOT NULL, `country` VARCHAR(255) DEFAULT ' ' NOT NULL, `latitude` VARCHAR(255), `longitude` VARCHAR(255), `bringfriends` TINYINT DEFAULT '0' NOT NULL, `opt_show_guest_list` TINYINT DEFAULT '0' NOT NULL, `opt_enable_wall` TINYINT DEFAULT '0' NOT NULL, `opt_enable_photos` SMALLINT UNSIGNED DEFAULT '0' NOT NULL, `opt_enable_videos` SMALLINT UNSIGNED DEFAULT '0' NOT NULL, `opt_enable_posted_items` SMALLINT UNSIGNED DEFAULT '0' NOT NULL, `access` SMALLINT UNSIGNED DEFAULT '0' NOT NULL, `publicize` TINYINT DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`eid`)) ENGINE = INNODB;
CREATE TABLE `events_members` (`eid` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `rsvp` INT UNSIGNED DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`eid`, `uid`)) ENGINE = INNODB;
CREATE TABLE `developer_app` (`id` BIGINT AUTO_INCREMENT, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, INDEX `app_id_idx` (`app_id`), INDEX `user_id_idx` (`user_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `default_app` (`app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `allows_status_update` TINYINT DEFAULT '1' NOT NULL, `allows_create_listing` TINYINT DEFAULT '1' NOT NULL, `allows_photo_upload` TINYINT DEFAULT '1' NOT NULL, `auth_information` TINYINT DEFAULT '1', `auth_profile` TINYINT DEFAULT '1', `auth_leftnav` TINYINT DEFAULT '1', `auth_newsfeeds` TINYINT DEFAULT '1', `enabled` TINYINT DEFAULT '1' NOT NULL, `profile_col` BIGINT DEFAULT 1 NOT NULL, `profile_order` INT UNSIGNED DEFAULT '0' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`app_id`)) ENGINE = INNODB;
CREATE TABLE `comments` (`cid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `xid` VARCHAR(45) DEFAULT ' ' NOT NULL, `aid` INT UNSIGNED DEFAULT 0 NOT NULL, `uid` INT UNSIGNED DEFAULT 0 NOT NULL, `text` TEXT NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`cid`)) ENGINE = INNODB;
CREATE TABLE `cache_content` (`app_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `key` TEXT NOT NULL, `store` VARCHAR(10) DEFAULT ' ' NOT NULL, `reference` TEXT NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`app_id`)) ENGINE = INNODB;
CREATE TABLE `app` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `callback_url` TEXT, `name` VARCHAR(255) DEFAULT ' ' NOT NULL, `canvas_url` TEXT NOT NULL, `sidenav_url` TEXT NOT NULL, `isdefault` TINYINT DEFAULT '0' NOT NULL, `desktop` TINYINT DEFAULT '0' NOT NULL, `developer_mode` TINYINT DEFAULT '0', `author` VARCHAR(255), `author_url` TEXT, `author_description` TEXT, `support_email` VARCHAR(255), `canvas_type` SMALLINT UNSIGNED DEFAULT '0', `application_type` VARCHAR(10) DEFAULT 'WEB', `mobile` TINYINT DEFAULT '0' NOT NULL, `deployed` TINYINT DEFAULT '0' NOT NULL, `description` TEXT, `default_fbml` TEXT, `tos_url` TEXT, `icon_url` TEXT, `postadd_url` TEXT, `postremove_url` TEXT, `privacy_url` TEXT, `ip_list` TEXT, `about_url` TEXT, `logo_url` TEXT, `edit_url` TEXT, `default_column` TINYINT DEFAULT '1', `attachment_action` TEXT, `attachment_callback_url` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `app_prefs` (`app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `user_id` INT UNSIGNED DEFAULT 0 NOT NULL, `value` TEXT, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`app_id`, `user_id`)) ENGINE = INNODB;
CREATE TABLE `app_keys` (`app_id` INT UNSIGNED DEFAULT 0 NOT NULL, `network_id` VARCHAR(32) DEFAULT ' ' NOT NULL, `api_key` VARCHAR(32) DEFAULT ' ' NOT NULL, `secret` VARCHAR(32) DEFAULT ' ' NOT NULL, `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `key_id` BIGINT, `created` TIMESTAMP, `modified` TIMESTAMP, UNIQUE INDEX `unique_idx` (`app_id`, `network_id`), INDEX `app_id_idx` (`app_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `album` (`aid` BIGINT NOT NULL AUTO_INCREMENT, `cover_pid` SMALLINT DEFAULT '0' NOT NULL, `owner` INT UNSIGNED DEFAULT 0 NOT NULL, `name` VARCHAR(100) DEFAULT ' ' NOT NULL, `description` VARCHAR(100) DEFAULT ' ' NOT NULL, `location` VARCHAR(100) DEFAULT ' ' NOT NULL, `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`aid`)) ENGINE = INNODB;
CREATE TABLE `PaymentPlans` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `aid` INT UNSIGNED, `network_id` INT UNSIGNED, `name` TEXT NOT NULL, `length` INT DEFAULT '12' NOT NULL, `unit` VARCHAR(6) DEFAULT 'months' NOT NULL, `price` DOUBLE NOT NULL, `description` VARCHAR(255), `created` TIMESTAMP, `modified` TIMESTAMP, PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `keyrings` (`id` BIGINT AUTO_INCREMENT, `entity_id` BIGINT, `domain_id` BIGINT, `api_key` VARCHAR(32) DEFAULT ' ' NOT NULL, `secret` VARCHAR(32) DEFAULT ' ' NOT NULL, `created` DATETIME, `modified` DATETIME, INDEX `domain_id_idx` (`domain_id`), PRIMARY KEY(`id`)) ENGINE = INNODB;
CREATE TABLE `domains` (`id` BIGINT AUTO_INCREMENT, `url` VARCHAR(255), `name` VARCHAR(255), `created` DATETIME, `modified` DATETIME, PRIMARY KEY(`id`)) ENGINE = INNODB;

ALTER TABLE `users_app` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `users_app` ADD FOREIGN KEY (`app_id`) REFERENCES `app`(`id`) ON DELETE CASCADE;
ALTER TABLE `social_pay_subscriptions` ADD FOREIGN KEY (`plan_id`) REFERENCES `social_pay_plans`(`id`);
ALTER TABLE `principal_map` ADD FOREIGN KEY (`principal_id`) REFERENCES `principal`(`id`) ON DELETE CASCADE;
ALTER TABLE `photo_tag` ADD FOREIGN KEY (`pid`) REFERENCES `photo`(`pid`);
ALTER TABLE `pages_info` ADD FOREIGN KEY (`page_id`) REFERENCES `pages`(`page_id`);
ALTER TABLE `pages_fans` ADD FOREIGN KEY (`page_id`) REFERENCES `pages`(`page_id`);
ALTER TABLE `mail_messages` ADD FOREIGN KEY (`mail_id`) REFERENCES `mail_box`(`mail_id`);
ALTER TABLE `mail_box` ADD FOREIGN KEY (`mail_id`) REFERENCES `mail`(`mail_id`);
ALTER TABLE `groups_member` ADD FOREIGN KEY (`gid`) REFERENCES `groups`(`gid`);
ALTER TABLE `developer_app` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `developer_app` ADD FOREIGN KEY (`app_id`) REFERENCES `app`(`id`) ON DELETE CASCADE;
ALTER TABLE `app_keys` ADD FOREIGN KEY (`app_id`) REFERENCES `app`(`id`) ON DELETE CASCADE;


DROP TABLE IF EXISTS users_profile;
DROP TABLE IF EXISTS users_pro_contact;
DROP TABLE IF EXISTS users_pro_econtact;
DROP TABLE IF EXISTS users_pro_school;
DROP TABLE IF EXISTS users_pro_work;

CREATE TABLE users_profile (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `domain_id` varchar(32) NOT NULL,
  `first_name` varchar(100) default NULL,
  `last_name` varchar(100) default NULL,
  `dob` date default NULL,
  `sex` varchar(1) default NULL,
  `political` varchar(100) default NULL,
  `religion` varchar(100) default NULL,
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `timezone` int(2) default NULL,
  `status_message` varchar(200) default NULL,
  `status_update_time` datetime default NULL,
  `pic_url` varchar(1024) default NULL,
  `pic_big_url` varchar(1024) default NULL,
  `pic_small_url` varchar(1024) default NULL,
  `pic_square_url` varchar(1024) default NULL,
  `activities` text default NULL,
  `interests` text default NULL,
  `music` text default NULL,
  `tv` text default NULL,
  `movies` text default NULL,
  `books` text default NULL,
  `quotes` text default NULL,
  `about` text default NULL,
  `relationship_status` varchar(200) default NULL,
  `alternate_name` varchar(100) default NULL,
  `significant_other` int(10) unsigned default NULL,
  `meeting_for` varchar(200) default NULL,
  `meeting_sex` varchar(3) default NULL,
  `layout` text,
  PRIMARY KEY (`id`,`user_id`),  
  KEY (`user_id`),
  CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE  users_pro_contact(
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL,
  `home_phone` varchar(20) default NULL,
  `mobile_phone` varchar(20) default NULL,
  `address` varchar(200) default NULL,
  `city` varchar(100) NOT NULL default '',
  `state` varchar(100) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  `zip` varchar(15) NOT NULL default '',
  `website` varchar(500) default NULL,
  `is_hometown` tinyint(1) default NULL,
  `is_current` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY (`profile_id`),
  FOREIGN KEY (`profile_id`) REFERENCES users_profile(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  users_pro_econtact(
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL,
  `contact_type` varchar(100) default NULL,
  `contact_value` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY (`profile_id`),
  FOREIGN KEY (`profile_id`) REFERENCES `users_profile`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_pro_school` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `grad_year` int(4) default NULL,
  `concentrations` varchar(500) default NULL,
  `is_highschool` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  FOREIGN KEY (`profile_id`) REFERENCES `users_profile`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  users_pro_work(
  `id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL,
  `employer` varchar(100) default NULL,
  `position` varchar(50) default NULL,
  `description` text,
  `city` varchar(100) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(50) default NULL,
  `current` tinyint(1) default NULL,
  `start_date` date NOT NULL,
  `end_date` date default NULL,
  PRIMARY KEY  (`id`),
  FOREIGN KEY (`profile_id`) REFERENCES `users_profile`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
