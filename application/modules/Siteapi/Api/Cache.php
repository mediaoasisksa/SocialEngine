<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Cache.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Cache extends Core_Api_Abstract {

    /**
     * Get the API caching
     * 
     * @return string|null
     */
    public function getCache() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $isCacheEnabled = $this->isCacheEnabled();
        if (empty($isCacheEnabled))
            return false;

        $cacheName = $this->_cacheName();
        if (empty($cacheName))
            return null;
        $cache = Zend_Registry::get('Zend_Cache');

        try {
            $getCache = $cache->load($cacheName);
        } catch (Exception $e) {
            return null;
        }
        if (!empty($getCache))
            return $getCache;

        return null;
    }

    /**
     * Set the API caching
     * 
     * @return null
     */
    public function setCache($content, $tempCacheName = null) {
        $isCacheEnabled = $this->isCacheEnabled();
        if (empty($isCacheEnabled))
            return;
        if (!empty($content)) {
            $cacheName = $this->_cacheName();
            if (empty($cacheName))
                return;
            $currentCache = $this->_readDefaultCache();
            $backendOptions = $currentCache['backend']['File'];
            $frontendOptions = $currentCache['frontend'];
            $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
            $cache->setLifetime($this->_getCacheLifeTime());
            $cache->save($content, $cacheName);
        }

        return;
    }

    /*
     * Delete the API caching
     * 
     * @return null
     */

    public function deleteCache($cacheName = false) {
        $currentCache = $this->_readDefaultCache();
        $backendOptions = $currentCache['backend']['File'];
        $frontendOptions = $currentCache['frontend'];
        $cache = Zend_Cache::factory('Page', 'File', $frontendOptions, $backendOptions);
        $cache->remove($cacheName);
        $getIds = $cache->getIds();

        foreach ($getIds as $key) {
            if (strstr($key, 'suggestion_listing'))
                continue;

            if (strstr($key, $cacheName))
                $cache->remove($key);

            // Delete activity cache
            if (strstr($key, 'siteapi_activity'))
                $cache->remove($key);

            // Delete activity cache
            if (strstr($key, 'advancedactivity'))
                $cache->remove($key);
        }

        return;
    }

    /**
     * Check is API cache enabled or not
     * 
     * @return true|null
     */
    public function isCacheEnabled() {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        // If cache disabled by SocialEngine
        if (APPLICATION_ENV != 'production')
            return;

        // In case, if don't want cache
        if (isset($_REQUEST['disableCache']) && !empty($_REQUEST['disableCache']))
            return;

        // In case of pagination, cache will not work
        if (isset($_REQUEST['page']) && !empty($_REQUEST['page']) && ($_REQUEST['page'] > 1))
            return;

        // In case of advanced activity feed pagination, cache will not work
        if (isset($_REQUEST['maxid']) && !empty($_REQUEST['maxid']))
            return;

        if (isset($_REQUEST['action_id']))
            return;
        //request method is put or post
        if ($request->isPost() || $request->isPut()) {
            return;
        }

        // Only file based caching allowed.
        $getDefaultCache = $this->_readDefaultCache();
        if (!isset($getDefaultCache['backend']['File']))
            return;
        
        $backendOptions = $getDefaultCache['backend']['File']['cache_dir'];
        if (!is_dir($backendOptions)) {
            return;
        }

//        $Data = $_REQUEST;
//        $keys = array_keys($Data);
//
//        $matched = preg_grep('/_id$/', $keys);
//        if ($matched) {
//            return;
//        }

        return true;
    }

    /**
     * Getting the available cache life time
     * 
     * @return integer
     */
    private function _getCacheLifeTime() {

        $getDefaultCache = $this->_readDefaultCache();
        if (isset($getDefaultCache['frontend']['lifetime']) && !empty($getDefaultCache['frontend']['lifetime']))
            $chacheLifetime = $getDefaultCache['frontend']['lifetime'];
        $chacheLifetime = !empty($chacheLifetime) ? $chacheLifetime : 300;
        return $chacheLifetime;
    }

    /**
     * Get the API cache name
     * 
     * @return array
     */
    private function _cacheName() {
        $file = APPLICATION_PATH . '/application/settings/restapi_caching_url.php';
        if (file_exists($file)) {
            $enableCacheUrl = include $file;
        } else {
            return '';
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $params = array();
        $params['module'] = $request->getModuleName();
        $params['controller'] = $request->getControllerName();
        $params['action'] = $request->getActionName();
        $parameters = $request->getParams();
        $language = $parameters['language'];
        $cacheName = implode("_", $params);
        $key_val = '';
        $cacheName = str_replace('-', '_', $cacheName);
        if (!isset($enableCacheUrl[$cacheName]) || empty($enableCacheUrl[$cacheName]))
            return;
        $getRequestUri = htmlspecialchars($_SERVER['REQUEST_URI']);
        $urlarray = explode("?", $getRequestUri);
        $trimData = trim($urlarray[0], "/");
        $cachName = str_replace("/", "_", $trimData);
        $viewerId = 0;
        $keys = array_keys($parameters);
        $matched = preg_grep('/_id|_type$/', $keys);
        $matched = array_flip($matched);
        $matched = array_intersect_key($parameters, $matched);
        $key_val = implode('_', $matched);

        if ($enableCacheUrl[$cacheName] == 'member_level') {
            $viewerId = $viewer && $viewer->getIdentity() ? $viewer->level_id : 0;
        } elseif ($enableCacheUrl[$cacheName] == 'user_level') {
            $viewerId = $viewer && $viewer->getIdentity() ? $viewer->getIdentity() : 0;
        }

        $cachName = $cachName . '_' . $language . '_' . $key_val . '_' . $viewerId;
        $cachName = str_replace('-', '_', $cachName);
        $cachName = str_replace('.', '_', $cachName);
        return $cachName;
    }

    /**
     * Read default cache file
     * 
     * @return array
     */
    private function _readDefaultCache() {
        $settingFile = APPLICATION_PATH . '/application/settings/restapi_cache.php';
        $defaultFilePath = APPLICATION_PATH . '/temporary/restapicache';

        if (file_exists($settingFile)) {
            $currentCache = include $settingFile;
        } else {
            $currentCache = array(
                'default_backend' => 'File',
                'frontend' => array(
                    'automatic_serialization' => true,
                    'cache_id_prefix' => 'Engine4_restapi',
                    'lifetime' => '300',
                    'caching' => true,
                    'status' => true,
                    'gzip' => true,
                ),
                'backend' => array(
                    'File' => array(
                        'file_locking' => true,
                        'cache_dir' => APPLICATION_PATH . '/temporary/restapicache',
                    ),
                ),
            );
        }
        $currentCache['default_file_path'] = $defaultFilePath;

        return $currentCache;
    }

}

?>