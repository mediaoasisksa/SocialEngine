<?php class Sitebooking_Widget_ServiceProviderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }

    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');
    $parent_id = $service->parent_id;

    $this->view->provider = Engine_Api::_()->getItem('sitebooking_pro',$parent_id);
  }
}
?>