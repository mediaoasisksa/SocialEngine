<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Delete.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Group_Form_Photo_Delete extends Engine_Form {

  public function init() {
  
    $descrption = 'Are you sure you want to delete this photo?';
    $photo_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('photo_id', 0);
    if ($photo_id) {
      $photo = Engine_Api::_()->getItem('group_photo', $photo_id);
      $group = Engine_Api::_()->getItem('group', $photo->group_id);
      if($group->coverphoto == $photo_id) {
        $descrption = 'Are you sure you want to delete this photo? This photo is also set as your cover photo & deleting it will also remove from the cover photo.';
      }
    }
    
    $this->setTitle('Delete Group Photo')
      ->setDescription($descrption);

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Delete Photo',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}
