<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: NotificationSettings.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Form_Member_NotificationSettings extends Engine_Form {

  public function init() {
  
    $this->setTitle('Allow Notifications?')
      ->setDescription('Do you want to receive notifications for the activities of this group?')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    
    $this->addElement('Radio', 'notification', array(
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');
  }
}
