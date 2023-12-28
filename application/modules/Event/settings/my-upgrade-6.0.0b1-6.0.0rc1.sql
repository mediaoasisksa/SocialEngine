ALTER TABLE `engine4_event_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_event_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_event_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_event_events` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_event_events` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
