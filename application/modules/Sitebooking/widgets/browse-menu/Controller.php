<?php

class Sitebooking_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  // Get navigation
  $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
  ->getNavigation('sitebooking_main');
    
  if( count($this->view->navigation) == 1 ) {
    $this->view->navigation = null;
  }
  }
}

?>