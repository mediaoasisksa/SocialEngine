--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_event_events` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `view_count`;

UPDATE `engine4_event_events` SET `like_count`=(SELECT COUNT('like_id')
                                                  FROM `engine4_core_likes`
                                                  WHERE `resource_type`='event' and `resource_id`=`engine4_event_events`.`event_id`
                                               );

ALTER TABLE `engine4_event_albums` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_event_albums` SET `like_count`=(SELECT COUNT('like_id')
                                                  FROM `engine4_core_likes`
                                                  WHERE `resource_type`='event_album' and `resource_id`=`engine4_event_albums`.`album_id`
                                               );

ALTER TABLE `engine4_event_photos` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_event_photos` SET `like_count`=(SELECT COUNT('like_id')
                                                  FROM `engine4_core_likes`
                                                  WHERE `resource_type`='event_photo' and `resource_id`=`engine4_event_photos`.`photo_id`
                                               );

--
-- Adding and populating `comment_count` column
--

ALTER TABLE `engine4_event_events` ADD `comment_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `view_count`;

UPDATE `engine4_event_events` SET `comment_count`=(SELECT COUNT('comment_id')
                                                  FROM `engine4_core_comments`
                                                  WHERE `resource_type`='event' and `resource_id`=`engine4_event_events`.`event_id`
                                               );

--
-- Dumping data for table `engine4_core_banners`
--

INSERT IGNORE INTO `engine4_core_banners`(`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('event', 'event', 'Your Source for Events', 'Don’t miss the action! Create, join and explore Events.', 0, '{"label":"Create New Event","route":"event_general","routeParams":{"action":"create"}}', 0);

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"event_general","icon":"fa-calendar"}'
WHERE `name` = 'core_main_event';
