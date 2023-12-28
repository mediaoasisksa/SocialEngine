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

        $cacheNameArray = $this->_cacheName();
        if (empty($viewer_id) && $cacheNameArray['module'] == 'advancedactivity' && $cacheNameArray['controller'] == 'feed' && $cacheNameArray['action'] == 'index')
            $cacheName = 'siteapi_advancedactivity_feed_index_homefeed';
        else
            $cacheName = implode("_", $cacheNameArray);

        $cache = Zend_Registry::get('Zend_Cache');
        $cacheName = str_replace('-', '_', $cacheName);

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
            $cacheName = implode("_", $this->_cacheName());
            $cacheName = !empty($tempCacheName) ? $cacheName . '_' . $tempCacheName : $cacheName;
            $cacheName = str_replace('-', '_', $cacheName);
            $cache = Zend_Registry::get('Zend_Cache');
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
        $isCacheEnabled = $this->isCacheEnabled();
        if (empty($isCacheEnabled))
            return;

        if (empty($cacheName)) {
            $cacheName = $this->_cacheName();
            $cacheName = $cacheName['default'] . '_' . $cacheName['module'];
        }

        $cache = Zend_Registry::get('Zend_Cache');
        $getIds = $cache->getIds();
        foreach ($getIds as $key) {
            if (strstr($key, $cacheName))
                $cache->remove($key);

            // Delete activity cache
            if (strstr($key, 'siteapi_activity'))
                $cache->remove($key);

            // Delete activity cache
            if (strstr($key, 'siteapi_advancedactivity'))
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
        // If cache disabled by SocialEngine
        if (APPLICATION_ENV != 'production')
            return;

        // If API cache disabled by site administrator
        $isCacheEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.caching.status', 1);
        if (empty($isCacheEnabled))
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

        // Only file based caching allowed.
        $getDefaultCache = $this->_readDefaultCache();
        if (!isset($getDefaultCache['backend']['File']))
            return;

        $Data = $_REQUEST;
        $keys = array_keys($Data);

        $matched = preg_grep('/_id$/', $keys);
        if ($matched) {
            return;
        }

        return true;
    }

    /**
     * Getting the available cache life time
     * 
     * @return integer
     */
    private function _getCacheLifeTime() {
        $lifetimeStatus = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.lifetime.status', 1);
        if (!empty($lifetimeStatus)) {
            $chacheLifetime = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.caching.lifetime', 300);
        } else {
            $getDefaultCache = $this->_readDefaultCache();
            if (isset($getDefaultCache['frontend']['core']['lifetime']) && !empty($getDefaultCache['frontend']['core']['lifetime']))
                $chacheLifetime = $getDefaultCache['frontend']['core']['lifetime'];
        }

        $chacheLifetime = !empty($chacheLifetime) ? $chacheLifetime : 300;
        return $chacheLifetime;
    }

    /**
     * Get the API cache name
     * 
     * @return array
     */
    private function _cacheName() {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $params['default'] = 'siteapi';
        $params['module'] = $request->getModuleName();
        $params['controller'] = $request->getControllerName();
        $params['action'] = $request->getActionName();

        return $params;
    }

    /**
     * Read default cache file
     * 
     * @return array
     */
    private function _readDefaultCache() {
        $setting_file = APPLICATION_PATH . '/application/settings/cache.php';
        $default_file_path = APPLICATION_PATH . '/temporary/cache';

        if (file_exists($setting_file)) {
            $current_cache = include $setting_file;
        } else {
            $current_cache = array(
                'default_backend' => 'File',
                'frontend' => array(
                    'core' => array(
                        'automatic_serialization' => true,
                        'cache_id_prefix' => 'Engine4_',
                        'lifetime' => '300',
                        'caching' => true,
                        'gzip' => 1,
                    ),
                ),
                'backend' => array(
                    'File' => array(
                        'cache_dir' => APPLICATION_PATH . '/temporary/cache',
                    ),
                ),
            );
        }
        $current_cache['default_file_path'] = $default_file_path;
        return $current_cache;
    }

}

?>