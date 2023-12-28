--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_video', 'video', '{item:$subject} liked {item:$owner}''s {item:$object:video}.', 1, 1, 1, 3, 3, 0);

--
-- updating data for table `engine4_activity_actiontypes`
--

UPDATE `engine4_activity_actiontypes`
SET `commentable`=3,
    `shareable`=3,
    `body`='{item:$subject} commented on {item:$owner}''s {item:$object:video}.'
WHERE `type`='comment_video';


--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_video_videos` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_video_videos` SET `like_count`=(SELECT COUNT('like_id')
                                                  FROM `engine4_core_likes`
                                                  WHERE `resource_type`='video' and `resource_id`=`engine4_video_videos`.`video_id`
                                               );

--
-- Dumping data for table `engine4_core_banners`
--

INSERT IGNORE INTO `engine4_core_banners`(`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('video', 'video', 'Grab some Popcorn', 'Play the moments. Pause the memories. Rewind the happiness. Explore Videos!', 0, '{"label":"Post New Video","route":"video_general","routeParams":{"action":"create"}}', 0);

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"video_general","icon":"fa-video-camera"}'
WHERE `name` = 'core_main_video';

--
-- Updating type of columns
--
ALTER TABLE `engine4_video_videos` CHANGE `type` `type` VARCHAR( 32 ) NOT NULL;
ALTER TABLE `engine4_video_videos` CHANGE `code` `code` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

--
-- Update `type` column in `engine4_video_videos` table
--
UPDATE `engine4_video_videos` SET `type` = 'upload' WHERE `engine4_video_videos`.`type` = '3';
UPDATE `engine4_video_videos` SET `type` = 'youtube' WHERE `engine4_video_videos`.`type` = '1';
UPDATE `engine4_video_videos` SET `type` = 'vimeo' WHERE `engine4_video_videos`.`type` = '2';
