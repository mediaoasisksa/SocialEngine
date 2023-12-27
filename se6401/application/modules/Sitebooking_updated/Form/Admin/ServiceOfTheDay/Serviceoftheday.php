<?php

class Sitebooking_Form_Admin_ServiceOfTheDay_Serviceoftheday extends Engine_Form {

  protected $_field;

  public function init() {

  $this->setMethod('post');
  $this->setTitle('Add a Service of the Day')
      ->setDescription('Select a start date and end date below and the corresponding service from the auto-suggest Service field. The selected service will be displayed as "Service of the Day" for this duration and if more than one services are found to be displayed in the same duration then they will be displayed randomly one at a time.');

  $label = new Zend_Form_Element_Text('title');
  $label->setLabel('Service')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text')
      ->setAttrib('style', 'width:300px;');


  // init to
  $this->addElement('Hidden', 'resource_id', array( 'order' => 900,));

  $this->addElements(array(
    $label,
  ));

  $starttime = new Seaocore_Form_Element_DatepickerCalendarDateTime('starttime');
  $starttime->setLabel("Start Date");
  $starttime->setAllowEmpty(false);
  $starttime->setValue(date('Y-m-d H:i:s'));
  $this->addElement($starttime);


  //Start End date work
  $endtime = new Seaocore_Form_Element_DatepickerCalendarDateTime('endtime');
  $endtime->setLabel("End Date");
  $endtime->setAllowEmpty(false);
  $endtime->setValue(date('Y-m-d H:i:s'));
  $this->addElement($endtime);
  //End End date work
  // Buttons
  $this->addElement('Button', 'submit', array(
    'label' => 'Add Service',
    'type' => 'submit',
    'ignore' => true,
    'decorators' => array('ViewHelper')
  ));

  // Element: cancel
  $this->addElement('Cancel', 'cancel', array(
    'label' => 'cancel',
    'link' => true,
    'prependText' => ' or ',
    //'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'siteservice_general', true),
    'onclick' => 'javascript:parent.Smoothbox.close()',
    'decorators' => array(
      'ViewHelper',
    ),
  ));

  $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  $button_group = $this->getDisplayGroup('buttons');
  }

}

?>