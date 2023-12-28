<?php

class Sitecourse_Form_Search extends Engine_Form
{
  protected $_category_id;

  public function setCategory($id = null){
    $this->_category_id = $id;
  }
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
    
    $this->addElement('Text', 'title', array(
      'label' => 'Search Courses',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        '0' => '',
        '1' => 'Most Recent',
        '2' => 'Most Rated',
        '3' => 'Only Favourites',
      ),
      'onchange' => 'this.form.submit();',
    ));


  // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getCategoriesAssoc();
    $categoriesOptions[0]='';
    foreach($categories as $value){
      $categoriesOptions[$value['category_id']]=$value['category_name'];
    }
    if (count($categories) > 0) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Course Category',
        'multiOptions' => $categoriesOptions,
        'onchange' => 'changeSubCategory()',
      ));
    } 


    //prepare subcategories
    $parentCategoryId = null;
    foreach($categories as $value) {
      $parentCategoryId = $value['category_id'];
      break;
    }

    if($this->_category_id)
      $parentCategoryId = $this->_category_id;
    if($parentCategoryId)
      $subCategories = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getSubCategoresAssoc($parentCategoryId);


    if (count($categories) > 0) {
      $subCategoriesOptions = array(0=>'');
      foreach($subCategories as $value){
        $subCategoriesOptions[$value['category_id']]=$value['category_name'];
      }
      $this->addElement('Select', 'subcategory_id', array(
        'label' => 'Course Sub-Category',
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'multiOptions' => $subCategoriesOptions,
      ));
    }

    $this->addElement('Select', 'difficulty_level', array(
      'label' => 'Difficulty level',
      'multiOptions' => array(
        '3' => '',
        '0' => 'Beginner',
        '1' => 'Intermediate',
        '2' => 'Expert'
      ),
      'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Text', 'minprice', array(
      'label' => 'Minimum Price',
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0),
      ),
    ));

   
    $this->addElement('Text', 'maxprice', array(
      'label' => 'Maximum Price',
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(1),
      ),
    ));


   

    $this->addElement('Hidden', 'tag', array(
      'order' => 101
    ));



    $this->addElement('Button', 'find', array(
      'id' => 'extra-done',
      'type' => 'submit',
      'label' => 'Search',
      'ignore' => true,
      'order' => 10000001,
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => 103
    ));

  }
}

