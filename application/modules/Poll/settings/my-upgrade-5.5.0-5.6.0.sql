ALTER TABLE `engine4_poll_polls` ADD `photo_id` INT(11) NOT NULL DEFAULT "0";


-- INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `editable`, `is_generated`) VALUES
-- ('poll_cover_photo_update', 'poll', '{item:$subject} has updated {item:$object} cover photo.', 1, 5, 1, 4, 1, 0, 1);

ALTER TABLE `engine4_poll_polls` ADD `coverphoto` INT ( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_poll_polls` ADD INDEX(`coverphoto`);

-- coverphotoupload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'poll' as `type`,
    'coverphotoupload' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');



DROP TABLE IF EXISTS `engine4_poll_albums` ;
CREATE TABLE `engine4_poll_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  `coverphotoparams` VARCHAR ( 265 ) NULL,
  `type` VARCHAR ( 265 ) NULL,
   PRIMARY KEY (`album_id`),
   KEY (`poll_id`),
   KEY (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


DROP TABLE IF EXISTS `engine4_poll_photos`;
CREATE TABLE `engine4_poll_photos` (
  `photo_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`photo_id`),
  KEY (`album_id`),
  KEY (`poll_id`),
  KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
