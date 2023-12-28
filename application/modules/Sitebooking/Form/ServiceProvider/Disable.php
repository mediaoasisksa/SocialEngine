<?php 
class Sitebooking_Form_ServiceProvider_Disable extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Disable Service Provider')
      ->setDescription('Are you sure you want to disable Service Provider along with all its services?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Disable',
      'type' => 'submit',
      'ignore' => true,
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
?>