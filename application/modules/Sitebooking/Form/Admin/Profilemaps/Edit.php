<?php

class Sitebooking_Form_Admin_Profilemaps_Edit extends Engine_Form {

  public function init() {

    $this->setMethod('post')
        ->setTitle("Edit Profile Type")
        ->setAttrib('class', 'global_form_box')
        ->setDescription("After selecting a profile type, please click on 'Save'.");

    //GET CURRENT PROFILE TYPE
    $profile_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('profile_type', null);

    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');
    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();

      if (count($options) > 0) {
        $options = $profileTypeField->getElementParams('sitebooking_ser');
        unset($options['options']['order']);
        unset($options['options']['multiOptions']['0']);

        //REMOVE CURRENT PROFILE TYPE
        unset($options['options']['multiOptions'][$profile_type]);

        $this->addElement('Select', 'profile_type', array_merge($options['options'], array(
          'required' => true,
          'allowEmpty' => false,
        )));
      } else if (count($options) == 1) {
        $this->addElement('Hidden', 'profile_type', array(
          'order' => 936,
          'value' => $options[0]->option_id
        ));
      }
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