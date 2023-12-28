
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"poll_general","action":"browse","icon":"fa fa-chart-bar"}' WHERE `name` = 'core_main_poll';
--
-- indexing for table `engine4_poll_polls`
--
ALTER TABLE `engine4_poll_polls` ADD INDEX(`view_privacy`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`view_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`like_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`vote_count`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`search`);
ALTER TABLE `engine4_poll_polls` ADD INDEX(`closed`);

UPDATE `engine4_authorization_permissions`
SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]'
WHERE
  `params` = '["everyone","owner_network","owner_member_member","owner_member","owner"]' &&
  `type` = 'poll' &&
  `name` IN('auth_view', 'auth_comment');

