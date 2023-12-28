<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminSettingsController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */

        if (!empty($method) && $method == 'Siteapi_Form_Admin_Settings') {
            
        }
        return true;
    }

    public function indexAction() {
        $this->view->backTo = $this->getParam('backTo', null);
        if (isset($_POST['siteapi_header_disable'])) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('siteapi.header.disable', $_POST['siteapi_header_disable']);
        }
        if (isset($_POST['video_youtube_apikey'])) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('video.youtube.apikey', $_POST['video_youtube_apikey']);
        }
        
        $errors = Engine_Api::_()->getApi('Core', 'siteapi')->hasExtraCode();
        if((isset($errors['shouldHaveFile']) && !empty($errors['shouldHaveFile'])))
            $this->view->displayPermissionError = $errors['shouldHaveFile'];
        
        if((isset($errors['isextraCodeAvailable']) && !empty($errors['isextraCodeAvailable'])))
            $this->view->isextraCodeAvailable = $errors['isextraCodeAvailable'];

        if (isset($_POST['advancedactivity_max_allowed_days'])) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedactivity_max_allowed_days', $_POST['advancedactivity_max_allowed_days']);
        }

        include APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license1.php';
    }

    public function faqAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_faq');
    }

    public function readmeAction() {
        
    }

    public function helpCreateApiAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_settings');
    }

    public function documentsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_documentation');
    }

    public function editRootFileAction() {
        
    }
    
    public function editBootFileAction() {
        
    }
    
    public function removeExtraCodeAction() {
        
    }

    public function tipMessagesAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_tip_messages');

        $this->view->form = $form = new Siteapi_Form_Admin_Widget_Messages();
        $formValues = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_tip_messages");
        $values = @unserialize($formValues);
        if (!empty($values))
            $form->populate($values);
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            unset($values['submit']);
            $value = @serialize($values);
            Engine_Api::_()->getApi('settings', 'core')->setSetting("siteapi_tip_messages", $value);
            $form->addNotice('Successfully Saved');
        }
    }

    public function siteapiFeedSettingsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_feed_settings');
        $this->view->form = $form = new Siteapi_Form_Admin_ActivityFeedSettings();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $this->getRequest()->getPost();
        unset($values['submit']);

        if (isset($values['siteapi_all_update_show'])) {
            $settings->setSetting('siteapi.all.update.show', $values['siteapi_all_update_show']);
        }

        if (isset($values['siteapi_greeting_announcement'])) {
            $settings->setSetting('siteapi.greeting.announcement', $values['siteapi_greeting_announcement']);
        }

        $form->addNotice('Your changes have been saved.');
    }

    public function profileSettingsAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_profile_settings');
        $this->view->form = $form = new Siteapi_Form_Admin_ProfileSettings();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
         
        foreach ($values as $key => $value) {  
            $getSettings = $settings->getSetting($key);
            if(!empty($getSettings)){
                $settings->removeSetting($key);
            }
            $settings->setSetting($key, $value);        
        }
        $this->view->form = $form = new Siteapi_Form_Admin_ProfileSettings();
        $form->renderForm();
        $form->addNotice('Your changes have been saved.');
    }

}
