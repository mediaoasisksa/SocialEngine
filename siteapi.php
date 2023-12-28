<?php

/**
 * @package     Engine_Core
 * @version     $Id: lite.php 9747 2012-07-26 02:08:08Z john $
 * @copyright   Copyright (c) 2008 Webligo Developments
 * @license     http://www.socialengine.com/license/
 */
//ini_set("display_errors", "1");
//  error_reporting(E_ALL);

$enableModules = array("core", "user", "activity", "blog", "classified", "group", "event", "album", "forum", "poll", "video", "music", "advancedactivity", "sitetagcheckin", "siteevent", "siteeventrepeat", "siteforum", "sitepage", "sitepagealbum", "sitepagevideo", "sitepagereview", "sitereview");

// Config
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
if (defined('_ENGINE_R_MAIN')) {
  return;
}
define('_ENGINE_R_CONF', true);
define('_ENGINE_R_INIT', true);
defined('APPLICATION_PATH_COR') ||
  define('APPLICATION_PATH_COR', realpath(dirname(__FILE__)));
defined('APPLICATION_PATH_LIB') ||
  define('APPLICATION_PATH_LIB', APPLICATION_PATH_COR . DS . 'libraries');
defined('_ENGINE') || define('_ENGINE', true);

defined('APPLICATION_PATH') ||
  define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));
set_include_path(
  APPLICATION_PATH_LIB . PS .
  APPLICATION_PATH_LIB . DS . 'PEAR' . PS .
  '.' // get_include_path()
);
// Sub apps
if (!defined('_ENGINE_R_MAIN') && !defined('_ENGINE_R_INIT')) {
  if (@$_GET['m'] == 'css') {
    define('_ENGINE_R_MAIN', 'css.php');
    define('_ENGINE_R_INIT', false);
  } else if (@$_GET['m'] == 'lite') {
    define('_ENGINE_R_MAIN', 'lite.php');
    define('_ENGINE_R_INIT', true);
  } else {
    define('_ENGINE_R_MAIN', false);
    define('_ENGINE_R_INIT', true);
  }
}

define('DEFAULT_APP_MODULES', implode(",", $enableModules));
defined('APPLICATION_NAME') || define('APPLICATION_NAME', 'Siteapi');
defined('_ENGINE_ADMIN_NEUTER') || define('_ENGINE_ADMIN_NEUTER', false);
defined('_ENGINE_NO_AUTH') || define('_ENGINE_NO_AUTH', false);
defined('_ENGINE_SSL') || define('_ENGINE_SSL', (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'));

$androidVersion = !empty($_REQUEST['_ANDROID_VERSION']) ? $_REQUEST['_ANDROID_VERSION'] : '1.6.1.1';
define('_ANDROID_VERSION', $androidVersion);

$iosVersion = !empty($_REQUEST['_IOS_VERSION']) ? $_REQUEST['_IOS_VERSION'] : '1.4.3';
define('_IOS_VERSION', $iosVersion);

$type = 'both';
if ((!empty($_REQUEST['_ANDROID_VERSION']) && empty($_REQUEST['_IOS_VERSION'])))
  $type = 'android';
elseif ((!empty($_REQUEST['_IOS_VERSION']) && empty($_REQUEST['_ANDROID_VERSION'])))
  $type = 'ios';
define('_CLIENT_TYPE', $type);


// development mode
$application_env = @$generalConfig['environment_mode'];
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (
    !empty($_SERVER['_ENGINE_ENVIRONMENT']) ? $_SERVER['_ENGINE_ENVIRONMENT'] : (
      $application_env ? $application_env :
        'production'
      )
    )
);

// Boot
if (_ENGINE_R_INIT) {

  // Application
  require_once 'Engine/Loader.php';
  require_once 'Engine/Application.php';

  // Create application, bootstrap, and run
  $application = new Engine_Application(
    array(
    'environment' => APPLICATION_ENV,
    'autoloaderNamespaces' => array(
      'Zend' => APPLICATION_PATH_LIB . DS . 'Zend',
      'Engine' => APPLICATION_PATH_LIB . DS . 'Engine',
      'Core' => APPLICATION_PATH_COR . DS . 'modules' . DS . 'Core',
      'Siteapi' => APPLICATION_PATH_COR . DS . 'modules' . DS . 'Siteapi',
    ),
    )
  );
  Engine_Application::setInstance($application);
  Engine_Api::getInstance()->setApplication($application);
}

$server = new Siteapi_ApiBootstrap();
$server->run();
