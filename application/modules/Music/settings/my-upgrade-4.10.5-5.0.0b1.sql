--
-- indexing for table `engine4_music_playlists`
--
ALTER TABLE `engine4_music_playlists` ADD INDEX(`search`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`profile`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`special`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`modified_date`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`view_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`comment_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`like_count`);
ALTER TABLE `engine4_music_playlists` ADD INDEX(`view_privacy`);

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"music_general","action":"browse","icon":"fa fa-music"}' WHERE `name` = 'core_main_music';
