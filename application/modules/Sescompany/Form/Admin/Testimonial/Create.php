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

class Sescompany_Form_Admin_Testimonial_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Testimonial')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'owner_name', array(
      'label' => 'Client Name',
      'description' => 'Enter name of the client.',
      'allowEmpty' => true,
      'required' => false,
    ));
    
    $this->addElement('Text', 'designation', array(
        'label' => 'Designation',
        'description' => 'Enter designation of the client.',
        'allowEmpty' => true,
        'required' => false,
    ));
    
    $this->addElement('Textarea', 'description', array(
        'label' => 'Testimonial Description',
        'description' => 'Enter the description for the testimonial.',
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('File', 'file', array(
        'allowEmpty' => true,
        'required' => false,
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