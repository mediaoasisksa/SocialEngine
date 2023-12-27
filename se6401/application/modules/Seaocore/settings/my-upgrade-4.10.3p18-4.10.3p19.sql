CREATE TABLE IF NOT EXISTS `engine4_seaocore_notifications` (
	`notification_id` int(11) NOT NULL,
	`show` TINYINT( 1 ) NOT NULL DEFAULT '0',
	PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;