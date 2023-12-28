<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10111 2013-10-31 05:05:49Z andres $
 * @author     Jung
 */
return array (
    // Package -------------------------------------------------------------------
  'package' =>
  array (
    'type' => 'module',
    'name' => 'bizlist',
    'version' => '6.2.0',
    'path' => 'application/modules/Bizlist',
      'repository' => 'socialengine.com',
    'title' => 'Business',
    'description' => '',
    'author' => 'SocialEngine',
      'dependencies' => array(
          array(
              'type' => 'module',
              'name' => 'core',
              'minVersion' => '5.0.0',
          ),
      ),
      'actions' =>
          array (
              0 => 'install',
              1 => 'upgrade',
              2 => 'refresh',
              3 => 'enable',
              4 => 'disable',
          ),
    'callback' =>
    array (
        'path' => 'application/modules/Bizlist/settings/install.php',
        'class' => 'Bizlist_Installer',
    ),

    'directories' =>
    array (
      0 => 'application/modules/Bizlist',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/bizlist.csv',
    ),
  ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Bizlist_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Bizlist_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'bizlist',
        'bizlist_album',
        'bizlist_category',
        'bizlist_photo'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'bizlist_extended' => array(
            'route' => 'businesses/:controller/:action/*',
            'defaults' => array(
                'module' => 'bizlist',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            ),
        ),
        'bizlist_general' => array(
            'route' => 'businesses/:action/*',
            'defaults' => array(
                'module' => 'bizlist',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|manage|create|upload-photo|fields)',
            ),
        ),
        'bizlist_specific' => array(
            'route' => 'businesses/:action/:bizlist_id/*',
            'defaults' => array(
                'module' => 'bizlist',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'bizlist_id' => '\d+',
                'action' => '(delete|edit|close|success)',
            ),
        ),
        'bizlist_entry_view' => array(
            'route' => 'businesses/:user_id/:bizlist_id/:slug',
            'defaults' => array(
                'module' => 'bizlist',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'bizlist_id' => '\d+'
            )
        ),
    ),
);
