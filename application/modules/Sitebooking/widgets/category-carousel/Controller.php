<?php

class Sitebooking_Widget_CategoryCarouselController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{

		$front = Zend_Controller_Front::getInstance();
  	$module = $front->getRequest()->getModuleName();
  	$action = $front->getRequest()->getActionName();
  	$controller = $front->getRequest()->getControllerName();

  	$combine = $module.$controller.$action;
	  $this->view->compare = "";

  	if($combine == "sitebookingservicehome" || $combine == "sitebookingservice-providerhome") {
  		
  	} else {
  	//	return $this->setNoRender();
  	}

  	if($combine == "sitebookingservicehome") {
  		$this->view->compare = "serviceHome";
  	}

  	if($combine == "sitebookingservice-providerhome") {
  		$this->view->compare = "providerHome";
  	}

		$values = array();

		$values['limit'] = $this->_getParam('limit', 13);


		$sql = Engine_Api::_()->getItemTable('sitebooking_category')->getMainCategories($values);

		$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll($sql);

	}

}

?>