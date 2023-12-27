ALTER TABLE `engine4_album_categories` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_categories` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_categories` ADD `order` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_albums` ADD `subcat_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `engine4_album_albums` ADD `subsubcat_id` INT(11) NOT NULL DEFAULT '0';
