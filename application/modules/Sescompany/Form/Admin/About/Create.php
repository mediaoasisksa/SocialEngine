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

class Sescompany_Form_Admin_About_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Introduction')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'about_name', array(
      'label' => 'Title',
      'description' => 'Enter title.',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'description' => 'Enter the description.',
        'allowEmpty' => true,
        'required' => false,
    ));
    
    $this->addElement('Text', 'font_icon', array(
      'label' => 'Font Icon',
      'description' => 'Enter font icon (ex: fa-like).',
      'allowEmpty' => true,
      'required' => false,
    ));
    
    $about_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('about_id', 0);
    if (!$about_id) {
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
    
    $this->addElement('Text', 'readmore_button_name', array(
      'label' => 'Read More Button Text',
      'description' => 'Enter read more button text.',
      'allowEmpty' => true,
      'required' => false,
    ));
    
    $this->addElement('Text', 'readmore_button_link', array(
      'label' => 'Read More Button Link',
      'description' => 'Enter read more button link.',
      'allowEmpty' => true,
      'required' => false,
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