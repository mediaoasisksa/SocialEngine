<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Loader.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Controller_Loader extends Engine_Loader {

    /**
     * Get current singleton instance
     * 
     * @return Engine_Loader
     */
    public static function getInstance() {
        return new self();
    }

    /**
     * Loads and instantiates a resource class
     * 
     * @param string $class
     * @return mixed
     */
    public function setComponentsObject($class, $orignalClassName = null) {
        if (empty($orignalClassName))
            $orignalClassName = $class;

        $loader = Engine_Loader::getInstance();
        return $loader->_components[$orignalClassName] = $loader->load($class);
    }

}
