ALTER TABLE `engine4_travel_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_travels` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_travel_travels` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
