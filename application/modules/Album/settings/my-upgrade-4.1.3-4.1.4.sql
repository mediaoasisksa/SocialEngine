
ALTER TABLE `engine4_album_albums` CHANGE `type` `type` ENUM( 'wall', 'profile', 'message', 'blog' ) NULL DEFAULT NULL;
