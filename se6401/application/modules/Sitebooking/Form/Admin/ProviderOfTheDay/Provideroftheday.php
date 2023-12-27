<?php

class Sitebooking_Form_Admin_ProviderOfTheDay_Provideroftheday extends Engine_Form {

  protected $_field;

  public function init() {

  $this->setMethod('post');
  $this->setTitle('Add a Provider of the Day')
      ->setDescription('Select a start date and end date below and the corresponding Provider from the auto-suggest Provider field. The selected Provider will be displayed as "Provider of the Day" for this duration and if more than one Providers are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');

  $label = new Zend_Form_Element_Text('title');
  $label->setLabel('Provider')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text')
      ->setAttrib('style', 'width:300px;');


  // init to
  $this->addElement('Hidden', 'resource_id', array( 'order' => 900,));

  $this->addElements(array(
    $label,
  ));

  $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
  $starttime->setLabel("Start Date");
  $starttime->setAllowEmpty(false);
  $starttime->setValue(date('Y-m-d H:i:s'));
  $this->addElement($starttime);


  //Start End date work
  $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
  $endtime->setLabel("End Date");
  $endtime->setAllowEmpty(false);
  $endtime->setValue(date('Y-m-d H:i:s'));
  $this->addElement($endtime);
  //End End date work
  // Buttons
  $this->addElement('Button', 'submit', array(
    'label' => 'Add Provider',
    'type' => 'submit',
    'ignore' => true,
    'decorators' => array('ViewHelper')
  ));

  // Element: cancel
  $this->addElement('Cancel', 'cancel', array(
    'label' => 'cancel',
    'link' => true,
    'prependText' => ' or ',
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