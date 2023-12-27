<?php
class Sitebooking_Form_Admin_Settings_Duration extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setTitle('Enter Duration')
      ->setAttrib('name', 'service_duration')
		->setAttrib('class','global_form_popup');
    
    // Durations
    $durations = array(
      '5400' => '1.5 Hours',
      '7200' => '2 Hours',
      '9000' => '2.5 Hours',
      '10800' => '3 Hours',
      '12600' => '3.5 Hours',
      '14400' => '4 Hours',
      '16200' => '4.5 Hours',
      '18000' => '5 Hours',
      '19800' => '5.5 Hours',
      '21600' => '6 Hours',
      '23400' => '6.5 Hours',
      '25200' => '7 Hours',
      '27000' => '7.5 Hours',
      '28800' => '8 Hours'
    );
    $duraionTable = Engine_Api::_()->getItemTable('sitebooking_duration');
    $durationItems = $duraionTable->fetchAll();
    foreach ($durationItems as $key => $value) {
      unset($durations[$value['duration']]);
    }
    $this->addElement('Select', 'duration', array(
      'label' => 'Duration',
      'multiOptions' => $durations,
      'required' => true,
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Duration',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));


  }
}