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

class Sescompany_Form_Admin_Team_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Team')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'name', array(
      'label' => 'Name',
      'description' => 'Enter name.',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Text', 'designation', array(
      'label' => 'Designation',
      'description' => 'Enter designation.',
    ));
    
    $this->addElement('Text', 'quote', array(
      'label' => 'Quote',
      'description' => 'Enter quote.',
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'description' => 'Enter the description.',
        'allowEmpty' => true,
        'required' => false,
    ));
    
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'description' => 'Enter phone.',
      'validators' => array(
        array('Int', true),
      ),
    ));
    
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'description' => 'Enter email.',
    ));
  
    $this->addElement('Text', 'address', array(
      'label' => 'Address',
      'description' => 'Enter address.',
    ));
    
    $this->addElement('Text', 'facebook', array(
      'label' => 'Facebook URL',
      'description' => 'Enter facebook url.',
    ));
    $this->addElement('Text', 'twitter', array(
      'label' => 'Twitter URL',
      'description' => 'Enter twitter url.',
    ));
    $this->addElement('Text', 'linkdin', array(
      'label' => 'Linkd In URL',
      'description' => 'Enter linkdin url.',
    ));
    $this->addElement('Text', 'googleplus', array(
      'label' => 'Google Plus URL',
      'description' => 'Enter google plus URL.',
    ));

    $team_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('team_id', 0);
    if (!$team_id) {
      $required = true;
      $allowEmpty = false;
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