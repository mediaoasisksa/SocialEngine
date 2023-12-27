ALTER TABLE `engine4_classified_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_classified_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_classified_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_classified_classifieds` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_classified_classifieds` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
