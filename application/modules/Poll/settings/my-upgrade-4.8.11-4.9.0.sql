--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_poll_polls` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_poll_polls` SET `like_count`=(SELECT COUNT('like_id')
                                                  FROM `engine4_core_likes`
                                                  WHERE `resource_type`='poll' and `resource_id`=`engine4_poll_polls`.`poll_id`
                                               );

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_poll', 'poll', '{item:$subject} liked {item:$owner}''s {item:$object:poll}.', 1, 1, 1, 3, 3, 0);

--
-- updating data for table `engine4_activity_actiontypes`
--

UPDATE `engine4_activity_actiontypes` SET `commentable`=3, `shareable`=3 WHERE `type`='comment_poll';

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"poll_general","action":"browse","icon":"fa-bar-chart"}'
WHERE `name` = 'core_main_poll';
