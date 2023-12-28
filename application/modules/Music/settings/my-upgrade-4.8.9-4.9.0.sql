--
-- updating data for table `engine4_activity_actiontypes`
--

UPDATE `engine4_activity_actiontypes` SET `commentable`=3, `shareable`=3 WHERE `type`='comment_playlist';

UPDATE `engine4_activity_actiontypes` SET `type`='comment_music_playlist' WHERE `type`='comment_playlist';

--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_music_playlists` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_music_playlists` SET `like_count`=(SELECT COUNT('like_id')
                                                    FROM `engine4_core_likes`
                                                    WHERE `resource_type`='music_playlist' and `resource_id`=`engine4_music_playlists`.`playlist_id`
                                                  );
--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_music_playlist', 'music', '{item:$subject} liked {item:$owner}''s {item:$object:playlist}.', 1, 1, 1, 3, 3, 0);


--
-- Dumping data for table `engine4_core_banners`
--

INSERT IGNORE INTO `engine4_core_banners`(`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('music', 'music', 'Create Your Own Playlists', 'Dance; relax; workout with Music!', 0, '{"label":"Upload Music","route":"music_general","routeParams":{"action":"create"}}', 0);

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"music_general","action":"browse","icon":"fa-music"}'
WHERE `name` = 'core_main_music';
