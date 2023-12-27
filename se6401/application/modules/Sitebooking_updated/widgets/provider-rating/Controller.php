<?php

class Sitebooking_Widget_ProviderRatingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_pro') ) {
      return $this->setNoRender();
    }

    $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

    if($review_settings == "provider_none")
      return $this->setNoRender();

    $this->view->item = $item = Engine_Api::_()->getItem('sitebooking_pro', Zend_Controller_Front::getInstance()->getRequest()->getParam('pro_id'));

    $this->view->rating_count = Engine_Api::_()->getDbtable('providerratings', 'sitebooking')->ratingCount($item->getIdentity());

    $this->view->rated = Engine_Api::_()->getDbtable('providerratings', 'sitebooking')->checkRated($item->getIdentity(), $viewer->getIdentity());

  }
}