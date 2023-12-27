<?php class Sitebooking_Widget_ServiceOptionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }

    //GET NAVIGATION
    $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitebooking_service_gutter");

    if (Count($this->view->gutterNavigation) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>