<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: HeaderSettings.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Form_Admin_HeaderSettings extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Manage Header Settings')
            ->setDescription('Here, you can configure the settings for the Header, Main and Mini navigation menus of your website. Below, you can choose from various header designs having vertical and horizontal placement of Main Navigation menu.');

    $this->addElement('Select', 'sescompany_header_fixed', array(
        'label' => 'Fix Main Menu for Header',
        'description' => 'Do you want to fix the Main menu for header to the top of the page when users scroll down? If you choose "Yes", then users will need to scroll back to the top to view the main menu.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sescompany.header.fixed', 1),
    ));
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $banner_options[] = '';
    $path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($path as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $base_name = basename($file->getFilename());
      if (!($pos = strrpos($base_name, '.')))
        continue;
      $extension = strtolower(ltrim(substr($base_name, $pos), '.'));
      if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
        continue;
      $banner_options['public/admin/' . $base_name] = $base_name;
    }
    $fileLink = $view->baseUrl() . '/admin/files/';
    $this->addElement('Select', 'sescompany_logo', array(
        'label' => 'Choose Logo',
        'description' => 'Choose from below the logo image for your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>. Leave the field blank if you do not want to show logo.]',
        'multiOptions' => $banner_options,
        'escape' => false,
        'value' => $settings->getSetting('sescompany.logo', ''),
    ));
    $this->sescompany_logo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Text', "company_menu_logo_top_space", array(
        'label' => 'Logo Top Margin',
        'description' => 'Enter the top margin for the logo of your website to be displayed in this header.(Ex: 30px)',
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->sescompany()->getContantValueXML('company_menu_logo_top_space'),
    ));

    $this->addElement('Text', 'sescompany_limit', array(
        'label' => 'Main Menu Item Count',
        'description' => 'Enter the number of menu items which will be shown in the Main Navigation Menu on your website.',
        'value' => $settings->getSetting('sescompany.limit', 4),
    ));

    $this->addElement('Text', 'sescompany_moretext', array(
        'label' => 'Text For "More" Button in Menu',
        'description' => 'Enter the text for the "More" button in the Main Navigation Menu. This text will come if there are more menus after the number of menu items selected in the "Main Menu Item Count" setting above.',
        'value' => $settings->getSetting('sescompany.moretext', 'More'),
    ));
    $footerLink = $view->baseUrl() . '/admin/menus?name=sescompany_extra_links_menu';
    $this->addElement('MultiCheckbox', 'sescompany_header_loggedin_options', array(
        'label' => 'Header Options for Logged In Members',
        'description' => 'Choose from below the header options that you want to be shown to Logged-in members on your website.',
        'multiOptions' => array(
            'search' => 'Search',
            'miniMenu' => 'Mini Menu',
            'mainMenu' =>'Main Menu',
            'logo' =>'Logo',
            //'socialshare' => 'Extra Links in Mini Menu (<a href="'.$footerLink.'">Click here</a> to edit links)',
        ),
        'escape' => false,
        'value' => $settings->getSetting('sescompany.header.loggedin.options',array('search','miniMenu','mainMenu','logo', 'socialshare')),
    ));
    
    
    $this->addElement('MultiCheckbox', 'sescompany_header_nonloggedin_options', array(
        'label' => 'Header Options for Non-Logged In users',
        'description' => 'Choose from below the header options that you want to be shown to Non-Logged In users on your website.',
        'multiOptions' => array(
            'search' => 'Global Search (AJAX based)',
            'miniMenu' => 'Mini Navigation Menu',
            'mainMenu' =>'Main Navigation Menu',
            'logo' =>'Site Logo',
           // 'socialshare' => 'Extra Links in Mini Menu (<a href="'.$footerLink.'">Click here</a> to edit links)',
        ),
        'escape' => false,
        'value' => $settings->getSetting('sescompany.header.nonloggedin.options', array('search','miniMenu','mainMenu','logo', 'socialshare')),
    ));
    
    $this->addElement('Select', 'sescompany_enable_footer', array(
        'label' => 'Include Footer in Vertical Menu?',
        'description' => 'Do you want to include the Footer in the vertical bar? If you choose Yes, then the SocialEngine Footer menu will come in this widget and will show when the vertical menu is expanded. <a href="">Click here</a> to see screenshot of how the Footer will look.',
        'multiOptions' => array(
          '1' => 'Yes',
          '0' => 'No',
        ),
        'escape' => false,
        'value' => $settings->getSetting('sescompany_enable_footer', 1),
    ));
    $this->sescompany_enable_footer->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Select', 'sescompany_searchleftoption', array(
        'label' => 'Allow Searching in Modules',
        'description' => 'Do you want to allow users to search on the basis on various modules on your website via AJAX in the Global Search? [If you choose "Yes", then you can manage various modules from the "Manage Modules for Search" section of this plugin. Note: This setting will only work if you have enabled "Global Search" in Header.]',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'onclick' => 'showLimitOption(this.value);',
        'value' => $settings->getSetting('sescompany.searchleftoption', 1),
    ));

    $this->addElement('Text', 'sescompany_search_limit', array(
        'label' => 'Modules Count',
        'description' => 'Enter the number of modules to be shown in the Global Search on your website.',
        'value' => $settings->getSetting('sescompany.search.limit', '8'),
    ));
    
    
    $this->addElement('Dummy', 'sescompany_heextralinks', array(
      'label' => 'Extra Links',
    ));
    
    $this->addElement('Select', 'sescompany_heshowextralinks', array(
      'label' => 'Show Extra Link',
      'description' => 'Do you want to show extra link in header?',
      'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',
      ),
      'onclick' => 'showextralinks(this.value);',
      'value' => $settings->getSetting('sescompany.heshowextralinks', 1),
    ));
    
    $this->addElement('Text', 'sescompany_heshowextraphoneicon', array(
      'label' => 'Phone Number Icon Class',
      'description' => 'Enter the Icon Class (Ex: fa-phone).',
      'value' => $settings->getSetting('sescompany.heshowextraphoneicon', 'fa-phone'),
    ));
    $this->addElement('Text', 'sescompany_heshowextraphonenumber', array(
      'label' => 'Phone Number',
      'description' => 'Enter the phone number.',
      'value' => $settings->getSetting('sescompany.heshowextraphonenumber', '123456789'),
    ));
    
    $this->addElement('Text', 'sescompany_heshowextraemailicon', array(
      'label' => 'Email Icon Class',
      'description' => 'Enter the Icon Class (Ex: fa-envelope-o).',
      'value' => $settings->getSetting('sescompany.heshowextraemailicon', 'fa-envelope-o'),
    ));
    $this->addElement('Text', 'sescompany_heshowextraemailnumber', array(
      'label' => 'Email',
      'description' => 'Enter the email.',
      'value' => $settings->getSetting('sescompany.heshowextraemailnumber', 'info@business.com'),
    ));
    
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
