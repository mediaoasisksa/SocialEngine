<?php

class Sitebooking_Form_Service_Search extends Sitebooking_Form_Searchfields
{

  protected $_fieldType = 'sitebooking_ser';

  public function init()
  {

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET');

    parent::init();

    $this->getMemberTypeElement();

    $this->getAdditionalOptionsElement();

  }


  public function getMemberTypeElement() {

    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
        return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

    foreach ($options as $option) {
        $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('hidden', 'profile_type', array(
        'order' => -1000001,
        'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_' . $profileTypeField->field_id . ' ',
        'onchange' => 'changeFields($(this));',
        'multiOptions' => $multiOptions,
    ));

    return $this->profile_type;
  }

  public function getAdditionalOptionsElement() {

    $this->addElement('Text', 'search', array(
      'label' => 'Search Services',
    ));

    $this->addElement('Text', 'provider', array(
      'label' => 'Provider Name',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Select', 'status', array(
      'label' => 'Show',
      'multiOptions' => array(
        '' => 'All Entries',
        '1' => 'Only Published Entries',
        '0' => 'Only Drafts',
      ),
      'onchange' => 'this.form.submit();',
    ));

    // $category_value = array();
    $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll();
    $category_value["-1"] = null;

    foreach ($table as $key => $value) {
        if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
        {
          $category_value[$value['category_id']]  = $value['category_name'];
        }
    }

    $this->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => $category_value,
        'onchange' => "showFields(this.value, 1);",
    ));

    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'id'=>'location',
      'placeholder'=> 'Enter Location',
      'allowEmpty' => false,
    ));

    $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
    if($unit === 'miles'){
      $this->addElement('Select', 'locationDistance', array(
        'label' => 'Within',
        'multiOptions' => array(
          '0' => '',
          '1' => '1 Mile',
          '2' => '2 Mile',
          '5' => '5 Mile',
          '10' => '10 Mile',
          '20' => '20 Mile',
          '50' => '50 Mile',
          '100' => '100 Mile',
          '200' => '200 Mile',
          '500' => '500 Mile',
          '750' => '750 Mile',
          '1000' => '1000 Mile',
        ),
        'value' => '50',
      ));
    }else if($unit === 'kilometers'){
      $this->addElement('Select', 'locationDistance', array(
        'label' => 'Within',
        'multiOptions' => array(
          '0' => '',
          '1' => '1 Kilometer',
          '2' => '2 Kilometers',
          '5' => '5 Kilometers',
          '10' => '10 Kilometers',
          '20' => '20 Kilometers',
          '50' => '50 Kilometers',
          '100' => '100 Kilometers',
          '200' => '200 Kilometers',
          '500' => '500 Kilometers',
          '750' => '750 Kilometers',
          '1000' => '1000 Kilometers',
          ),
        'value' => '50',
      ));
    }

    $this->addElement('Text', 'city', array(
      'label' => 'City',
      'placeholder'=> '',
      'allowEmpty' => false,
    ));

    $this->addElement('Text', 'country', array(
      'label' => 'Country',
      'placeholder'=> '',
      'allowEmpty' => false,
    ));

    $this->addElement('Hidden', 'detectlocation', array(
      'id' => 'locationParams',
      'order' => 999,
    ));

    $this->addElement('Button', 'find', array(
      'type' => 'submit',
      'label' => 'Search',
      'ignore' => true,
      'order' => 10000001,
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 104
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => 103
    ));
  }

}
