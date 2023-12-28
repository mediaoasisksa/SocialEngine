<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Settings.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_Settings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "environment_mode",
        "submit_lsetting"
    );

    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');


        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'siteapi_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialApps.tech from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.lsettings'),
        ));

        $errors = Engine_Api::_()->getApi('Core', 'siteapi')->hasExtraCode();
        if (!Engine_Api::_()->getApi('Core', 'siteapi')->isRootFileValid() && !isset($errors['shouldHaveFile'])) {
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $url = $view->url(array('action' => 'edit-root-file'), 'admin_default', false);
            $this->addElement('Radio', 'siteapi_valid_root_file', array(
                'label' => 'Modify Root File',
                'description' => 'By this, the root file(index.php) changes to our siteapi file automatically. It thus start API calling for your website.<br>Note 1: It might happen that above process fails as only file owner can do the file changes. So in this case, please <a href="' . $url . '" class="smoothbox">click here</a> to do the changes manually.<br />Note 2: If you do not prefer this method then <a href="' . $url . '" class="smoothbox">click here</a> to do it manually.
',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0
            ));
            $this->siteapi_valid_root_file->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
        }

        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));


        $this->addElement('Radio', 'siteapi_ssl_verification', array(
            'label' => 'API Communication on SSL',
            'description' => 'Do you want to allow API requests and responses only on SSL (https://)? [Note: Before enabling this setting, please ensure that your website supports SSL. We strongly recommend all API requests to be sent on SSL for security reasons. Plain HTTP API requests are unsecure. If you need help in enabling HTTPS for your website, then you may purchase our "<a href="http://www.socialapps.tech/services/ssl-certification-installation" target="_blank">SSL Certificate Installation Service</a>"]',
            'multiOptions' => array(
                1 => 'Yes, allow API communication only on SSL.',
                0 => 'No, allow plain HTTP API requests too.'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.ssl.verification', 0),
        ));
        $this->siteapi_ssl_verification->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $this->addElement('Radio', 'siteapi_header_disable', array(
            'label' => 'Webview Content Header & Footer',
            'description' => 'Hide Header & Footer content of mobile website in Webview of iOS & Android Apps.',
            'multiOptions' => array(
                0 => 'Yes',
                1 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteapi.header.disable', 0),
        ));
        $this->siteapi_header_disable->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $description = 'While posting videos on your site, users can choose YouTube as a source. This requires a valid YouTube API key.<br>To learn how to create that key with correct permissions, read our <a href="https://www.slickremix.com/docs/get-api-key-for-youtube/" target="_blank">KB Article</a> or follow this <a href="https://www.youtube.com/watch?v=pP4zvduVAqo"> Video</a>';

        $currentYouTubeApiKey = '******';
        if (!_ENGINE_ADMIN_NEUTER) {
            $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.youtube.apikey', $coreSettings->getSetting('video.youtube.apikey'));
        }
        $sitevideo = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        $video = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        if (!empty($sitevideo) || !empty($video)) {
            $this->addElement('Text', 'sitevideo_youtube_apikey', array(
                'label' => 'YouTube API Key',
                'description' => $description,
                'filters' => array(
                    'StringTrim',
                ),
                'value' => $currentYouTubeApiKey,
            ));
            $this->sitevideo_youtube_apikey->getDecorator('Description')->setOption('escape', false);
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'order' => 500,
        ));
    }

}
