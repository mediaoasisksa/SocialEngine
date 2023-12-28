<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Messages.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_Widget_Messages extends Engine_Form {
    public function init() {
        parent::init();

        // Set form attributes
        $this
                ->setTitle('Configure Tip Messages')
//                ->setDescription('Configured tip messages')
        ;

        $defaultFormValue = '<h3>Connect and Share</h3><br />Download <b>[SITE_TITLE]</b> App Now.';
        $localeMultiOptions = Engine_Api::_()->siteapi()->getLanguageArray();
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
        $total_allowed_languages = COUNT($localeMultiOptions);
        if (!empty($localeMultiOptions)) {
            $defaultValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_tip_messages", null);
            if (!empty($defaultValue))
                $defaultValue = @unserialize($defaultValue);

            foreach ($localeMultiOptions as $key => $label) {
                $lang_name = $label;
                if (isset($localeMultiOptions[$label]))
                    $lang_name = $localeMultiOptions[$label];

                $page_block_field = "siteapi_tip_message_$key";

                if (isset($defaultValue) && isset($defaultValue[$page_block_field]))
                    $formElementValue = $defaultValue[$page_block_field];
                else
                    $formElementValue = $defaultFormValue;

                $this->addElement('TinyMce', $page_block_field, array(
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_("Tip Message for [%s]"), $lang_name),
                    'description' => 'Available Placeholders: [SITE_TITLE]',
                    'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:120px;'),
                    'value' => $formElementValue,
                    'filters' => array(
                        new Engine_Filter_Html(),
                        new Engine_Filter_Censor()),
                    'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
                ));
            }
        }


        $this->addElement('Dummy', 'siteapi_spread_world', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_("<enter><h3>Spread the World Page Content</h3></center>")),
        ));
        $this->siteapi_spread_world->getDecorator('Label')->setOptions(array('escape' => false));

        foreach ($localeMultiOptions as $key => $label) {
            $lang_name = $label;
            if (isset($localeMultiOptions[$label]))
                $lang_name = $localeMultiOptions[$label];

            $page_block_field = "siteapi_spread_title_$key";

            if (isset($defaultValue) && isset($defaultValue[$page_block_field]))
                $formElementValue = $defaultValue[$page_block_field];
            else
                $formElementValue = $defaultFormValue;

            $this->addElement('Text', $page_block_field, array(
                'label' => sprintf(Zend_Registry::get('Zend_Translate')->_("Spread the World Title [%s]"), $lang_name),
            ));
        }

        // Get available files
        $logoOptions = Engine_Api::_()->siteapi()->getImages(array('' => 'Text-only (No logo)'));

        $this->addElement('Select', 'siteapi_spread_image', array(
            'label' => 'Spread the World Image',
            'multiOptions' => $logoOptions,
        ));
        
        $this->addElement('Select', 'siteapi_spread_background', array(
            'label' => 'Spread the World Background Image',
            'multiOptions' => $logoOptions,
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }

}
