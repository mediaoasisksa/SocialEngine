--
-- indexing for table `engine4_event_events`
--
ALTER TABLE `engine4_event_events` ADD INDEX(`view_privacy`);

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"event_general","icon":"fa fa-calendar"}' WHERE `name` = 'core_main_event';
