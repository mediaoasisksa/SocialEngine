<?php

class Customtheme_Widget_MemberBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    // Prepare data
    if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

 
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select();
    $select->where('level_id =?', 7);
    $select->order("user_id DESC");
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
     
    $items_per_page = 100;
    

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);

  }
  
}