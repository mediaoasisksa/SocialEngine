<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Form_Admin_ProfileMapsContact_Map extends Engine_Form {

    protected $_optionId;

    public function getOptionId() {
        return $this->_optionId;
    }

    public function setOptionId($optionId) {
        $this->_optionId = $optionId;
        return $this;
    }

    public function init() {

        $this->setMethod('post')
                ->setTitle("Select Mobile / Contact Number Profile Question")
                ->setAttrib('class', 'global_form_box')
                ->setDescription("After selecting a profile question please click on save.");
        $browseOptions = Engine_Api::_()->getApi('fields', 'siteapi')->getProfileTypes($this->_optionId);
        $value = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_contact_profile_" . $this->_optionId);
        if (count($browseOptions) > 0) {
            $this->addElement('Select', 'contactfield', array(
                'label' => 'Contact No.',
                'allowEmpty' => false,
                'required' => true,
                'multiOptions' => $browseOptions,
                'value' => $value
            ));
        } else if (count($browseOptions) == 1) {
            $this->addElement('Hidden', 'browse_view_type', array(
                'order' => 968,
                'value' => $browseOptions[0]
            ));
        }

        $this->addElement('Button', 'yes_button', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('yes_button', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}
