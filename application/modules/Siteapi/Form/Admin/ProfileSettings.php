<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ThirdPartyServices.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_ProfileSettings extends Engine_Form {

	public function init() {
        $this
                ->setTitle('Profile Page Settings');
               
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Radio', 'siteapi_profile_friends_block', array(
            'label' => 'Display Friends Block in iOS & Android App',
            'description' => 'Do you want to display friends block on Member Profile Page?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('siteapi.profile.friends.block',1),
        ));
        $this->getElement('siteapi_profile_friends_block')->getDecorator('Description')->setEscape(false);
         
         $this->addElement('Radio', 'siteapi_profile_photos_block', array(
            'label' => 'Display Photos Block in iOS & Android App',
            'description' => "Do you want to display photos block on Member Profile Page?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('siteapi.profile.photos.block',1),
        ));
         
        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }
}