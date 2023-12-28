<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'CustomTheme',
    'version' => '4.0.0',
    'path' => 'application/modules/CustomTheme',
    'title' => 'CustomTheme',
    'description' => 'CustomTheme',
    'author' => '',
    'callback' => array(
      'path' => 'application/modules/CustomTheme/settings/install.php',
      'class' => 'CustomTheme_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/CustomTheme',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/CustomTheme.csv',
      1=> 'public/banner/e0/63/6b5a7627af4e7aefa9a8f00ed282c5f4.png',
      2=> 'public/banner/e1/63/fc57e693a462817965ff818ea40b4292.png',
      3=> 'public/banner/e2/63/c09525b2c9b0155d635a3a56b7506ca1.png'
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'CustomTheme_banner',
),
); ?>