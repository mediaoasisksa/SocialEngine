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

class Sescompany_Form_Admin_Feature_Create extends Engine_Form {

  public function init() {
  
    $this->setTitle('Create New Feature')
          ->setDescription("Enter below details.")
          ->setMethod('post');
    
    $this->addElement('Text', 'feature_name', array(
      'label' => 'Feature Name',
      'description' => 'Enter feature name.',
      'allowEmpty' => false,
      'required' => true,
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Feature Description',
        'description' => 'Enter the description for the feature.',
        'allowEmpty' => false,
        'required' => true,
    ));
    
    $feature_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('feature_id', 0);
    if (!$feature_id) {
      $required = true;
      $allowEmpty = false;
    } else {
      $required = false;
      $allowEmpty = true;
    }

    $this->addElement('File', 'file', array(
      'allowEmpty' => $allowEmpty,
      'required' => $required,
      'label' => 'Feature Photo',
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