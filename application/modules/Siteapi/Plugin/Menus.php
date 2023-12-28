<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Menus.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Plugin_Menus {

    public function apiDocumentation($row) {
        $params = $row->params;

//        $file = APPLICATION_PATH . "/public/apidocumentation.html";
//        if (file_exists($file)) {
        return array(
            'route' => 'admin_default',
            'params' => array(
                'module' => 'siteapi',
                'controller' => 'settings',
                'action' => 'documents'
            ),
        );
//        }
    }
}
