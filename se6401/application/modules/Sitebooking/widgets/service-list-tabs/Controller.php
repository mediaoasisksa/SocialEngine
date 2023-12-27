<?php

class Sitebooking_Widget_ServiceListTabsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

    $this->view->flag = 0;
    $params["approved"] = "1";
    $params["status"] = "1";
    // LIMIT
    $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',5);
    $this->view->limitParam = $params['limit'] = $this->_getParam('limit', $limit);

    $listParams = array();

    $listParams = $this->_getParam('list_id');
    if(!empty($listParams)) {
      for($i = 0; $i < count($listParams); $i++) {
        if($listParams[$i] == "likeCount")
          $listParams[$i] = "like_count";
        if($listParams[$i] == "commentCount")
          $listParams[$i] = "comment_count";
        if($listParams[$i] == "reviewCount")
          $listParams[$i] = "review_count";
        if($listParams[$i] == "creationDate")
          $listParams[$i] = "creation_date";
      }
    }
    if(!empty($listParams))
      $this->view->listParams = json_encode($listParams);
    else
      $this->view->flag  = 1;

    // VIEW 
    $viewParams = array();
    $param = $this->_getParam('view_id');
    if(empty($param))
      $viewParams[0] = "grid";
    else
      $viewParams = $param;

    $this->view->viewParams = json_encode($viewParams);

    // CATEGORY
    $params['category_id'] = $this->_getParam('category_id');

    // FILTER TYPE
    $params['filter_type'] = $this->_getParam('filter_type');
    if(empty($params['filter_type'])) {
      if(!empty($listParams[0]))
        $params['filter_type'] = $listParams[0];
    }

    $serviceTable = Engine_Api::_()->getDbtable('sers', 'sitebooking');

    $select = $serviceTable->serviceListTabs($params);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    if( $this->_getParam('page') )
    {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    
    $this->view->items_per_page = $items_per_page = $params['limit'];

    $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);
  }

}