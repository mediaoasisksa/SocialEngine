ALTER TABLE `engine4_blog_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_blog_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_blog_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_blog_blogs` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_blog_blogs` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
