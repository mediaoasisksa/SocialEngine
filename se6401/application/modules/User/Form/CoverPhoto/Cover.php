<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://www.socialengine.com/license/
 * @version    $Id: Cover.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_CoverPhoto_Cover extends Engine_Form {

  public function init() {
    $translate = Zend_Registry::get('Zend_Translate');
    $this
      ->setTitle($translate->translate('Upload Cover Photo'))
      ->setDescription('The minimum height is 400px. If your image is under this height, it will not be able to be repositioned.')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('id', 'cover_photo_form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('File', 'Filedata', array(
      'label' => $translate->translate('Choose a cover photo.'),
      'validators' => array(
        array('Extension', false, 'jpg,png,gif,jpeg,webp'),
      ),
      'onchange' => 'javascript:uploadPhoto();'
    ));
  }
}
