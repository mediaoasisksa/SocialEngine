--
-- Adding and populating `like_count` column
--

ALTER TABLE `engine4_blog_blogs` ADD `like_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `comment_count`;

UPDATE `engine4_blog_blogs` SET `like_count`=(SELECT COUNT('like_id')
                                                FROM `engine4_core_likes`
                                                WHERE `resource_type`='blog' and `resource_id`=`engine4_blog_blogs`.`blog_id`
                                             );

--
-- Add `photo_id` column in `engine4_blog_blogs`
--

ALTER TABLE `engine4_blog_blogs` ADD `photo_id` INT(11) unsigned NOT NULL DEFAULT '0' AFTER `category_id`;

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('like_blog', 'blog', '{item:$subject} liked {item:$owner}''s {item:$object:blog entry}.', 1, 1, 1, 3, 3, 0);
--
-- updating data for table `engine4_activity_actiontypes`
--

UPDATE `engine4_activity_actiontypes`
SET `commentable`=3,
    `shareable`=3,
    `body`='{item:$subject} commented on {item:$owner}''s {item:$object:blog entry}.'
WHERE `type`='comment_blog';

--
-- Dumping data for table `engine4_core_banners`
--

INSERT IGNORE INTO `engine4_core_banners`(`name`, `module`, `title`, `body`, `photo_id`, `params`, `custom`) VALUES
('blog', 'blog', 'Words have Power', 'Post or read Blogs for inspiration, entertainment, education and more.', 0, '{"label":"Write New Entry","route":"blog_general","routeParams":{"action":"create"}}', 0);

--
-- Update `param` column in `engine4_core_menuitems` table
--

UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"blog_general","icon":"fa-pencil"}'
WHERE `name` = 'core_main_blog';
