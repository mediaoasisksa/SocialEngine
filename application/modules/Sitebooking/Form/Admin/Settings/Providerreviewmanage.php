<?php

class Sitebooking_Form_Admin_Settings_Providerreviewmanage extends Engine_Form
{

  public function init() {

    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ));

    $this->addElement('Text', 'providerTitle', array(
      'label' => 'Provider Title',
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
