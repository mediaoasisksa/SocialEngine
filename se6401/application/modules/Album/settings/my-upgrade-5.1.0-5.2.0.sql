
UPDATE `engine4_core_menuitems` SET `params`='{"route":"album_general", "controller": "index", "action":"browse-photos","icon":"fa fa-images"}' WHERE `name`='album_main_browse_photos';
UPDATE `engine4_core_menuitems` SET `params`='{"route":"album_general","action":"browse","icon":"fa fa-image"}' WHERE `name`='album_main_browse';
UPDATE `engine4_core_menuitems` SET `params`='{"route":"album_general","action":"manage","icon":"fa fa-user"}' WHERE `name`='album_main_manage';
UPDATE `engine4_core_menuitems` SET `params`='{"route":"album_general","action":"upload","icon":"fa fa-plus"}' WHERE `name`='album_main_upload';
