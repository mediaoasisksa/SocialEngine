<?php

class Sescompany_Form_Admin_Widget_Banner extends Core_Form_Admin_Widget_Standard
{

  public function init()
  {
    parent::init();

    // Set form attributes
    $this
      ->setTitle('Display Banner')
      ->setDescription('Please choose an banner.');
    $table = Engine_Api::_()->getDbtable('banners', 'core');
    $banners = $table->fetchAll($table->getBannersSelect());

    $this->removeElement('title');

    if( count($banners) > 0 ) {
      $this->addElement('Select', 'banner_id', array(
        'label' => 'Banner',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
          array('NotEmpty', true),
        )
      ));

      $this->banner_id->addMultiOption(0, '');
      foreach( $banners as $banner ) {
        $this->banner_id->addMultiOption($banner->getIdentity(), $banner->getTitle());
      }

      $this->addElement('Text', 'height', array(
        'label' => 'Enter the height of this Banner (in pixels).',
        'allowEmpty' => false,
        'required' => true,
        'value' => '300',
      ));
      
      $this->addElement('Select', 'fullwidth', array(
        'label' => 'Do you want to show banner in full width?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => 1,
      ));
    }
  }
}
