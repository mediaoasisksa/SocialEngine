<?php

/**
 * @package     Engine_Core
 * @version     $Id: lite.php 9747 2012-07-26 02:08:08Z john $
 * @copyright   Copyright (c) 2008 Webligo Developments
 * @license     http://www.socialengine.com/license/
 */
$enableModules = array("core", "user", "activity", "blog", "classified", "group", "event", "album", "forum", "poll", "video", "music", "advancedactivity", "sitetagcheckin", "siteevent", "siteeventrepeat", "siteforum", "sitepage", "sitepagealbum", "sitepagevideo", "sitepagereview", "sitereview");

// Config
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);
if (defined('_ENGINE_R_MAIN')) {
    return;
}
define('_ENGINE_R_CONF', true);
define('_ENGINE_R_INIT', true);
defined('APPLICATION_PATH_COR') || define('APPLICATION_PATH_COR', realpath(dirname(__FILE__)));
defined('APPLICATION_PATH_LIB') || define('APPLICATION_PATH_LIB', APPLICATION_PATH_COR . DS . 'libraries');
defined('_ENGINE') || define('_ENGINE', true);

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));
set_include_path(
        APPLICATION_PATH_LIB . PS .
        APPLICATION_PATH_LIB . DS . 'PEAR' . PS .
        '.' // get_include_path()
);

defined('APPLICATION_PATH_MOD') || define('APPLICATION_PATH_MOD', APPLICATION_PATH_COR . DS . 'modules');
defined('APPLICATION_PATH_TMP') || define('APPLICATION_PATH_TMP', APPLICATION_PATH . DS . 'temporary');

if(file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR.'engineFunctions.php'))
  include dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . 'engineFunctions.php';

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
//defined('_ENGINE_SSL') || define('_ENGINE_SSL', (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on'));
defined('_ENGINE_SSL') || define('_ENGINE_SSL', ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')));

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
//for api Caching..........................
if ($_SERVER['REQUEST_METHOD'] === 'GET')
    restapiCache();

//.........................................
$server = new Siteapi_ApiBootstrap();
$server->run();

function restapiCache() {

    if (isset($_REQUEST['page']) && !empty($_REQUEST['page']) && ($_REQUEST['page'] > 1))
        return;

    // In case of advanced activity feed pagination, cache will not work
    if (isset($_REQUEST['maxid']) && !empty($_REQUEST['maxid']))
        return;

    if (isset($_REQUEST['action_id']))
        return;

    $getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);
    $urlarray = explode("?", $getRequestUri);
    $trimData = trim($urlarray[0], "/");
    $cachName = str_replace("/", "_", $trimData);
    $file = APPLICATION_PATH . '/application/settings/database.php';
    $options = include $file;
    $user_id = $level_id = 0;
    $language = $_REQUEST['language'];
    if (isset($_REQUEST['oauth_token'])) {
        $db = Zend_Db::factory($options['adapter'], $options['params']);
//Engine_Db_Table::setDefaultAdapter($db);
//Engine_Db_Table::setTablePrefix($options['tablePrefix']);
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_siteapi_oauth_tokens')
                ->where('token = ?', $_REQUEST['oauth_token'])
                ->limit(1);
        $authObject = $select->query()->fetchObject();
        if($authObject){
            $user_id = $authObject->user_id;
            $select1 = $select = new Zend_Db_Select($db);
            $select1->from('engine4_users')
                ->where('user_id = ?', $user_id)
                ->limit(1);
            $level_id = $select->query()->fetchObject()->level_id;
        }
    }


    $keys = array_keys($_REQUEST);
    $matched = preg_grep('/_id|_type$/', $keys);
    $matched = array_flip($matched);
    $matched = array_intersect_key($_REQUEST, $matched);
    $key_val = implode('_', $matched);

    if (!strstr($cachName, "advancedactivity_feeds") && !strstr($cachName, "user_profile"))
        $cachName = $cachName . '_' . $language . '_' . $key_val . '_' . $level_id;
    else {
        $cachName = $cachName . '_' . $language . '_' . $key_val . '_' . $user_id;
    }
    $cachName = str_replace('-', '_', $cachName);
     $cachName = str_replace('.', '_', $cachName);

    $settingFile = APPLICATION_PATH . '/application/settings/restapi_cache.php';
    $path = APPLICATION_PATH . '/temporary/restapicache';
    if (file_exists($settingFile)) {
        $currentCache = include $settingFile;
        $backendOptions = $currentCache['backend']['File'];
        $frontendOptions = $currentCache['frontend'];
        $isEnableCache = $frontendOptions['caching'];
        if (empty($isEnableCache))
            return;
    }
    else {
        $frontendOptions = array(
            'lifetime' => 300, // cache lifetime of 2 hours
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/temporary/restapicache' // Directory where to put the cache files
        );
    }

    if (!is_dir($path)) {
        mkdir($path,0777);
    }


// getting a Zend_Cache_Core object
    $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
    $result = $cache->load($cachName);
    if (!$result) {
        return;
    } else {
        $response['status_code'] = 200;
        $response['body'] = $result;
        $data = @json_encode($response, JSON_NUMERIC_CHECK);
        print_r($data);
        exit();
    }
//.........................................
}
