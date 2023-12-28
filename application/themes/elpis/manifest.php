<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 2022-06-20
 */

 return array (
  'package' =>
  array (
    'type' => 'theme',
    'name' => 'elpis',
    'version' => '6.2.2',
    'revision' => '$Revision: 10113 $',
    'path' => 'application/themes/elpis',
    'repository' => 'socialengine.com',
    'title' => 'Elpis',
    'thumb' => 'theme.jpg',
    'author' => 'Webligo Developments',
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' =>
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' =>
    array (
      0 => 'application/themes/elpis',
    ),
    'description' => 'Elpis',
  ),
  'files' =>
  array (
    0 => 'theme.css',
    1 => 'constants.css',
    2 => 'custom.css',
  ),
); ?>
