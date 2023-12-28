<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Featch.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_ConfigureKeys extends Engine_Form
{
    protected $_installedModules;  

    public function getInstalledModules() 
    {
      return $this->_installedModules;
    }
    public function setInstalledModules($installedModules) 
    {
      $this->_installedModules = $installedModules;
      return $this;
    }

    public function init()
    {  
        $this->setTitle('Configuration of API Keys');
        $this->setDescription('Please configure and save below details to enable various 3rd party services on your website.');
        $this->setAttrib('id', 'seao-keys');

        $this->addElement('Dummy', 'yahoo_settings_temp', array(
            'label' => '',
            'decorators' => array(array('ViewScript', array(
                'viewScript' => '_formsocialkeys.tpl',
                'class' => 'form element',
                'installedModules' => $this->_installedModules
            )))
        )); 

        // Element: submit
        $this->addElement('Button', 'submit', array(
          'label' => 'Save',
          'type' => 'submit',
          'ignore' => true
        ));
    } 
} 