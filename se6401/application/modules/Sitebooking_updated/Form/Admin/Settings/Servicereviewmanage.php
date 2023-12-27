<?php

class Sitebooking_Form_Admin_Settings_Servicereviewmanage extends Engine_Form
{

  public function init() {

    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
      ));

    $this->addElement('Text', 'serviceTitle', array(
      'label' => 'Service Title',
    ));

    $ratings = array();
    $ratings['0'] = 'Select';
    $ratings['1'] = '1 Star Rating';
    $ratings['2'] = '2 Star Rating';
    $ratings['3'] = '3 Star Rating';
    $ratings['4'] = '4 Star Rating';
    $ratings['5'] = '5 Star Rating';

    $this->addElement('Select', 'rating', array(
      'label' => 'Rating',
      'multiOptions' => $ratings,
    ));

    $this->addElement('Button', 'search', array(
      'type' => 'submit',
      'label' => 'Search',
      'ignore' => true,
      'order' => 10000001,
    ));

  }
}
