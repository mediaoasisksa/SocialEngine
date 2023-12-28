<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Icon.php 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sescompany_Form_Admin_Icon extends Engine_Form {

  public function init() {

    $this->setTitle('Configure Menu Icon');
    $this->setDescription('Here, you can choose the icon for the Main Menu item.');
    
    $this->addElement('Select', "icon_type", array(
        'label' => 'Choose the icon type',
        'allowEmpty' => false,
        'required' => true,
        'multiOptions' => array(
            '0' => 'Image Icon',
            '1' => "Font Icon",
        ),
        'onclick' => 'showIcon(this.value)',
        'value' => 0,
    ));
    
    $this->addElement('Text', "font_icon", array(
        'label' => 'Icon / Icon Class (Ex: fa-home)',
        //'allowEmpty' => false,
        //'required' => true,
    ));
    

    $this->addElement('File', 'photo', array(
        'label' => '',
       // 'allowEmpty' => false,
       // 'required' => true,
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg,JPG,PNG,GIF,JPEG');

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }

}
