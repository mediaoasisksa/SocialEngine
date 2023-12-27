<?php class Sitebooking_Widget_ReviewButtonController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    if($subject->getType() === 'sitebooking_pro' ) {

      $this->view->providerItem = $subject;

      $this->view->pro_id = $pro_id = $subject->getIdentity();

      $this->view->reviewHide = '0';

      $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

      if($review_settings == "provider_none" || $review_settings == "provider_onlyRating") {
        $this->view->reviewHide = '1';
      }

      // REVIEW BUTTON WORK
      $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

      $sql = $reviewTable->select();
      $sql->where("engine4_sitebooking_reviews.resource_type like 'sitebooking_pro' AND engine4_sitebooking_reviews.resource_id = ".$pro_id)
      ->where("engine4_sitebooking_reviews.user_id = $viewer->user_id");

      $data = $reviewTable->fetchRow($sql);

      if(!empty($data))
        $this->view->flag = '1';
      else
        $this->view->flag = '0';

    }

    if($subject->getType() === 'sitebooking_ser' ) {
      $ser_id = $subject->getIdentity();

      $this->view->reviewHide = '0';

      $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview');

      if($review_settings == "service_none" || $review_settings == "service_onlyRating") {
        $this->view->reviewHide = '1';
      }

      if($review_settings == "service_Rating&Review")
        $this->view->reviewHide = '0';

      $this->view->providerItem = $providerItem = Engine_Api::_()->getItem('sitebooking_pro',$subject->parent_id);
      $this->view->pro_id = $pro_id = $providerItem->getIdentity();

      // REVIEW BUTTON WORK
      $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

      $sql = $reviewTable->select();
      $sql->where("engine4_sitebooking_reviews.resource_type like 'sitebooking_ser' AND engine4_sitebooking_reviews.resource_id = ".$ser_id)
      ->where("engine4_sitebooking_reviews.user_id = $viewer->user_id");

      $data = $reviewTable->fetchRow($sql);

      if(!empty($data))
        $this->view->flag = '1';
      else
        $this->view->flag = '0';
    }
    
  }
}
?>