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

class Sescompany_Form_Admin_Client_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Client')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'client_name', array(
      'label' => 'Client Name',
      'description' => 'Enter client name.',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Text', 'client_link', array(
      'label' => 'Client Link',
      'description' => 'Enter client link.',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $client_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('client_id', 0);
    if (!$client_id) {
      $required = false;
      $allowEmpty = true;
    } else {
      $required = false;
      $allowEmpty = true;
    }

    $this->addElement('File', 'file', array(
      'allowEmpty' => $allowEmpty,
      'required' => $required,
      'label' => 'Client Photo',
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