<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 2022-06-21
 */

return array (
  'package' =>
  array(
    'type' => 'module',
    'name' => 'elpis',
    'sku' => 'elpis',
    'version' => '6.2.2',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '5.0.0',
      ),
    ),
    'path' => 'application/modules/Elpis',
    'title' => 'Elpis Theme',
    'description' => 'Elpis Theme',
    'author' => 'WebligoDevelopments',
    'callback' => array(
        'path' => 'application/modules/Elpis/settings/install.php',
        'class' => 'Elpis_Installer',
    ),
    'actions' =>
    array(
        0 => 'install',
        1 => 'upgrade',
        2 => 'refresh',
        3 => 'enable',
        4 => 'disable',
    ),
    'directories' =>
    array(
      'application/modules/Elpis',
      'application/themes/elpis',
    ),
    'files' =>
    array(
      'application/languages/en/elpis.csv',
    ),
  ),
  'items' => array(
    'elpis_customthemes',
  ),
	// Hooks ---------------------------------------------------------------------
	'hooks' => array(
		array(
			'event' => 'onRenderLayoutDefault',
			'resource' => 'Elpis_Plugin_Core'
		),
	),
);
