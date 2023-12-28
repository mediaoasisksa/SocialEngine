<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2021 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: manifest.php 10111 2013-10-31 05:05:49Z andres $
 * @author     Donna
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'travel',
    'version' => '6.2.0',
    'revision' => '$Revision: 10111 $',
    'path' => 'application/modules/Travel',
    'repository' => 'socialengine.com',
    'title' => 'Travel',
    'description' => 'Travel',
    'author' => 'SocialEngine',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '5.0.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Travel/settings/install.php',
      'class' => 'Travel_Installer',
    ),
    'directories' => array(
      'application/modules/Travel',
    ),
    'files' => array(
      'application/languages/en/travel.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Travel_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Travel_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'travel',
    'travel_album',
    'travel_category',
    'travel_photo'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'travel_extended' => array(
      'route' => 'travels/:controller/:action/*',
      'defaults' => array(
        'module' => 'travel',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),
    ),
    'travel_general' => array(
      'route' => 'travels/:action/*',
      'defaults' => array(
        'module' => 'travel',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|manage|create|upload-photo)',
      ),
    ),
    'travel_specific' => array(
      'route' => 'travels/:action/:travel_id/*',
      'defaults' => array(
        'module' => 'travel',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'travel_id' => '\d+',
        'action' => '(delete|edit|close|success)',
      ),
    ),
    'travel_entry_view' => array(
      'route' => 'travels/:user_id/:travel_id/:slug',
      'defaults' => array(
        'module' => 'travel',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'travel_id' => '\d+'
      )
    ),
  ),
);
