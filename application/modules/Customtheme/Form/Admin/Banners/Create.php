<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class CustomTheme_Form_Admin_Banners_Create extends Engine_Form
{
  
  public function init()
  {
    // Set form attributes
    $this->setTitle('Create New Banner');
    $this->setDescription('Below you can create a new banner for your website. (Note: The recommended size for the image is: 400px x 300px.)');
    $this->setAttrib('id', 'form-upload');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    // Init file
    $this->addElement('File', 'photo', array(
      'label' => 'Banner Image'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $this->addElement('Text', 'uri', array(
      'label' => 'URL',
      'description' => 'Enter the URL of the page where you want to redirect users after they click on this banner.',
    ));
    
    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Banner',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));


    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'customtheme', 'controller' => 'banners', 'action' => 'index'), 'admin_default', true),
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }

}
