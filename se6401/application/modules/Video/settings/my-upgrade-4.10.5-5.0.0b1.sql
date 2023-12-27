UPDATE `engine4_core_menuitems` SET `params` = '{"route":"video_general","icon":"fa-video"}' WHERE `name` = 'core_main_video';

--
-- indexing for table `engine4_video_videos`
--
ALTER TABLE `engine4_video_videos` ADD INDEX(`parent_type`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`parent_id`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`like_count`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`type`);
ALTER TABLE `engine4_video_videos` ADD INDEX(`view_privacy`);
