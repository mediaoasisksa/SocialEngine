
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('seaocore_admin_upgrade', 'sitecore', 'Plugin Upgrades', '', '{"route":"admin_default","module":"sitecore","controller":"settings","action":"upgrade"}', 'seaocore_admin_main', '', 0),
('seaocore_admin_news', 'sitecore', 'News', '', '{"route":"admin_default","module":"sitecore","controller":"settings","action":"news"}', 'seaocore_admin_main', '', 2);


-- -----------------------------------------------------------------
-- Newly added tabs for the license configuration work and some other tabs

 INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES ('seaocore_admin_licence_configure', 'sitecore', 'Reconfigure Licences', NULL, '{"route":"admin_default","module":"sitecore","controller":"plugin-manage","action":"index"}', 'seaocore_admin_main', NULL, '0', '9', '1'),
('seaocore_admin_plugin_installation', 'sitecore', 'Plugin Installations', NULL, '{"route":"admin_default","module":"sitecore","controller":"plugin-manage","action":"not-installed"}', 'seaocore_admin_main', NULL, '0', '11', '1'),
('seaocore_admin_plugin_notactivated', 'sitecore', 'Pending Plugin Activation', NULL, '{"route":"admin_default","module":"sitecore","controller":"plugin-manage","action":"not-activated"}', 'seaocore_admin_main', NULL, '0', '13', '1');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES
('Background Inviter', 'seaocore', 'Seaocore_Plugin_Task_Invite', 15);
