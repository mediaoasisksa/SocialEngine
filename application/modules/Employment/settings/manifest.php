<?php return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'employment',
    'version' => '6.2.0',
    'path' => 'application/modules/Employment',
    'title' => 'Employment',
    'description' => '',
    'author' => 'SocialEngine',
      'dependencies' => array(
          array(
              'type' => 'module',
              'name' => 'core',
              'minVersion' => '5.0.0',
          ),
      ),
    'callback' =>
    array (
        'path' => 'application/modules/Employment/settings/install.php',
        'class' => 'Employment_Installer',
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => array(
      'application/modules/Employment',
    ),
    'files' => array(
      'application/languages/en/employment.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Employment_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Employment_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'employment',
    'employment_album',
    'employment_category',
    'employment_photo'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'employment_extended' => array(
      'route' => 'employments/:controller/:action/*',
      'defaults' => array(
        'module' => 'employment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),
    ),
    'employment_general' => array(
      'route' => 'employments/:action/*',
      'defaults' => array(
        'module' => 'employment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|manage|create|upload-photo|fields)',
      ),
    ),
    'employment_specific' => array(
      'route' => 'employments/:action/:employment_id/*',
      'defaults' => array(
        'module' => 'employment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'employment_id' => '\d+',
        'action' => '(delete|edit|close|success)',
      ),
    ),
    'employment_entry_view' => array(
      'route' => 'employments/:user_id/:employment_id/:slug',
      'defaults' => array(
        'module' => 'employment',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'employment_id' => '\d+'
      )
    ),
  ),
);
