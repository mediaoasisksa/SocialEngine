<?php

class Sitebooking_Widget_UserReviewController extends Engine_Content_Widget_Abstract {

   
  public function indexAction() {

    //CHECK SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    // GET SUBJECT
    $this->view->sitebooking = $sitebooking = Engine_Api::_()->core()->getSubject();

    $resource_type = $sitebooking->getType();

    $this->view->user_id = $user_id = $viewer->getIdentity();

    $resource_id = $sitebooking->getIdentity();

    //REVIEWS TABLE
    $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

    $select = $table->select()->where('resource_id = ?', $resource_id)
      ->where('resource_type = ?', $resource_type);
    
    $paginator = Zend_Paginator::factory($select);
    
    //review_count
    $this->view->review_count = $paginator->getTotalItemCount();

    //resource item
    $resource_item = Engine_Api::_()->getItem($resource_type, $resource_id);

    //avgRating
    $this->view->avgRating = $resource_item['rating'];
    //rating_count
    $this->view->rating_count = $resource_item['rating_count'];

    $review = Engine_Api::_()->getDbtable('reviews', 'sitebooking');
    $reviewName = $review->info('name');

    // for service
    if($resource_type === 'sitebooking_ser') {

      $star_arr = array();

      $serviceRating = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

      $star_arr = $serviceRating->show_rating_by_star($resource_id);

      $this->view->one_star = $star_arr[0];
      $this->view->two_star = $star_arr[1];
      $this->view->three_star = $star_arr[2];
      $this->view->four_star = $star_arr[3];
      $this->view->five_star = $star_arr[4];

      $rating = $serviceRating->fetchRow($serviceRating->select()->where('ser_id = ?', $resource_id)
      ->where('user_id = ?', $user_id));

      $this->view->myRating = $rating['rating'];

      //USER REVIEW
      $serviceRatingName = $serviceRating->info('name');

      //REVIEW
      $select = $review->select();
      $select
        ->setIntegrityCheck(false)
        ->from($reviewName)
        ->join($serviceRatingName, "$reviewName.user_id = $serviceRatingName.user_id");

      $sql = $reviewName.".resource_id = ".$resource_id." AND ".$reviewName.".resource_type = '".$resource_type."' AND ".$serviceRatingName.".ser_id = ".$resource_id;
      $select->group($reviewName.'.user_id');
      $select->where($sql);
      // END

      if ($this->_getParam('isAjax')) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
        $this->getElement()->removeDecorator('');
        $this->view->isAjax = true;
      }

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);

      if( $this->_getParam('page') )
      {
        $paginator->setCurrentPageNumber($this->_getParam('page'));
      }
      $items_per_page = 1;

      $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);

    }

    //for service provider
    if($resource_type === 'sitebooking_pro') {

      $star_arr = array();

      $providerRating = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');

      $star_arr = $providerRating->show_rating_by_star($resource_id);

      $this->view->one_star = $star_arr[0];
      $this->view->two_star = $star_arr[1];
      $this->view->three_star = $star_arr[2];
      $this->view->four_star = $star_arr[3];
      $this->view->five_star = $star_arr[4];

      $rating = $providerRating->fetchRow($providerRating->select()->where('pro_id = ?', $resource_id)
      ->where('user_id = ?', $user_id));

      $this->view->myRating = $rating['rating'];


      //USER REVIEW
      $providerRatingName = $providerRating->info('name');

      // REVIEW
      $select = $review->select();
      $select
        ->setIntegrityCheck(false)
        ->from($reviewName)
        ->join($providerRatingName, "$reviewName.user_id = $providerRatingName.user_id");

      $sql = $reviewName.".resource_id = ".$resource_id." AND ".$reviewName.".resource_type = '".$resource_type."' AND ".$providerRatingName.".pro_id = ".$resource_id;
      $select->group($reviewName.'.user_id');
      $select->where($sql);
      // END

      if ($this->_getParam('isAjax')) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
        $this->getElement()->removeDecorator('');
        $this->view->isAjax = true;
      }

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);

      if( $this->_getParam('page') )
      {
        $paginator->setCurrentPageNumber($this->_getParam('page'));
      }
      $items_per_page = 1;

      $this->view->paginator = $paginator->setItemCountPerPage($items_per_page);

    }

  }

}