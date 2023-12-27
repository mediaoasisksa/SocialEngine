<?php  
class Sitecourse_Form_Admin_SubCategory extends Sitecourse_Form_Admin_Category{

	protected $_field;

	public function init(){
      parent::init();	
		$this->label->setLabel('Add Sub-Category');
	}

 public function setField($category)
  {
    $this->_field = $category;

    // Set up elements
    $this->label->setValue($category->category_name);
    $this->id->setValue($category->category_id);
    $this->submit->setLabel('Edit Category');

    
  }

}
