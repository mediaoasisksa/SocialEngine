<?php
class Sitecourse_Form_Admin_Settings_Format extends Engine_Form {

    public function init() {

        $this->setTitle('Certificate Formats')
        ->setName('sitecourse_certificate_format_settings');
        
        

        // Get available files
        $logoOptions = Engine_Api::_()->sitecourse()->getImages();

        $this->addElement('Select', 'sitecourse_companylogo', array(
            'label' => 'Company logo',
            'description' => 'Select the company logo Image from the dropdown below, which you want with the PDF file of your certificate. This image will get printed on your certificates.
            [Note 1: Images shown below are coming from files uploaded on ‘Layout >> Files & Media Manager’ (available in the admin panel of your site). So you need to upload your Certificate Image here to make it available in the dropdown.]
            [Note 2: Recommended size of the image: Width X Height : 150 px X 100 px]
            ',
            'multiOptions' => $logoOptions,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.companylogo')
        ));
        $this->sitecourse_companylogo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        
        
        $this->addElement('Select', 'sitecourse_cbackground', array(
            'label' => 'Background Image',
            'description' => 'Select the background Image for your certificate.',
            'multiOptions' => $logoOptions,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecourse.cbackground')
        ));
        

        

        //FORMAT EDITOR
//         $this->addElement('dummy', 'dummy_text', array('description' => 'Available Placeholders:
// [Free], [name], [event_date_time],[event_location], [event_venue], [buyer_ticket_id], [ticket_time], [ticket_title], [ticket_price], [user_name], [QR_code_image]'));

        $editorOptions = array(
            'html' => (bool) true,
            'mode' => "exact",
            'forced_root_block' => false,
            'force_p_newlines' => false,
            'elements' => 'sitecourse_format_bodyhtml',
            'plugins' => array(
                'table', 'fullscreen', 'preview', 'paste',
                'code', 'image', 'textcolor', 'link'
            ),
            'toolbar1' => array(
                'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
                'image', 'link', 'fullscreen',
                'preview'
            ));


        $this->addElement('TinyMce', 'sitecourse_format_bodyhtml', array(
            'label' => 'Format Body',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please customize your Certificate Format by using below editor.',
            'editorOptions' => $editorOptions,
        ));

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Save Settings',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'default', array(
            'type' => 'submit',
            'label' => 'Reset to Default',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $buttonsArray = array('submit', 'default');

        $this->addDisplayGroup($buttonsArray, 'buttons');
        $this->getDisplayGroup('buttons');
    }

}
