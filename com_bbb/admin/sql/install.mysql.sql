CREATE TABLE IF NOT EXISTS `#__bigbluebutton_meetings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` varchar(40) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `meetingName` varchar(50) NOT NULL,
  `meeting_description` text NOT NULL,
  `moderatorPW` varchar(40) NOT NULL,
  `attendeePW` varchar(40) NOT NULL,
  `voiceBridge` int(11) NOT NULL DEFAULT '12345',
  `maxParticipants` int(5) NOT NULL DEFAULT '12',
  `record` varchar(6) NOT NULL DEFAULT 'true',
  `event_moderators` text NOT NULL,
  `allowed_users` text NOT NULL,  
  `allowed_groups` text NOT NULL,
  `start_time` bigint(10) NOT NULL,
  `display_time` bigint(10) NOT NULL,
  `end_time` bigint(10) NOT NULL,
  `all_moderator` tinyint(5) NOT NULL DEFAULT '0',	
  `event_isopen` tinyint(5) NOT NULL DEFAULT '0',	
  `duration` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE `#__bigbluebutton_category` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__bigbluebutton_type` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__bigbluebutton_recordings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`meeting_id` int(11) NOT NULL,
	`recording_id` varchar(255) NOT NULL,
	`recording_title` text NOT NULL,
	`recording_description` text NOT NULL,
	`recording_url` text NOT NULL,
	`start_time` bigint(20) NOT NULL,
	`end_time` bigint(20) NOT NULL,
	PRIMARY KEY (`id`)
);


CREATE TABLE `#__bigbluebutton_excludedgroups` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`group_id` int(11) NOT NULL,
	`group_name` text NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `#__bigbluebutton_freeusers` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`event_id` int(11) NOT NULL,
	`users_id` text NOT NULL,
	`users_count` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);


ENGINE=MyISAM CHARSET=utf8;

