<?php class Sitebooking_Widget_ServiceOverviewController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }
    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');
    $ser_id = $service->getIdentity();
    $serviceOverviewTable = Engine_Api::_()->getDbTable('serviceoverviews','sitebooking');
    $this->view->item = $serviceOverviewTable->fetchRow($serviceOverviewTable->select()->where('ser_id = ?',$ser_id));
     
  }
}
?>