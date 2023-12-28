ALTER TABLE `engine4_group_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_group_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_group_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_group_groups` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_group_groups` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
