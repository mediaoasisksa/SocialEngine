
-- Apple Signin Table

DROP TABLE IF EXISTS `engine4_user_apple`;
CREATE TABLE IF NOT EXISTS `engine4_user_apple` (
  `user_id` int(10) unsigned NOT NULL,
  `apple_id` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
   PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
