<?php
class Sitebooking_Form_ServiceProvider_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('GET')
      ;
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Providers',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
    ));

    $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll();
    $category_value["0"] = "All Categories";

    foreach ($table as $key => $value) {
      if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
      {
        $category_value[$value['category_id']]  = $value['category_name'];
      }
    }

    $this->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => $category_value,
        'onchange' => "this.form.submit();",
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

  }
}
?>