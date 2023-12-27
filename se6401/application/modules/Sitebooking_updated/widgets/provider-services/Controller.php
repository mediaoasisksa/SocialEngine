<?php class Sitebooking_Widget_ProviderServicesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
      return $this->setNoRender();
    }

    $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');

  	$values['parent_id'] = $provider->getIdentity();

  	if ($this->_getParam('isAjax')) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
      $this->getElement()->removeDecorator('');
      $this->view->isAjax = true;
    }

  	$service = Engine_Api::_()->getItemTable('sitebooking_ser');
    $sql = $service->select()->where('parent_id = ?',$values['parent_id'])
      ->where('approved = ?',1)
      ->where('enabled = ?',1)
      ->where('status = ?',1);

    $this->view->paginator = $paginator = Zend_Paginator::factory($sql);

	  if( $this->_getParam('page') ) {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    $this->view->limit = $limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page',10);
    $this->view->paginator = $paginator = $paginator->setItemCountPerPage($limit);
    
    if(count($paginator) <= 0) {
      return $this->setNoRender();
    }

	}

}
?>
