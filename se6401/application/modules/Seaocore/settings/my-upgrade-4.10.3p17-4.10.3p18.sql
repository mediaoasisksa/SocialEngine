
DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'seaocore_admin_licence_configure'
AND `module` = 'seaocore';


DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'seaocore_admin_plugin_installation'
AND `module` = 'seaocore';

DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'seaocore_admin_plugin_notactivated'
AND `module` = 'seaocore';


DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'seaocore_admin_upgrade'
AND `module` = 'seaocore';


DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'seaocore_admin_news'
AND `module` = 'seaocore';


UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"seaocore","controller":"settings","action":"index"}' WHERE `engine4_core_menuitems`.`name` ='core_admin_plugins_Seaocore';

UPDATE `engine4_core_menuitems` SET `params` = '{"route":"admin_default","module":"seaocore","controller":"settings","action":"save-keys"}' WHERE `engine4_core_menuitems`.`name` ='seaocore_admin_plugin_keys';