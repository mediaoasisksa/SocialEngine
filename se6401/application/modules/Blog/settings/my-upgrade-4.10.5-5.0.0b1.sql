UPDATE `engine4_core_menuitems` SET `params` = '{"route":"blog_general","icon":"fa fa-pencil-alt"}' WHERE `name` = 'core_main_blog';

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`creation_date`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`modified_date`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`view_count`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`comment_count`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`like_count`);

ALTER TABLE `engine4_blog_blogs` ADD INDEX(`view_privacy`);

UPDATE `engine4_authorization_permissions`
SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","member","owner"]'
WHERE
  `params` = '["everyone","owner_network","owner_member_member","owner_member","owner"]' &&
  `type` = 'blog' &&
  `name` IN('auth_view', 'auth_comment');
