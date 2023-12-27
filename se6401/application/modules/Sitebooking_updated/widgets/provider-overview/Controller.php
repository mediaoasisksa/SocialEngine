<?php

class Sitebooking_Widget_ProviderOverviewController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
    return $this->setNoRender();
  }

  $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');

  $pro_id = $provider->getIdentity();
  $overview = Engine_Api::_()->getDbtable('providersoverviews','sitebooking');
    $this->view->item = $item = $overview->fetchRow('pro_id = '.$pro_id);

    if(empty($item)){
    return $this->setNoRender();
  }    
  }
}


?>