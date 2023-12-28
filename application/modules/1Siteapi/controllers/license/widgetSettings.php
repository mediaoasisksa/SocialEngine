<?php

$db = Engine_Db_Table::getDefaultAdapter();
$menuTable = Engine_Api::_()->getDbTable('menuItems', 'core');

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteapi_admin_api_clients\' LIMIT 1')->fetch();
if (empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteapi_admin_api_clients';
    $row->module = 'siteapi';
    $row->label = 'API Consumers';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteapi","controller":"consumers", "action":"manage"}';
    $row->menu = 'siteapi_admin_main';
    $row->submenu = '';
    $row->order = 1;
    $row->save();
}

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteapi_admin_api_caching\' LIMIT 1')->fetch();
if (empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteapi_admin_api_caching';
    $row->module = 'siteapi';
    $row->label = 'API Caching';
    $row->plugin = '';
    $row->params = '{"route":"admin_default","module":"siteapi","controller":"api-cache"}';
    $row->menu = 'siteapi_admin_main';
    $row->submenu = '';
    $row->order = 2;
    $row->save();
}

$isRowExist = $db->query('SELECT * FROM `engine4_core_menuitems` WHERE `name` LIKE \'siteapi_admin_api_documentation\' LIMIT 1')->fetch();
if (empty($isRowExist)) {
    $row = $menuTable->createRow();
    $row->name = 'siteapi_admin_api_documentation';
    $row->module = 'siteapi';
    $row->label = 'API Documentation';
    $row->plugin = 'Siteapi_Plugin_Menus::apiDocumentation';
    $row->params = '';
    $row->menu = 'siteapi_admin_main';
    $row->submenu = '';
    $row->order = 3;
    $row->save();
}

$key = Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString();
$secret = Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString();

if (!empty($key) && !empty($secret)) {
    $table = Engine_Api::_()->getDbtable('consumers', 'siteapi');
    $row = $table->createRow();
    $row->setFromArray(array(
        'title' => 'Default Consumer',
        'key' => $key,
        'secret' => $secret
    ));
    $row->save();
}
