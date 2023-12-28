ALTER TABLE `engine4_group_groups` CHANGE `rating` `rating` FLOAT NOT NULL DEFAULT '0';
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`,  `body`,  `enabled`,  `displayable`,  `attachable`,  `commentable`,  `shareable`, `is_generated`) VALUES
('group_video_new', 'group', '{item:$subject} posted a new video:', '1', '6', '1', '4', '1', 0),
('group_event_create', 'group', '{item:$subject} created a new event:', '1', '6', '1', '4', '1', 0),
('group_poll_new', 'group', '{item:$subject} created a new poll:', '1', '6', '1', '4', '1', 0),
('group_blog_new', 'group', '{item:$subject} wrote a new blog entry:', '6', '5', '1', '4', '1', 0);
