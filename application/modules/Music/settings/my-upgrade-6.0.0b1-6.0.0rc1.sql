UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} commented on {item:$owner}''s {item:$object:playlist}.' WHERE `engine4_activity_actiontypes`.`type` = 'comment_music_playlist';
