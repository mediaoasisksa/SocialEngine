/* Queries to make the admin side tab Configure Licences, Plugin installation, Pending Plugin Activations and save keys */
 
 INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES (NULL, 'seaocore_admin_licence_configure', 'seaocore', 'Reconfigure Licences', NULL, '{\"route\":\"admin_default\",\"module\":\"seaocore\",\"controller\":\"plugin-manage\",\"action\":\"index\"}', 'seaocore_admin_main', NULL, '0', '9', '1');

INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES (NULL, 'seaocore_admin_plugin_installation', 'seaocore', 'Plugin Installations', NULL, '{\"route\":\"admin_default\",\"module\":\"seaocore\",\"controller\":\"plugin-manage\",\"action\":\"not-installed\"}', 'seaocore_admin_main', NULL, '0', '11', '1');

 INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES (NULL, 'seaocore_admin_plugin_notactivated', 'seaocore', 'Pending Plugin Activation', NULL, '{\"route\":\"admin_default\",\"module\":\"seaocore\",\"controller\":\"plugin-manage\",\"action\":\"not-activated\"}', 'seaocore_admin_main', NULL, '0', '13', '1');

INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES (NULL, 'seaocore_admin_plugin_keys', 'seaocore', 'Configuration of Keys', NULL, '{\"route\":\"admin_default\",\"module\":\"seaocore\",\"controller\":\"plugin-manage\",\"action\":\"save-keys\"}', 'seaocore_admin_main', NULL, '0', '15', '1');
