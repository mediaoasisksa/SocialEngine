<?php

class Sitebooking_Widget_ServiceSuggestionController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }

    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');
    $ser_id = $service->getIdentity();

    $table = Engine_Api::_()->getDbtable('sers', 'sitebooking');
    $select = $table->select();

    $category_id = $service->category_id;
    $limit = $this->_getParam('limit', 5);

    $sql = "category_id = ".$category_id." OR first_level_category_id = ".$category_id." OR second_level_category_id = ".$category_id;
    $select->where($sql);
    $select->where('ser_id != ?',$ser_id);
    $select->where('approved = 1');
    $select->where('enabled = 1');
    $select->where('status = 1');
    $select->limit($limit);

    $this->view->suggestedServices = $table->fetchAll($select);

    //DONT RENDER IF COUNT ZERO
    if (count($this->view->suggestedServices) <= 0) {
      return $this->setNoRender();
    }
  }
}


?>