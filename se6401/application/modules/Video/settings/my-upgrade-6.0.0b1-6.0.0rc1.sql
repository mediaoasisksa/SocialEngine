ALTER TABLE `engine4_video_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_videos` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_video_videos` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
