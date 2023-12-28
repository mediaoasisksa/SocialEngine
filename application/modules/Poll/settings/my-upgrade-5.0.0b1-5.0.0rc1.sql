ALTER TABLE `engine4_poll_polls` ADD `parent_type` VARCHAR(64) NOT NULL, ADD `parent_id` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `engine4_poll_polls` ADD INDEX( `parent_type`, `parent_id`);
