--
-- indexing for table `engine4_forum_forums`
--
ALTER TABLE `engine4_forum_forums` ADD INDEX(`order`);

--
-- indexing for table `engine4_forum_posts`
--
ALTER TABLE `engine4_forum_posts` ADD INDEX(`creation_date`);

--
-- indexing for table `engine4_forum_topics`
--
ALTER TABLE `engine4_forum_topics` ADD INDEX(`creation_date`);


UPDATE `engine4_core_menuitems` SET `params` = '{"route":"forum_general","icon":"fa fa-comments"}' WHERE `name` = 'core_main_forum';
