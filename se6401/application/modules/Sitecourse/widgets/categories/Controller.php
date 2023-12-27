<?php

class Sitecourse_Widget_CategoriesController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $showAll = $this->_getParam('showAll',1);
    $categories = array();
    if($showAll){
      $category_info = Engine_Api::_()->getDbTable('categories', 'sitecourse')->getCategoriesAssoc();
    }
    else{
      $category_info = Engine_Api::_()->getDbTable('categories', 'sitecourse')->getCategoriesContainsCourse();
    }
    // no category found
    if(!count($category_info)){
      return $this->setNoRender();
    }
    foreach ($category_info as $key => $value) {  
      //get sub category array
      $sub_cat_array = Engine_Api::_()->getDbtable('categories', 'sitecourse')->getSubCategoresAssoc($value['category_id']);
      // create associated category array
      $category_array = array('category_id' => $value['category_id'],
        'category_name' => $value['category_name'],
        'sub_categories' => $sub_cat_array);
      $categories[] = $category_array;
    }
    $this->view->categories = $categories;
    $this->view->subcategorys = 0;
    $this->view->category = 0;
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);

    if ($request->getParam('category')) {

      $categoryidtemp = $request->getParam('category');
      if ($request->getParam('subcategory')) {
        $subcategoryidtemp = $request->getParam('subcategory');
      } else {
        $subcategoryidtemp = $request->getParam('subcategory_id');
      }
      if (!empty($categoryidtemp)) {
        $this->view->category = $categoryidtemp;
        $this->view->subcategorys = $subcategoryidtemp;
      }
    } elseif ($request->getParam('category_id')) {
      $categoryid = $request->getParam('category_id');
      $subcategoryid = $request->getParam('subcategory_id');
      if (!empty($categoryid)) {
        $_GET['category_id'] = $this->view->category = $categoryid;
        $_GET['categoryname'] = $categoryname;
      }

      if (!empty($subcategoryid)) {
        $_GET['subcategory_id'] = $this->view->subcategorys = $subcategoryid;
        $_GET['subcategoryname'] = $subcategoryname;
      }

      if (!empty($_GET)) {
        if (!empty($_GET['subcategory_id'])) {
          $this->view->subcategorys = $_GET['subcategory_id'];
        }
        if (!empty($_GET['category_id'])) {
          $this->view->category = $_GET['category_id'];
        }
      }
    }

    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = $this->view->category = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
    }
  }

}

?>
