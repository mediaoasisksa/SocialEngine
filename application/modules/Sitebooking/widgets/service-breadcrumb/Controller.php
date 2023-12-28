<?php

class Sitebooking_Widget_ServiceBreadcrumbController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // HAS SUBJECT
    if( !Engine_Api::_()->core()->hasSubject('sitebooking_ser') ) 
      return $this->setNoRender();

    // GET SUBJECT
    $this->view->sitebooking = $sitebooking = Engine_Api::_()->core()->getSubject();

    $resource_type = $sitebooking->getType();

    $resource_id = $sitebooking->getIdentity();

    $this->view->item = $item = Engine_Api::_()->getItem($resource_type, $resource_id);

    // SHOW CATEGORY
    if($item->category_id != 0) {
      $category = Engine_Api::_()->getItem('sitebooking_category',$item->category_id);
      $this->view->category_name = $category["category_name"];
    } 
      
    if($item->first_level_category_id != 0) {
      $category = Engine_Api::_()->getItem('sitebooking_category',$item->first_level_category_id);
      $this->view->first_level_category_name = $category["category_name"];
    } 
      
    if($item->second_level_category_id != 0) {
      $category = Engine_Api::_()->getItem('sitebooking_category',$item->second_level_category_id);
      $this->view->second_level_category_name = $category["category_name"];
    }
  }
}