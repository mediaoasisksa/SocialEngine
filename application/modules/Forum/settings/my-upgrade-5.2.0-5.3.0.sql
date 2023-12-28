INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('forum_topic_subscribed', 'forum', '{item:$subject} has {item:$postGuid:posted} on a {item:$object:forum topic} you subscribed to.', 0, '')
;