<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ManageContents.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sescompany_Form_Admin_ManageContents extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Manage Contents Settings')
            ->setDescription('These settings affect on your community landing page.');
            
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
/*    
    $moduleEnable = Engine_Api::_()->sescompany()->getModulesEnable();

    $this->addElement('Select', "sescompany_contmodule", array(
      'label' => 'Choose the Module to be shown in this widget.',
      'description' => 'Choose the Module to be shown in this widget.',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => $moduleEnable,
      'value' => $settings->getSetting('sescompany.contmodule', ''),
    ));

    $this->addElement('Text', 'sescompany_contheading', array(
      'label' => 'Heading',
      'description' => 'Enter Heading.',
      'value' => $settings->getSetting('sescompany.contheading', 'Blog'),
    ));

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
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $fileLink = $view->baseUrl() . '/admin/files/';
    $this->addElement('Select', 'sescompany_conbgimage', array(
        'label' => 'Background Image',
        'description' => 'Choose from below the background image for your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>.]',
        'multiOptions' => $banner_options,
        'escape' => false,
        'value' => $settings->getSetting('sescompany.conbgimage', ''),
    ));
    $this->sescompany_conbgimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    $this->addElement('Select', "sescompany_contpopularitycriteria", array(
      'label' => 'Choose the popularity criteria in this widget.',
      'description' => 'Choose the popularity criteria in this widget.',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(
        'creation_date' => 'Recently Created',
        'view_count' => 'View Count',
        'like_count' => 'Most Liked',
        'comment_count' => 'Most Commented',
        'modified_date' => 'Recently Modified'
      ),
      'value' => $settings->getSetting('sescompany.contpopularitycriteria', 'creation_date'),
    ));
    
    $this->addElement('Text', 'sescompany_contlimit', array(
      'label' => 'Limit',
      'description' => 'Enter limit.',
      'value' => $settings->getSetting('sescompany.contlimit', 4),
    ));*/


    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}