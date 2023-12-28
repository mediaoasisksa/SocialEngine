<?php

class Sitebooking_Widget_ProviderInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  if (!Engine_Api::_()->core()->hasSubject('sitebooking_pro')) {
    return $this->setNoRender();
  }
  $this->view->item = $provider = Engine_Api::_()->core()->getSubject('sitebooking_pro');

  $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

  if($review_settings == "provider_none" || $review_settings == "provider_onlyRating") {
    $this->view->reviewHide = '1';
  }

  $pro_id = $provider->getIdentity();   
  $this->view->flag = '0';
  }

}

?>