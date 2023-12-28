
ALTER TABLE `engine4_event_events` CHANGE `view_privacy` `view_privacy` VARCHAR(24) NOT NULL DEFAULT 'everyone';

UPDATE `engine4_event_events` SET `view_privacy` = 'everyone' WHERE (`view_privacy` = '' OR `view_privacy` IS NULL);
