<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Bootstrap.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_ApiBootstrap {

    /**
     * Contains the loader/autoloader instance
     * 
     * @var Engine_Loader
     */
    static $_autoloader;

    /**
     * Calling Bootstrap
     * 
     * @var Engine_Loader
     */
    static $_bootstrap;

    /**
     * Api REST type
     */
    const API_TYPE_REST = 'rest';

    /*
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_INTERNAL_ERROR = 500;

    /**
     * Contains the registry object where application resources are stored
     *
     * @var Zend_Registry
     */
    protected $_container;

    /**
     * List of api types
     *
     * @var array
     */
    protected static $_apiTypes = array(self::API_TYPE_REST);
    protected $_notIncludeHooks = array(
        'ynforum',
        'siteforum',
        'social-connect',
        'advgroup',
        'yncontest',
        'auto-friender',
        'sdcore'
    );
    protected $_notIncludeRoute = array(
        'ynforum',
        'siteforum',
        'social-connect',
        'advgroup',
        'sitevideo',
        'sitealbum'
    );
    protected $_notIncludeItemType = array(
        'ynforum',
        'siteforum',
    );
    protected $_notIncludeListingRoute = array();
    protected $_hooks = array();
    protected $_moduleBaePath;

    public function __construct() {

        // Not include following routes for MLT
        foreach (array('blogs', 'classifieds', 'groups', 'events') as $modName) {
            $this->_notIncludeListingRoute[] = $modName . '/:action/*';
            $this->_notIncludeListingRoute[] = $modName . '/:action/:listing_id/*';
            $this->_notIncludeListingRoute[] = $modName . '/photo/:action/*';
            $this->_notIncludeListingRoute[] = $modName . '/:controller/:action/*';
            $this->_notIncludeListingRoute[] = $modName . '/photo/:listing_id/*';
        }

        $this->_moduleBaePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application'
                . DIRECTORY_SEPARATOR . 'modules';

        $this->_initDb();
        $this->_initModules();
        $this->_initCache();
        $this->_initLog();
        $this->_initManifest();
        $this->_initHooks();
        $this->_initFrontController();
        $this->_initRouter();
        $this->_initTranslate();
    }

    public function run(Zend_Controller_Request_Http $request = null) {

        try {
            $response = new Zend_Controller_Response_Http();
        } catch (Exception $e) {

            if (!headers_sent()) {
                header('HTTP/1.1 ' . self::HTTP_INTERNAL_ERROR);
            }
            echo 'Service temporary unavailable';
            return;
        }

        try {
            if (!$request) {
                $request = new Zend_Controller_Request_Http();
            }

            $request->setRequestUri(str_replace("api/rest/", "", $request->getRequestUri()));
            $front = $this->getContainer()->frontcontroller;
            $front->setParam('noViewRenderer', true);

            $front->setRequest($request);
            $front->registerPlugin(new Siteapi_Plugin_Core);

            $response = $front->dispatch($request, $response);
            $response->sendResponse();
        } catch (Exception $e) {
            $response->setHttpResponseCode(self::HTTP_INTERNAL_ERROR)
                    ->setBody('Service temporary unavailable')
                    ->sendResponse();
        }
    }

    /**
     * Set the resource container
     * 
     * @param Zend_Registry $container
     * @return Engine_Application_Bootstrap_Abstract
     */
    public function setContainer(Zend_Registry $container) {
        $this->_container = $container;
        return $this;
    }

    /**
     * Get the current resource container
     * 
     * @return Zend_Registry
     */
    public function getContainer() {
        if (null === $this->_container) {
            $this->setContainer(new Zend_Registry());
        }
        return $this->_container;
    }

    public function registerPath($prefix, $path = null) {
        Engine_Loader::getInstance()->register($prefix, $path);
    }

    protected function getModuleBootstrap() {
        if (self::$_bootstrap === null) {

            self::$_bootstrap = new Siteapi_ModulesBootstrap(Engine_Application::getInstance());
        }
        return self::$_bootstrap;
    }

    public static function getAutoloader() {
        if (null === self::$_autoloader) {
            self::$_autoloader = Engine_Loader::getInstance();
        }
        return self::$_autoloader;
    }

    protected function _initFrontController() {

        $modulePath = $this->_moduleBaePath . DIRECTORY_SEPARATOR;
        Zend_Controller_Action_HelperBroker::addPath("Engine/Controller/Action/Helper/", 'Engine_Controller_Action_Helper');

        Zend_Controller_Action_HelperBroker::addPath($modulePath . "Authorization/Controller/Action/Helper/", 'Authorization_Controller_Action_Helper');

        Zend_Controller_Action_HelperBroker::addPath($modulePath . "Core/Controller/Action/Helper/", 'Core_Controller_Action_Helper');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger')) {
            Zend_Controller_Action_HelperBroker::addHelper(new Primemessenger_Controller_Action_Helper_PrimeMessengers());
        }

        Zend_Controller_Action_HelperBroker::addPath($modulePath . "Siteapi/Controller/Action/Helper/", 'Siteapi_Controller_Action_Helper');


        $this->_loadLicense();
        $frontController = Siteapi_Controller_Front::getInstance();
        $frontController
                //->addModuleDirectory(APPLICATION_PATH . "/application/modules/")
                ->setDefaultModule('siteapi')
                ->setParam('prefixDefaultModule', 'true');

        // Add our special path for action helpers
        //$this->initActionHelperPath();
        // Our virtual index hack confuses the request class, this other hack will
        // make it think it's in the root folder
        $request = new Zend_Controller_Request_Http();
        $script = $_SERVER['SCRIPT_NAME'];
        $_SERVER['SCRIPT_NAME'] = str_replace('/application/', '/', $script);
        $frontController->setBaseUrl($request->getBaseUrl());
        $_SERVER['SCRIPT_NAME'] = $script;

        // Save to registy and local container
        Zend_Registry::set('Zend_Controller_Front', $frontController);
        $this->getContainer()->{'frontcontroller'} = $frontController;
        return $frontController;
    }

    protected function _initDb() {
        $file = APPLICATION_PATH . '/application/settings/database.php';
        $options = include $file;
        $db = Zend_Db::factory($options['adapter'], $options['params']);
        Engine_Db_Table::setDefaultAdapter($db);
        Engine_Db_Table::setTablePrefix($options['tablePrefix']);

        // set DB to UTC timezone for this session
        switch ($options['adapter']) {
            case 'mysqli':
            case 'mysql':
            case 'pdo_mysql': {
                    $db->query("SET time_zone = '+0:00'");
                    break;
                }

            case 'postgresql': {
                    $db->query("SET time_zone = '+0:00'");
                    break;
                }

            default: {
                    // do nothing
                }
        }

        // attempt to disable strict mode
        try {
            $db->query("SET SQL_MODE = ''");
        } catch (Exception $e) {
            
        }
        $resource = 'db';
        $this->getContainer()->{'db'} = $db;
        return $db;
    }

    protected function _initModules() {

        $default = 'siteapi';

        // Prepare data
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $baseDir = APPLICATION_PATH;
        $moduleBaePath = $baseDir . DIRECTORY_SEPARATOR . 'application'
                . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
        foreach ($enabledModuleNames as $module) {
            // Default module is already bootstrapped, but bootstrap others
            if (strtolower($module) === strtolower($default)) {
                continue;
            }

            $moduleInflected = Engine_Api::inflect($module);
            $moduleDir = $moduleBaePath . $moduleInflected;

            $this->registerPath(ucfirst($module), $moduleDir);
            //REGISTER COMMON BOOTSTRAP FOR ALL MODULES:
            $bootstrap = $this->getModuleBootstrap();
            //SET MODULE NAME
            $bootstrap->setModuleName($module);
            //NOW SET BOOTSTRAP
            Engine_Api::getInstance()->setModuleBootstrap($bootstrap);
        }

        //return $bootstraps;
    }

    protected function _initCache() {
        // Get configurations
        $file = APPLICATION_PATH . '/application/settings/cache.php';

        // @todo cache config in database

        if (file_exists($file)) {
            // Manual config
            $options = include $file;
        } else if (is_writable(APPLICATION_PATH . '/temporary/cache') || (
                !@is_dir(APPLICATION_PATH . '/temporary/cache') &&
                @mkdir(APPLICATION_PATH . '/temporary/cache', 0777, true)
                )) {
            // Auto default config
            $options = array(
                'default_backend' => 'File',
                'frontend' => array(
                    'core' => array(
                        'automatic_serialization' => true,
                        'cache_id_prefix' => 'Engine4_',
                        'lifetime' => '300',
                        'caching' => true,
                    ),
                ),
                'backend' => array(
                    'File' => array(
                        'cache_dir' => APPLICATION_PATH . '/temporary/cache',
                    ),
                ),
            );
        } else {
            // Failure
            return null;
        }

        // Create cache
        $frontend = key($options['frontend']);
        $backend = key($options['backend']);
        Engine_Cache::setConfig($options);
        if( in_array($backend, array('Engine_Cache_Backend_Apc', 'Engine_Cache_Backend_Redis')) ) {
          $cache = Engine_Cache::factory($frontend, $backend, array(), array(), false, true);
         } else {
           $cache = Engine_Cache::factory($frontend, $backend);
         }

        // Disable caching in development mode
        if (APPLICATION_ENV == 'development') {
            $cache->setOption('caching', false);
        }

        // Save in registry
        Zend_Registry::set('Zend_Cache', $cache);

        // Use cache helper?
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-1, new Engine_Controller_Action_Helper_Cache());

        // Add cache to database
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        $this->getContainer()->{'cache'} = $cache;
        // Save in bootstrap
        return $cache;
    }

    protected function _initLog() {
        $log = new Zend_Log();
        $log->setEventItem('domain', 'error');

        // Non-production
        if (APPLICATION_ENV !== 'production') {
            $log->addWriter(new Zend_Log_Writer_Firebug());
        }

        // Get log config
        $db = Engine_Db_Table::getDefaultAdapter();
        $logAdapter = $db->select()
                ->from('engine4_core_settings', 'value')
                ->where('`name` = ?', 'core.log.adapter')
                ->query()
                ->fetchColumn();

        // Set up log
        switch ($logAdapter) {
            case 'database': {
                    try {
                        $log->addWriter(new Zend_Log_Writer_Db($db, 'engine4_core_log'));
                    } catch (Exception $e) {
                        // Make sure logging doesn't cause exceptions
                        $log->addWriter(new Zend_Log_Writer_Null());
                    }
                    break;
                }
            default:
            case 'file': {
                    try {
                        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/main.log'));
                    } catch (Exception $e) {
                        // Check directory
                        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
                            $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/main.log'));
                        } else {
                            // Silence ...
                            if (APPLICATION_ENV !== 'production') {
                                $log->log($e->__toString(), Zend_Log::CRIT);
                            } else {
                                // Make sure logging doesn't cause exceptions
                                $log->addWriter(new Zend_Log_Writer_Null());
                            }
                        }
                    }
                    break;
                }
            case 'none': {
                    $log->addWriter(new Zend_Log_Writer_Null());
                    break;
                }
        }

        // Save to registry
        Zend_Registry::set('Zend_Log', $log);

        // Register error handlers
        Engine_Api::registerErrorHandlers();

        if ('production' != APPLICATION_ENV) {
            Engine_Exception::setLog($log);
        }
        $this->getContainer()->{'log'} = $log;
        return $log;
    }

    protected function _initHooks() {
        $hooks = Engine_Hooks_Dispatcher::getInstance();
        if (isset($temp['hooks'])) {
            $data[$apiModName]['hooks'] = $temp['hooks'];
            if (!in_array($apiModName, $this->_notIncludeHooks)) {
                $this->_hooks = array_merge($this->_hooks, $temp['hooks']);
            }
        }

        $hooks->addEvents($this->_hooks);
        return $hooks;
    }

    protected function _initManifest() {

        $data = array();
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "modules";
        $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        // Modified existing routes for APIs
        $file = $path . DIRECTORY_SEPARATOR . 'Siteapi' . '/settings/apiroutes.php';
        $apiRoutes = include($file);

        foreach ($enabledModuleNames as $module) {
            $moduleInflected = Engine_Api::inflect($module);
            $moduleDir = $path . DIRECTORY_SEPARATOR . $moduleInflected;
            $file = $moduleDir . '/settings/manifest.php';
            $apiModName = $module;
            $data[$apiModName] = array('routes' => array());
            if (file_exists($file)) {
                $temp = include($file);

                if (isset($temp['hooks'])) {
                    $data[$apiModName]['hooks'] = $temp['hooks'];
                    if (!in_array($apiModName, $this->_notIncludeHooks)) {
                        $this->_hooks = array_merge($this->_hooks, $temp['hooks']);
                    }
                }

                if (isset($temp['items']) && (!in_array($apiModName, $this->_notIncludeItemType))) {
                    $data[$apiModName]['items'] = $temp['items'];
                }

                if (isset($temp['routes']) && (is_array($this->_notIncludeRoute) && !in_array($apiModName, $this->_notIncludeRoute))) {
                    // Edit the SocialEngine default routes.
                    if (($apiModName == 'sitereview') && isset($temp['routes']) && (is_array($this->_notIncludeRoute) && !in_array($apiModName, $this->_notIncludeRoute))) {
                        foreach ($temp['routes'] as $key => $value) {
                            if (isset($value['route']) && !empty($value['route']) && in_array($value['route'], $this->_notIncludeListingRoute))
                                $temp['routes'][$key]['route'] = 'se' . @ltrim($value['route'], "/");
                        }
                    }
                    // Edit the SocialEngine default routes.
                    if (($apiModName == 'siteevent') && isset($temp['routes']) && (is_array($this->_notIncludeRoute) && !in_array($apiModName, $this->_notIncludeRoute))) {
                        foreach ($temp['routes'] as $key => $value) {
                            if (isset($value['route']) && !empty($value['route']) && in_array($value['route'], $this->_notIncludeListingRoute))
                                $temp['routes'][$key]['route'] = 'se' . @ltrim($value['route'], "/");
                        }
                    }
                    
                    if (($apiModName == 'sitegroup') && isset($temp['routes']) && (is_array($this->_notIncludeRoute) && !in_array($apiModName, $this->_notIncludeRoute))) {
                        foreach ($temp['routes'] as $key => $value) {
                            if (isset($value['route']) && !empty($value['route']) && in_array($value['route'], $this->_notIncludeListingRoute))
                                $temp['routes'][$key]['route'] = 'se' . @ltrim($value['route'], "/");
                        }
                    }

                    $data[$apiModName]['routes'] = $temp['routes'];
                }
            }
            if (!empty($apiRoutes[$apiModName])) {
                $routes = $apiRoutes[$apiModName];
                $data[$apiModName]['routes'] = array_merge($data[$apiModName]['routes'], $routes);
            }
        }

        Zend_Registry::set('Engine_Manifest', $data);
        $this->getContainer()->{'manifest'} = $data;
        return $data;
    }

    protected function _initRouter() {
        $router = $this->getContainer()->frontcontroller->getRouter();

        $defaultAdminRoute = Engine_Controller_Router_Route_ControllerPrefix::getInstance(new Zend_Config(array()));
        $router->addRoute('admin_default', $defaultAdminRoute);
        $requestUri = $_SERVER['REQUEST_URI'];
        $manifest = Zend_Registry::get('Engine_Manifest');
        foreach ($manifest as $module => $config) {
            if (!isset($config['routes']))
                continue;
            $getEnabledModulesArray = (strstr($_SERVER['REQUEST_URI'], '/api/rest/advancedactivity/feeds?') || strstr($_SERVER['REQUEST_URI'], 'api/rest/notifications?') || strstr($_SERVER['REQUEST_URI'], '/api/rest/search?') || strstr($_SERVER['REQUEST_URI'], '/api/rest/activity/')) ? Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames() : array("activity", "advancedactivity", "album", "blog", "classified", "core", "event", "forum", "group", "message", "music", "poll", "user", "video", "sitetagcheckin", "messages", "siteevent", "siteeventrepeat", "sitereview", "sitepage", "sitepagevideo", "sitepagereview", "sitepagealbum", "sitehashtag", "sitegroup", "sitegroupalbum", "sitegroupvideo", "sitegroupreview", "sitegroupmember", "sitegroupoffer", "sitegroupintegration", "siteusercoverphoto", "sitecontentcoverphoto", "sitereaction", "suggestion", "nestedcomment", "siteeventticket", "sitevideo", "sitestore", "sitestoreproduct", "sitestoreoffer", "sitestorereview", "communityad","sitemember");

            if (!in_array($module, $getEnabledModulesArray))
                continue;

            $router->addConfig(new Zend_Config($config['routes']));
        }

        // Add default routes
        $router->addDefaultRoutes();
        $this->getContainer()->{'router'} = $router;
        return $router;
    }

    /**
     * Initializes translator
     *
     * @return Zend_Translate_Adapter
     */
    protected function _initTranslate() {
        // Set translate in case of form post because translate are using in Email or Notification methods.
        if (empty($_SERVER['REQUEST_METHOD']) || !in_array($_SERVER['REQUEST_METHOD'], array('POST', 'PUT'))) {
            return;
        }
        // Set cache
        if (isset($this->getContainer()->cache)) {
            Zend_Translate::setCache($this->getContainer()->cache);
        }

        // If in development, log untranslated messages
        $params = array(
            'scan' => Zend_Translate_Adapter::LOCALE_DIRECTORY,
            'logUntranslated' => true
        );
        $log = new Zend_Log();
        if (APPLICATION_ENV == 'development') {
            $log = new Zend_Log();
            $log->addWriter(new Zend_Log_Writer_Firebug());
            $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/translate.log'));
        } else {
            $log->addWriter(new Zend_Log_Writer_Null());
        }
        $params['log'] = $log;
        // Check Locale
        $locale = Zend_Locale::findLocale();
        // Make Sure Language Folder Exist
        $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
        if ($languageFolder === false) {
            $locale = substr($locale, 0, 2);
            $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
            if ($languageFolder == false) {
                $locale = 'en';
            }
        }
        // Check which Translation Adapter has been selected
        $db = Engine_Db_Table::getDefaultAdapter();
        $translationAdapter = $db->select()
                ->from('engine4_core_settings', 'value')
                ->where('`name` = ?', 'core.translate.adapter')
                ->query()
                ->fetchColumn();

        // Use Array Translation Adapter, Loop through all Availible Translations
        if ($translationAdapter == 'array') {
            // Find all Valid Language Arrays
            // Check For Array Files
            $languagePath = APPLICATION_PATH . '/application/languages';
            // Get List of Folders
            $languageFolders = array_filter(glob($languagePath . DIRECTORY_SEPARATOR . '*'), 'is_dir');
            // Look inside Folders for PHP array
            $locale_array = array();
            foreach ($languageFolders as $folder) {
                // Get Locale code
                $locale_code = str_replace($languagePath . DIRECTORY_SEPARATOR, "", $folder);
                $locale_array[] = $locale_code;
                if (count(glob($folder . DIRECTORY_SEPARATOR . $locale_code . 'php')) == 0) {
                    // If Array files do not exist, switch to CSV
                    $translationAdapter = 'csv';
                }
            }
            $language_count = count($locale_array);
            // Add the First One
            $translate = new Zend_Translate(
                    array(
                'adapter' => 'array',
                'content' => $languagePath . DIRECTORY_SEPARATOR . $locale_array[0] . DIRECTORY_SEPARATOR . $locale_array[0] . '.php',
                'locale' => $locale_array[0])
            );
            if ($language_count > 1) {
                for ($i = 1; $i < $language_count; $i++) {
                    $translate->addTranslation(
                            array(
                                'content' => $languagePath . DIRECTORY_SEPARATOR . $locale_array[$i] . DIRECTORY_SEPARATOR . $locale_array[$i] . '.php',
                                'locale' => $locale_array[$i])
                    );
                }
            }
        }
        // Use CSV Translation Adapter
        else {
            $translate = new Zend_Translate(
                    'Csv', APPLICATION_PATH . '/application/languages', null, $params
            );
        }
        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Form::setDefaultTranslator($translate);
        Zend_Controller_Router_Route::setDefaultTranslator($translate);
        return $translate;
    }

    protected function _loadLicense() {
        $modulePath = $this->_moduleBaePath . DIRECTORY_SEPARATOR;
        include $modulePath . 'Siteapi/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitereview/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitereview/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitehashtag/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitehashtag/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Sitereaction/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitereaction/controllers/license/license.php';

        if (is_file(APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license.php';
        if (is_file(APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Sitegateway/controllers/license/license.php';
        if (is_file(APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license.php'))
            include APPLICATION_PATH . '/application/modules/Siteeventticket/controllers/license/license.php';
    }

}
