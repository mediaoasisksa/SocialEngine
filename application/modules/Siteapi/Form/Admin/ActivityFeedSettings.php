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
class Siteapi_Form_Admin_ActivityFeedSettings extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Activity Feed Settings');
               
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

//        $this->addElement('Dummy', 'linkedin_settings_temp', array(
//            'label' => '',
//            'decorators' => array(array('ViewScript', array(
//                        'viewScript' => '_formcontactimport.tpl',
//                        'class' => 'form element'
//                    )))
//        ));

        $this->addElement('Radio', 'siteapi_all_update_show', array(
            'label' => 'Display Filter on main page in iOS and Android apps',
            'description' => 'Do you want to display Filter tab (All updates, Friends etc) on Members Home Page. This filter allows users to filter the feeds on main page of the Android <a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteapi/externals/images/AFilters.png" target="_blank"><img src="application/modules/Siteapi/externals/images/eye.png" /></a> and iOS App <a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteapi/externals/images/IFilters.PNG" target="_blank"><img src="application/modules/Siteapi/externals/images/eye.png" /></a> ?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('siteapi.all.update.show',1),
        ));
        $this->getElement('siteapi_all_update_show')->getDecorator('Description')->setEscape(false);
         
        $this->addElement('Radio', 'siteapi_greeting_announcement', array(
            'label' => 'Allow Greetings / Announcements on Home Page in iOS & Android apps',
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to display Greetings / Announcements on the Members Home Page. All the greetings created from %1$sAdvanced Activity Plugin%2$s will be displayed in the app, if enabled?'), "<a href='" . Zend_Registry::get('Zend_View')->url(array('module' => 'advancedactivity', 'controller' => 'greeting', 'action' =>'index'), 'admin_default', true)."' target='_blank'>", '</a>'),
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('siteapi.greeting.announcement',1),
        ));
        $this->siteapi_greeting_announcement->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

       

        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>


