
UPDATE `engine4_core_menuitems` SET `params` = '{"route":"classified_general","icon":"fa fa-newspaper"}' WHERE `name` = 'core_main_classified';

--
-- indexing for table `engine4_classified_classifieds`
--
ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`modified_date`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`creation_date`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`like_count`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`comment_count`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`view_count`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`view_privacy`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`closed`);

ALTER TABLE `engine4_classified_classifieds` ADD INDEX(`category_id`);

ALTER TABLE `engine4_classified_fields_meta` ADD `icon` TEXT NULL DEFAULT NULL;
