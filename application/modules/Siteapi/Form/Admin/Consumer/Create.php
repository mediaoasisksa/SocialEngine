<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Create.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_Consumer_Create extends Engine_Form {

    public function init() {

        $this->setTitle('Create API Consumer')
                ->setDescription("Create API consumers to generate OAuth tokens. OAuth token is a key to access your API and it is generated dynamically according to the client device. API Consumer Key and API Consumer Secret Key are confidential information because using these, clients can generate OAuth tokens to access API.");

        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'description' => 'If you are working on multiple platforms like iOS and Android, then you can differentiate their ‘Consumer Details’ on the basis of "Title". [Note: This field will only be used for the personal purpose of identification, it has no involvement in API calling procedure.]',
            'required' => true,
            'style' => 'width: 18em;',
        ));

        $this->addElement('Text', 'key', array(
            'label' => 'API Consumer Key',
            'style' => 'width: 18em; color: #777;',
            'required' => true,
            'allowEmpty' => false,
//            'disabled' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array(4, 164)),
//                array('Regex', true, array('/^[a-z][a-z0-9]*$/i'))
            ),
            'value' => Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString()
        ));

        $this->addElement('Text', 'secret', array(
            'label' => 'API Consumer Secret Key',
            'style' => 'width: 18em; color: #777;',
            'required' => true,
//            'disabled' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', true, array(4, 164)),
//            array('Regex', true, array('/^[a-z][a-z0-9]*$/i'))
            ),
            'value' => Engine_Api::_()->getApi('oauth', 'siteapi')->generateRandomString()
        ));

//        $this->addElement('Radio', 'expire', array(
//            'label' => "Expire Access Token",
//            'description' => "Do you want to expire the created Access Token? Access Token will be genrate dynamically. Whenever allowed client type will call to getAccessToken API [NOTE: If you select delete then created access token will be delete after selected time interval and client will not be able to get response from API. For this client need to call getAccessToken API again to genrate new access token]",
//            'multiOptions' => array(0 => "No", 1 => "Yes"),
//            'onclick' => 'expireAccessToken()',
//            'value' => 0,
//                )
//        );
//
//        $this->addElement('Select', 'expire_limit', array(
//            'label' => 'Time Interval to Delete Access Token',
//            'description' => "Select the time intervals to delete access token. A newly created access token will be delete after creation to reach selected time limit. In this case devise which are using that access token need to call getAccessToken API again to get new access token.",
//            'multiOptions' => array(
//                608400 => '1 Week',
//                1216800 => '2 Weeks',
//                1825200 => '3 Weeks',
//                2520000 => '1 Month',
//                7560000 => '3 Months',
//                15120000 => '6 Months',
//                30240000 => '1 Year',
//            ),
//            'value' => 15120000
//        ));
//

        $this->addElement('Checkbox', 'status', array(
            'label' => 'Enable this API client',
            'value' => 1,
        ));


        $this->addElement('Button', 'submit', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage')),
            'decorators' => array('ViewHelper'),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            )
        ));
    }

}
