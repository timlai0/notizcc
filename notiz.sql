CREATE TABLE `notes` (
	`notes_id` int(11) NOT NULL AUTO_INCREMENT,
	`notes_name` text COLLATE utf8_unicode_ci NOT NULL,
	`notes_content` longtext COLLATE utf8_unicode_ci NOT NULL,
	`notes_creator` int(11) NOT NULL,
	`notes_lastchange` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`notes_lastchanger` int(11) NOT NULL,
	`notes_password` text COLLATE utf8_unicode_ci,
	`notes_adminonly` tinyint(1) NOT NULL DEFAULT '0',
	`notes_change` int(11) NOT NULL DEFAULT '0',
	`REMOTE_ADDR` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
	`HTTP_X_FORWARDED_FOR` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
	PRIMARY KEY (`notes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7690 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user` (
	`user_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`user_pass` text CHARACTER SET utf8 COLLATE utf8_bin,
	`user_admin` tinyint(1) NOT NULL DEFAULT '0',
	`lastlogin` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`lastnote` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`favednotes` varchar(20) DEFAULT NULL,
	`user_online` tinyint(1) NOT NULL DEFAULT '0',
	`regDate` date NOT NULL,
	PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1