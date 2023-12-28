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

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }

}
