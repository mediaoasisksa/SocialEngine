<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    content.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Mobile App Browser Tip Message Widget',
        'description' => 'This widget displays a tip message "Connect and Share with the people and download link for Mobile App", to the users who are viewing mobile app\'s website in browser. This widget needs to be a placed in the footer section.',
        'category' => 'REST API',
        'type' => 'widget',
        'name' => 'siteapi.mobile-tip',
        'adminForm' => 'Siteapi_Form_Admin_Widget_Logo',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
);
?>
