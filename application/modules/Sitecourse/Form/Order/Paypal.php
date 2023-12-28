<?php

class Sitecourse_Form_Order_PayPal extends Engine_Form {

    public function init() {
        parent::init();

        $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->translate('PayPal Account for receiving payments from %s'), $siteTitle));   

        
        $this->setName('sitecourse_payment_info');

        $description = $this->getTranslator()->translate('SITECOURSE_FORM_PAYMENT_PAYPAL_DESCRIPTION');
        // $description = vsprintf($description, array(
        //     'https://www.paypal.com/signup/account',
        //     'https://www.paypal.com/webapps/customerprofile/summary.view',
        //     'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-api-access',
        //     'https://developer.paypal.com/docs/classic/api/apiCredentials/',
        //     'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
        //     'https://www.paypal.com/cgi-bin/customerprofileweb?cmd=_profile-ipn-notify',
        //     (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        //         'module' => 'sitecourse',
        //         'controller' => 'order',
        //         'action' => 'paymentInfo'
        //     ), 'default', true),
        // ));
        // $description = sprintf(Zend_Registry::get('Zend_Translate')->translate('Below, you can configure your Paypal Account to receive payments from %s. This information should be accurately provided and enabled.'), $siteTitle) . ' <br/> ' . $description . '<div id="show_paypal_form_massges"></div>';
        $this->setDescription($description);

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        $this->addElement('Text', 'email', array(
            'label' => 'Paypal Email',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true)
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
        // Elements
        $this->addElement('Text', 'username', array(
            'label' => 'API Username',
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'password', array(
            'label' => 'API Password',
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        $this->addElement('Text', 'signature', array(
            'label' => 'API Signature',
            'filters' => array(
                'StripTags',
                new Zend_Filter_StringTrim(),
            ),
        ));

        // remove if no such setting is given at the admin page.
        // $showExtraInfo = true;
        // $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
        // if (empty($isPaymentToSiteEnable)) {
        //     $showExtraInfo = false;
        // }

        // if (!empty($showExtraInfo)) {

        // Element: test mode
        $this->addElement('Radio', 'test_mode', array(
            'description' => 'Enable if testing details given.',
            'label' => 'Test Mode?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => '0',
        ));

        // Element: execute
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                'viewScript' => '_formSetDivAddress.tpl',
                'class' => 'form element')))
        ));

        $this->addDisplayGroup(array('submit'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
        // }
    }

}
?>
