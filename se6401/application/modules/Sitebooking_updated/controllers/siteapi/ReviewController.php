<?php

class Sitebooking_ReviewController extends Siteapi_Controller_Action_Standard
{

  public function createAction()
  {

    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError('unauthorized');

    $subject_type = $this->_getParam('subject_type');
    $subject_id = $this->_getParam('subject_id');

    $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $user_id = $viewer->getIdentity();

    $type =  $subject->getType();
    $identity =  $subject->getIdentity();

    if ($this->getRequest()->isGet()) {
      $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitebooking')->getReviewCreateForm($subject);
      $this->respondWithSuccess($response, true);
    }
    else{
      if( $subject->getType() === 'sitebooking_ser' ) {

        //RATING
        $resource_id = $subject->ser_id;
        $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

        $rating_count = $ratingTable->ratingCount($subject->getIdentity());

        $rated = $ratingTable->checkRated($subject->getIdentity(), $viewer->getIdentity());

        $rating_row = $ratingTable->getMyRating($identity, $user_id);
        //RATING END
      }

      // Provider
      if( $subject->getType() === 'sitebooking_pro' ) {
        
        //RATING
        $resource_id = $subject->pro_id; 

        $providerRatingtable = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
        $rating_count = $providerRatingtable->ratingCount($subject->getIdentity());

        $rated = $providerRatingtable->checkRated($subject->getIdentity(), $viewer->getIdentity());

        $rating_row = $providerRatingtable->getMyRating($identity, $user_id);
      }

      $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

      $review_row = $table->fetchRow($table->select()->where('resource_id = ?', $identity)
      ->where('resource_type = ?', $type)
      ->where('user_id = ?', $user_id));


      $database = Engine_Api::_()->getItemTable($type)->getAdapter();
      $database->beginTransaction();

      $db = $table->getAdapter();
      $db->beginTransaction();
      try {

          $formValues['rating'] = $this->_getParam('rating');
          $formValues['review'] = $_REQUEST['review'];

           // Start form validation
          $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'Sitebooking')->getReviewFormValidator($subject);
          $formValues['validators'] = $validators;
          $validationMessage = $this->isValid($formValues);

          if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
          }

          $res = ($subject->getType() == 'sitebooking_ser') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview') :
          Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

          if (strstr($res, "Rating&Review")) {
            // Insert In reviews table
            $row = $table->createRow();
            $row->review = $_REQUEST['review'];
            $row->resource_id = $identity;
            $row->resource_type = $type;
            $row->user_id = $user_id;
            $row->creation_date = date('Y-m-d H:i:s');
            $row->save();
            $db->commit();

            //Review_count increase in both service nad provider table
            $resourceTable = Engine_Api::_()->getItem($type, $identity);
            $resourceTable['review_count'] = $resourceTable['review_count'] + 1;
            $resourceTable->save();
            $database->commit();
          }
          
          $resourceTb = Engine_Api::_()->getItem($type, $resource_id);
          //check if user already rated or not and save rating in db also
          if( $subject->getType() === 'sitebooking_ser' ) {             
            
            $ratingTable->setRating($resource_id, $user_id, $formValues['rating']);

            $resourceTb->rating = $ratingTable->getRating($resourceTb->getIdentity());
            $resourceTb->rating_count = $ratingTable->ratingCount($resourceTb->getIdentity());

            $resourceTb->save();
            $database->commit();

            $rating_row = $ratingTable->getMyRating($identity, $user_id);
          }

          if( $subject->getType() === 'sitebooking_pro' ) { 
            $providerRatingtable->setRating($resource_id, $user_id, $formValues['rating']);

            $resourceTb->rating = $providerRatingtable->getRating($resourceTb->getIdentity());
            $resourceTb->rating_count = $providerRatingtable->ratingCount($resourceTb->getIdentity());

            $resourceTb->save();

            $database->commit();

            $rating_row = $providerRatingtable->getMyRating($identity, $user_id);
          }

      } catch (Exception $e) {
        $db->rollBack();
        $database->rollBack();
        $this->respondWithValidationError('internal_server_error', $e->getMessage());

      }
      $this->successResponseNoContent('no_content', true);
    }
  }
  public function editAction() {
    if( !$this->_helper->requireUser()->isValid() )
      $this->respondWithError('unauthorized');

    $subject_type = $this->_getParam('subject_type');
    $subject_id = $this->_getParam('subject_id');

    $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $user_id = $viewer->getIdentity();

    $type =  $subject->getType();
    $identity =  $subject->getIdentity();

    if( $subject->getType() === 'sitebooking_ser' ) {

      //RATING
      $resource_id = $subject->ser_id;
      $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

      $rating_count = $ratingTable->ratingCount($subject->getIdentity());

      $rated = $ratingTable->checkRated($subject->getIdentity(), $viewer->getIdentity());

      $rating_row = $ratingTable->getMyRating($identity, $user_id);
      //RATING END
    }

    // Provider
    if( $subject->getType() === 'sitebooking_pro' ) {
      //RATING
      $resource_id = $subject->pro_id; 

      $providerRatingtable = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
      $rating_count = $providerRatingtable->ratingCount($subject->getIdentity());

      $rated = $providerRatingtable->checkRated($subject->getIdentity(), $viewer->getIdentity());

      $rating_row = $providerRatingtable->getMyRating($identity, $user_id);
    }

    $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

    $review_row = $table->fetchRow($table->select()->where('resource_id = ?', $identity)
      ->where('resource_type = ?', $type)
      ->where('user_id = ?', $user_id));

    if ($this->getRequest()->isGet()) {  
      $form_fields['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Sitebooking')->getReviewCreateForm($subject,1);

      if(!empty($rating_row[0]['rating']))
        $form_fields['formValues']['rating'] = $rating_row[0]['rating'];
      else
        $form_fields['formValues']['rating'] = 0;

      if (!empty($review_row->review))
        $form_fields['formValues']['review'] = $review_row->review;
      else
        $form_fields['formValues']['review'] = '';
      
      $this->respondWithSuccess($form_fields);
    }

    // Check post/form
    if( $this->getRequest()->isPost() ) {
      $database = Engine_Api::_()->getItemTable($type)->getAdapter();
      $database->beginTransaction();

      $db = $table->getAdapter();
      $db->beginTransaction();
      try {

          $formValues['review'] = $_REQUEST['review'];
           // Start form validation
          $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'Sitebooking')->getReviewFormValidator($subject,1);
          $formValues['validators'] = $validators;
          $validationMessage = $this->isValid($formValues);

          if (!empty($validationMessage) && @is_array($validationMessage)) {
            $this->respondWithValidationError('validation_fail', $validationMessage);
          }

          $res = ($subject->getType() == 'sitebooking_ser') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview') :
          Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

          if (strstr($res, "Rating&Review")) {

           // Update
            $review_row->review = $_REQUEST['review'];
            $review_row->resource_id = $identity;
            $review_row->resource_type = $type;
            $review_row->user_id = $user_id; 
            $review_row->save();
          }
          $db->commit();
      } catch (Exception $e) {
          $db->rollBack();
          $database->rollBack();
          $this->respondWithValidationError('internal_server_error', $e->getMessage());

      }
      $this->successResponseNoContent('no_content', true);
    }
  }

}