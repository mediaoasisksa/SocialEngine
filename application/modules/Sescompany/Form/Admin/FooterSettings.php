<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: FooterSettings.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Form_Admin_FooterSettings extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Manage Footer Settings')
            ->setDescription('Here, you can configure the settings for the Footer.');
            
    $this->addElement('Text', 'sescompany_footabtheading', array(
      'label' => 'About Us Heading',
      'description' => 'Enter About Us Heading..',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.footabtheading', 'About Us'),
    ));
    
    $this->addElement('Textarea', 'sescompany_footeraboutusdes', array(
      'label' => 'About Us Description',
      'description' => 'Enter About Us Description..',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.footeraboutusdes', 'Lorem Ipsum Is Simply Dummy Text Of The Printing And Typesetting Industry.'),
    ));

    $this->addElement('Text', 'sescompany_resourceheading', array(
      'label' => 'Resources Heading',
      'description' => 'Enter resources heading..',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.resourceheading', 'RESOURCES'),
    ));
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/menus/index/name/sescompany_extra_menu';
    $this->addElement('Radio',
      'sescompany_quicklinksenable',
      array(
          'label' => 'Enable Extra Links',
          'description' => 'Do you want to enable extra links to your preferred links in the footer? If you choose Yes, the menu items will display which have been configured from <a href="' . $fileLink . '" target="_blank">Click Here</a>.',
          'multiOptions' => array('1'=>'Yes','0'=>'No'),
          'onchange' => 'enableExtralink(this.value);',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.quicklinksenable', '1'),
    ));
    $this->sescompany_quicklinksenable->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    $this->addElement('Text', 'sescompany_quicklinkheading', array(
      'label' => 'Quick Links Heading',
      'description' => 'Enter quick links heading..',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.quicklinkheading', 'Quick Links'),
    ));
    

    $fileLinkSocial = $view->baseUrl() . '/admin/menus/index/name/core_social_sites';
    $this->addElement('Select', 'sescompany_showsocialmedia', array(
      'label' => 'Show Social Media Icons',
      'description' => 'Do you want to show social media icons. If you choose Yes, the menu items will display which have been configured from <a href="' . $fileLinkSocial . '" target="_blank">Click Here</a>.',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'onchange' => 'social(this.value);',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showsocialmedia', 1),
    ));
    $this->sescompany_showsocialmedia->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Text', 'sescompany_socialmediaheading', array(
      'label' => 'Social Media Icons Heading',
      'description' => 'Enter social media icons heading..',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.socialmediaheading', 'Social'),
    ));
    
    $this->addElement('Select', 'sescompany_showlanguage', array(
      'label' => 'Show Language Drop Down',
      'description' => 'Do you want to show language drop down.',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showlanguage', 1),
    ));

    $this->addElement('Select', 'sescompany_showcopyrights', array(
      'label' => 'Show Copyright',
      'description' => 'Do you want to show copyright.',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showcopyrights', 1),
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
