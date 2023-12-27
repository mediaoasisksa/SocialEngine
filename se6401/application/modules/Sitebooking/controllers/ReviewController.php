<?php

class Sitebooking_ReviewController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {

    if( !$this->_helper->requireUser()->isValid() ) {return;} 

    $subjectGuid = $this->_getParam('subject');

    $this->view->item = $subject= Engine_Api::_()->getItemByGuid($subjectGuid);

    $this->view->title = $subject->title;
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $user_id = $viewer->getIdentity();

    $type =  $subject->getType();
    $identity =  $subject->getIdentity();

    if( $subject->getType() === 'sitebooking_ser' ) {

      //RATING
      $this->view->id = $resource_id = $subject->ser_id;
      $ratingTable = Engine_Api::_()->getDbtable('serviceratings', 'sitebooking');

      $this->view->rating_count = $ratingTable->ratingCount($subject->getIdentity());

      $this->view->rated = $ratingTable->checkRated($subject->getIdentity(), $viewer->getIdentity());

      $rating_row = $ratingTable->getMyRating($identity, $user_id);

      try{
        $this->view->pre_rate = $rating_row[0]['rating'];
        $flag = 1; 
      }
      catch (Exception $e) { 
        $this->view->pre_rate = 0;
        $flag = 0;
      }

      //RATING END

    }


    // Provider
    if( $subject->getType() === 'sitebooking_pro' ) {
      
      //RATING
      $this->view->id = $resource_id = $subject->pro_id; 

      $providerRatingtable = Engine_Api::_()->getDbtable('providerratings', 'sitebooking');
      $this->view->rating_count = $providerRatingtable->ratingCount($subject->getIdentity());

      $this->view->rated = $providerRatingtable->checkRated($subject->getIdentity(), $viewer->getIdentity());

      $rating_row = $providerRatingtable->getMyRating($identity, $user_id);

      try{
        $this->view->pre_rate = $rating_row[0]['rating'];
        $flag = 1; 
      }
      catch (Exception $e) { 
        $this->view->pre_rate = 0;
        $flag = 0;
      }
    }


    $this->view->form = $form = new Sitebooking_Form_Service_Review();

    $table = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

    $review_row = $table->fetchRow($table->select()->where('resource_id = ?', $identity)
    ->where('resource_type = ?', $type)
    ->where('user_id = ?', $user_id));

    // Populate form
    if($review_row != NULL)
      $form->populate($review_row->toArray());


    if(empty($_POST['submit']))
      return;
    
    //check user give rating or not on button click
    if($_POST['rating'] == '' && $flag == 0){
      return $form->addError('Please give Rating');
    }

    // If not post or form not valid, return
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    $database = Engine_Api::_()->getItemTable($type)->getAdapter();
    $database->beginTransaction();

    $db = $table->getAdapter();
    $db->beginTransaction();
    try {

      if($review_row == NULL) {

        // Insert In reviews table
        $row = $table->createRow();
        $row->review = $_POST['review'];
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

        $rating = $this->_getParam('rating');
        $resourceTb = Engine_Api::_()->getItem($type, $resource_id);

        //check if user already rated or not and save rating in db also
        if( $subject->getType() === 'sitebooking_ser' ) {             
          
          $ratingTable->setRating($resource_id, $user_id, $rating);

          $resourceTb->rating = $ratingTable->getRating($resourceTb->getIdentity());
          $resourceTb->rating_count = $ratingTable->ratingCount($resourceTb->getIdentity());

          $resourceTb->save();

          $database->commit();

          $rating_row = $ratingTable->getMyRating($identity, $user_id);

        }

        if( $subject->getType() === 'sitebooking_pro' ) { 


          $providerRatingtable->setRating($resource_id, $user_id, $rating);

          $resourceTb->rating = $providerRatingtable->getRating($resourceTb->getIdentity());
          $resourceTb->rating_count = $providerRatingtable->ratingCount($resourceTb->getIdentity());

          $resourceTb->save();

          $database->commit();

          $rating_row = $providerRatingtable->getMyRating($identity, $user_id);
        }

        $this->view->pre_rate = $rating_row[0]['rating'];
        $this->view->rated = 1;
        $form->populate($row->toArray());
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Review has been posted successfully.');

        if( $subject->getType() === 'sitebooking_ser' ) {
          return $this->_forward('success' ,'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('ser_id' => $identity, 'pro_id' => $subject->parent_id, 'slug' => $subject->slug), 'sitebooking_service_entry_view', true),
            'messages' => Array($this->view->message)
          ));
        }

        if( $subject->getType() === 'sitebooking_pro' ) {
          return $this->_forward('success' ,'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $subject->owner_id, 'pro_id' => $identity, 'slug' => $subject->slug), 'sitebooking_provider_view', true),
            'messages' => Array($this->view->message)
          ));
        }
      } else {

        // Update
        $review_row->review = $_POST['review'];
        $review_row->resource_id = $identity;
        $review_row->resource_type = $type;
        $review_row->user_id = $user_id; 
        $review_row->save();
        $db->commit();
        $form->populate($review_row->toArray());
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Review has been updated successfully.');

        if( $subject->getType() === 'sitebooking_ser' ) {
          return $this->_forward('success' ,'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('ser_id' => $identity, 'pro_id' => $subject->parent_id, 'slug' => $subject->slug), 'sitebooking_service_entry_view', true),
            'messages' => Array($this->view->message)
          ));
        }

        if( $subject->getType() === 'sitebooking_pro' ) {
          return $this->_forward('success' ,'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $subject->owner_id, 'pro_id' => $identity, 'slug' => $subject->slug), 'sitebooking_provider_view', true),
            'messages' => Array($this->view->message)
          ));
        }
      }

    } catch (Exception $e) {
      
      $db->rollBack();
      $database->rollBack();

      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Review has not been updated successfully, please try again.');

      if( $subject->getType() === 'sitebooking_ser' ) {
        return $this->_forward('success' ,'utility', 'core', array(
          'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('ser_id' => $identity, 'pro_id' => $subject->parent_id, 'slug' => $subject->slug), 'sitebooking_service_entry_view', true),
          'messages' => Array($this->view->message)
        ));
      }

      if( $subject->getType() === 'sitebooking_pro' ) {
        return $this->_forward('success' ,'utility', 'core', array(
          'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $subject->owner_id, 'pro_id' => $identity, 'slug' => $subject->slug), 'sitebooking_provider_view', true),
          'messages' => Array($this->view->message)
        ));
      }
    }

  }

}