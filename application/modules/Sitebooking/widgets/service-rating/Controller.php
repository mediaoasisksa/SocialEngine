<?php

class Sitebooking_Widget_ServiceRatingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    if( !Engine_Api::_()->core()->hasSubject('sitebooking_ser') ) {
      return $this->setNoRender();
    }

    $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview');

    if($review_settings == "service_none")
      return $this->setNoRender();

    $this->view->item = $item = Engine_Api::_()->getItem('sitebooking_ser', Zend_Controller_Front::getInstance()->getRequest()->getParam('ser_id'));

    //RATING

    $this->view->rating_count = $table = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking')->ratingCount($item->getIdentity());

    $this->view->rated = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking')->checkRated($item->getIdentity(), $viewer->getIdentity());

    //RATING END
  }
}