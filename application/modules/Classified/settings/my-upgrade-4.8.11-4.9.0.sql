--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_classified_classifieds` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_classified_classifieds` SET `like_count`=(SELECT COUNT('like_id')
                                                            FROM `engine4_core_likes`
                                                            WHERE `resource_type`='classified' and `resource_id`=`engine4_classified_classifieds`.`classified_id`
                                                         );

ALTER TABLE `engine4_classified_albums` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_classified_albums` SET `like_count`=(SELECT COUNT('like_id')
                                                      FROM `engine4_core_likes`
                                                      WHERE `resource_type`='classified_album' and `resource_id`=`engine4_classified_albums`.`album_id`
                                                    );

ALTER TABLE `engine4_classified_photos` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `file_id`;

UPDATE `engine4_classified_photos` SET `like_count`=(SELECT COUNT('like_id')
                                                      FROM `engine4_core_likes`
                                                      WHERE `resource_type`='classified_photo' and `resource_id`=`engine4_classified_photos`.`photo_id`
                                                    );

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_classified', 'classified', '{item:$subject} liked {item:$owner}''s {item:$object:classified listing}.', 1, 1, 1, 3, 3, 0);

--
-- updating data for table `engine4_activity_actiontypes`
--

UPDATE `engine4_activity_actiontypes`
SET `commentable`=3,
    `shareable`=3,
    `body`='{item:$subject} commented on {item:$owner}''s {item:$object:classified listing}.'
WHERE `type`='comment_classified';

--
-- Dumping data for table `engine4_core_banners`
--

INSERT IGNORE INTO `engine4_core_banners`(`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('classified', 'classified', 'Find Treasures', 'Find some great things or post your own in the Classifieds!', 0, '{"label":"Post a New Listing","route":"classified_general","routeParams":{"action":"create"}}', 0);

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"classified_general","icon":"fa-file-text-o"}'
WHERE `name` = 'core_main_classified';
