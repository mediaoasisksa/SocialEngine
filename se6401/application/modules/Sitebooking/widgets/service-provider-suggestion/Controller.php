<?php

class Sitebooking_Widget_ServiceProviderSuggestionController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }

    if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');
    $category_id = $service->category_id;
    $parent_id = $service->parent_id;

    $tableProvider = Engine_Api::_()->getDbtable('pros', 'sitebooking');
    $providerTableName = $tableProvider->info('name');

    $tableService= Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $serviceTableName = $tableService->info('name');

    //MAKE QUERY
    $select = $tableProvider->select()
      ->setIntegrityCheck(false)
      ->from($serviceTableName, array('category_id', 'parent_id as par'))
      ->join($providerTableName, "$providerTableName.pro_id = $serviceTableName.parent_id");

    $sql = $serviceTableName.".category_id = ".$category_id." OR ".$serviceTableName.".first_level_category_id = ".$category_id." OR ".$serviceTableName.".second_level_category_id = ".$category_id;
    $select->where($sql);
    $select->where($serviceTableName.'.parent_id != ?',$parent_id );
    $select->group($providerTableName. '.pro_id');
    $select->where($providerTableName.'.approved = ?',1 );
    $select->where($providerTableName.'.status = ?',1 );
    $select->where($providerTableName.'.enabled = ?',1 );    

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    if( $this->_getParam('page') ) {
        $paginator->setCurrentPageNumber($this->_getParam('page'));
      }

      $this->view->limit = $limit = $this->_getParam('limit',5);
      $this->view->paginator = $paginator = $paginator->setItemCountPerPage($limit);


    if(count($paginator) <= 0) {
      return $this->setNoRender();
    }  
  }
}


?>