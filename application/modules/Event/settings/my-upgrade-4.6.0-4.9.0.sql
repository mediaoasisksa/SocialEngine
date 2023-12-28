
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `editable`, `is_generated`) VALUES
('post_event', 'event', '{actors:$subject:$object}: {body:$body}', 1, 7, 1, 4, 1, 1, 0)
;

UPDATE `engine4_activity_actions`
SET `type` = 'post_event'
WHERE `type` = 'post' AND `object_type` LIKE 'event';
