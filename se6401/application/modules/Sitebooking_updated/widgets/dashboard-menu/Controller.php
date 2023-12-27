<?php
class Sitebooking_Widget_DashboardMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // DONT RENDER IF SUBJECT IS NOT SET
  if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
    return $this->setNoRender();
  }
  
  // GET NAVIGATION
  $this->view->dashboardNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitebooking_provider_dashboard");

  if (Count($this->view->dashboardNavigation) <= 0) {
    return $this->setNoRender();
  }
  }
}
?>