ALTER TABLE `engine4_blog_blogs` ADD `parent_type` VARCHAR(64) NOT NULL, ADD `parent_id` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `engine4_blog_blogs` ADD INDEX( `parent_type`, `parent_id`);
