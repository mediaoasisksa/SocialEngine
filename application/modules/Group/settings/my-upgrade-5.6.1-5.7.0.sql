INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_group', 'group', '{item:$subject} liked {item:$owner}''s {item:$object:group}.', 1, 1, 1, 3, 3, 0);
