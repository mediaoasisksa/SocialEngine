INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('group_joined', 'group', '{item:$subject} has joined the group {item:$object}.', 0, ''),
('group_leave', 'group', '{item:$subject} has left the group {item:$object}.', 0, ''),
('group_pollcreate', 'group', '{item:$subject} has posted poll in the group {item:$object}.', 0, ''),
('group_photocreate', 'group', '{item:$subject} has posted photo in the group {item:$object}.', 0, ''),
('group_discussioncreate', 'group', '{item:$subject} has posted discussion in the group {item:$object}.', 0, ''),
('group_eventcreate', 'group', '{item:$subject} has posted event in the group {item:$object}.', 0, ''),
('group_videocreate', 'group', '{item:$subject} has posted video in the group {item:$object}.', 0, ''),
('group_blogcreate', 'group', '{item:$subject} has posted blog in the group {item:$object}.', 0, ''),
('group_post', 'group', '{item:$subject} has posted a feed in the group {item:$object}.', 0, '');


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_group_leave', 'group', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('group_profile_notification', 'group', 'Notification Settings', 'Group_Plugin_Menus', '', 'group_profile', '', 99);

ALTER TABLE `engine4_group_membership` ADD `notification` TINYINT(1) NOT NULL DEFAULT '1';
