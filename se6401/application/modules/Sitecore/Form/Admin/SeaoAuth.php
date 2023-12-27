<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    https://socialapps.tech/license/
 * @version    $Id: Featch.php 2010-11-18 9:40:21Z SocialApps.tech $
 * @author     SocialApps.tech
 */
class Sitecore_Form_Admin_SeaoAuth extends Engine_Form
{
  public function init()
  { 
    $this->setTitle('Sign In: SAT Account');
    $this->setDescription('Please sign-in into your SAT Client Account to proceed further.');
    $this->setAttrib('data-seao', 'form')
      ->setAttrib('id', 'seao-featch');
    $this->addElement('Text', 'email', array(
      'label' => 'SEAO Account Email',
       'autocomplete' => 'new-email',
    ));
    $this->addElement('Password', 'password', array(
      'label' => 'SEAO Account Password',
      'autocomplete' => 'new-password',
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Login SAT Account',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}

?>