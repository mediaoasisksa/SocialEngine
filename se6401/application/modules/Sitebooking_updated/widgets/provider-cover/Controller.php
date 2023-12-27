<?php

class Sitebooking_Widget_ProviderCoverController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
        
  if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
    return $this->setNoRender();
  }
  
  $this->view->item = Engine_Api::_()->core()->getSubject('sitebooking_pro');
  }
}

?>