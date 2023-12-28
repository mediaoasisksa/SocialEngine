<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Create.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Form_Admin_Counter_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Statistics')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'counter_name', array(
      'label' => 'Statistics Name',
      'description' => 'Enter statistics name.',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Text', 'counter_value', array(
      'label' => 'Statistics Value',
      'description' => 'Enter statistics value.',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $counter_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('counter_id', 0);
    if (!$counter_id) {
      $required = false;
      $allowEmpty = true;
    } else {
      $required = false;
      $allowEmpty = true;
    }

    $this->addElement('File', 'file', array(
      'allowEmpty' => $allowEmpty,
      'required' => $required,
      'label' => 'Photo',
      'description' => 'Upload a photo [Note: photos with extension: “jpg, png and jpeg] only.]',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'link' => true,
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}