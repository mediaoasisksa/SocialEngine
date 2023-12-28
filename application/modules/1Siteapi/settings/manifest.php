<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manifest.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteapi',
        'version' => '4.9.4p6',
        'path' => 'application/modules/Siteapi',
        'title' => 'SocialEngine REST API Plugin',
        'description' => 'SocialEngine REST API Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' =>
        array(
            'path' => 'application/modules/Siteapi/settings/install.php',
            'class' => 'Siteapi_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            'application/modules/Siteapi',                        
            'public/siteapi-guidelines',
            
            'application/modules/Activity/Api/Siteapi',
            'application/modules/Activity/controllers/siteapi',
            
            'application/modules/Album/Api/Siteapi',
            'application/modules/Album/controllers/siteapi',
            
            'application/modules/Blog/Api/Siteapi',
            'application/modules/Blog/controllers/siteapi',
            
            'application/modules/Classified/Api/Siteapi',
            'application/modules/Classified/controllers/siteapi',
            
            'application/modules/Core/Api/Siteapi',
            'application/modules/Core/controllers/siteapi',
            
            'application/modules/Event/Api/Siteapi',
            'application/modules/Event/controllers/siteapi',
            
            'application/modules/Forum/Api/Siteapi',
            'application/modules/Forum/controllers/siteapi',
            
            'application/modules/Group/Api/Siteapi',
            'application/modules/Group/controllers/siteapi',
            
            'application/modules/Messages/controllers/siteapi',
            
            'application/modules/Music/Api/Siteapi',
            'application/modules/Music/controllers/siteapi',
            
            'application/modules/Poll/Api/Siteapi',
            'application/modules/Poll/controllers/siteapi',
            
            'application/modules/User/Api/Siteapi',
            'application/modules/User/controllers/siteapi',           
            
            'application/modules/Video/Api/Siteapi',
            'application/modules/Video/controllers/siteapi',
            
            'public/api-documentation'
        ),
        'files' =>
        array(
            'application/languages/en/siteapi.csv',
            'application/siteapi.php'            
        ),
    ),
    // COMPATIBLE WITH MOBILE / TABLET PLUGIN
    'sitemobile_compatible' => true,
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Siteapi_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Siteapi_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Siteapi_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutMobileSMDefault',
            'resource' => 'Siteapi_Plugin_Core',
        )
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'siteapi_consumers',
        'siteapi_menus',
        'siteapi_tokens'
    ),
);
?>