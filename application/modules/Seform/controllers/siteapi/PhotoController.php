<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    PhotoController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Classified_PhotoController extends Siteapi_Controller_Action_Standard {

  public function init() {
    if ( !Engine_Api::_()->core()->hasSubject() ) {
      if ( 0 !== ($photo_id = ( int ) $this->getRequestParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('classified_photo', $photo_id)) ) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if ( 0 !== ($classified_id = ( int ) $this->getRequestParam('classified_id')) &&
              null !== ($classified = Engine_Api::_()->getItem('classified', $classified_id)) ) {
        Engine_Api::_()->core()->setSubject($classified);
      }
    }

    if ( !Engine_Api::_()->core()->hasSubject() ) {
      if( !$this->getRequestParam('photo_id') && !$this->getRequestParam('classified_id') ) {
        $this->_forward('throw-error', 'photo', 'classified', array(
            "error_code" => "parameter_missing",
            "message" => "photo_id OR classified_id"
        ));
        return;
      }else {
        $this->_forward('throw-error', 'photo', 'classified', array(
            "error_code" => "no_record"
        ));
        return;
      }
    }
  }

  /**
   * Throw the init constructor errors.
   *
   * @return array
   */
  public function throwErrorAction() {
    $message = $this->getRequestParam("message", null);
    if ( ($error_code = $this->getRequestParam("error_code")) && !empty($error_code) ) {
      if ( !empty($message) )
        $this->respondWithValidationError($error_code, $message);
      else
        $this->respondWithError($error_code);
    }

    return;
  }

  /**
   * Getting the list of photos.
   *
   * @return array
   */
  public function listAction() {
    // Validate request methods
    $this->validateRequestMethod();
    
    $viewer = Engine_Api::_()->user()->getViewer();

    // CHECK AUTHENTICATION
    $classified = Engine_Api::_()->core()->getSubject('classified');
    if ( !$classified->authorization()->isAllowed($viewer, 'view') )
      $this->respondWithError('unauthorized');

    $bodyResponse['canUpload'] = $classified->authorization()->isAllowed(null, 'photo');
    $select = $classified->getSingletonAlbum()->getCollectiblesSelect();

    $requestLimit = $this->getRequestParam("limit", 10);
    $requestPage = $this->getRequestParam("page", 1);

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($requestPage);
    $paginator->setItemCountPerPage($requestLimit);
    $bodyResponse['totalItemCount'] = $paginator->getTotalItemCount();
    foreach ( $paginator as $photo ) {
      $tempImages = $photo->toArray();

      // Add images
      $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
      $tempImages = array_merge($tempImages, $getContentImages);

      $tempImages['user_title'] = $photo->getOwner()->getTitle();
      $tempImages['likes_count'] = $photo->likes()->getLikeCount();
      $tempImages['is_like'] = ($photo->likes()->isLike($viewer)) ? 1 : 0;
      
      if($viewer->getIdentity() && $classified->authorization()->isAllowed($viewer, 'edit')) {
          $tempImages['menu'][] = array(
              'label' => $this->translate('Delete'),
              'name' => 'delete',
              'url' => 'albums/photo/delete',
              'urlParams' => array(
                  "classified_id" => $classified->getIdentity(),
                  "photo_id" => $photo->getIdentity()
              )
          );
      }


      $bodyResponse['images'][] = $tempImages;
    }

    $this->respondWithSuccess($bodyResponse, true);
  }

  /**
   * View respective photo
   *
   * @return array
   */
  public function viewAction() {
    // Validate request methods
    $this->validateRequestMethod();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject();
    $tempPhoto = $photo->toArray();

    // Add images
    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
    $tempPhoto = array_merge($tempPhoto, $getContentImages);

    $tempPhoto['canEdit'] = $canEdit = $photo->authorization()->isAllowed(null, 'photo.edit');
    
    if($viewer->getIdentity() && !empty($canEdit)) {
        $tempImages['menu'][] = array(
            'label' => $this->translate('Delete'),
            'name' => 'delete',
            'url' => 'albums/photo/delete',
            'urlParams' => array(
                "classified_id" => $classified->getIdentity(),
                "photo_id" => $photo->getIdentity()
            )
        );
    }

    $this->respondWithSuccess($tempPhoto);
  }

  /**
   * Upload new photo
   *
   * @return array
   */
  public function uploadAction() {
    // Validate request methods
    $this->validateRequestMethod('POST');
    
    $classified = Engine_Api::_()->core()->getSubject('classified');
    if ( empty($classified) )
      $this->respondWithError('no_record');

    if ( empty($_FILES) ) {
      $errorParams = $this->translate('NO FILES');
      $this->respondWithValidationError('parameter_missing', $errorParams);
    }

    // Process
    $table = Engine_Api::_()->getItemTable('classified_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $classified, 'classified_photo_upload', null, array('count' => count($_FILES)));

      foreach ( $_FILES as $value ) {
        $photo = Engine_Api::_()->getApi('Siteapi_Core', 'classified')->setPhoto($value, $classified, false);

        if ( $action instanceof Activity_Model_Action && $count < 8 ) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
      }

      $db->commit();
      $this->successResponseNoContent('no_content', true);      
    } catch ( Exception $e ) {
      $db->rollBack();
      $this->respondWithValidationError('internal_server_error', $e->getMessage());
    }
  }

  /**
   * Remove photo
   *
   * @return array
   */
  public function removeAction() {
    // Validate request methods
    $this->validateRequestMethod('DELETE');
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    
    if ( empty($viewer_id) )
      $this->respondWithError('unauthorized');

    $photo = Engine_Api::_()->core()->getSubject('classified_photo');
    if ( empty($photo) )
      $this->respondWithError('no_record');


    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();

      $this->successResponseNoContent('no_content', true);
      $db->commit();
    } catch ( Exception $e ) {
      $db->rollBack();
      $this->respondWithValidationError('internal_server_error', $e->getMessage());
    }
  }

  /**
   * Make photo as classified cover
   *
   * @return array
   */
  public function makePhotoCoverAction() {
    // Validate request methods
    $this->validateRequestMethod('POST');
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if ( empty($viewer_id) )
      $this->respondWithError('unauthorized');

    if ( !$this->getRequest()->isPost() )
      $this->respondWithError('invalid_method');
    
    $photo = Engine_Api::_()->core()->getSubject('classified_photo');
    if ( empty($photo) )
      $this->respondWithError('no_record');

    $classified = Engine_Api::_()->getItem('classified', $photo->classified_id);
    $classified->modified_date = date('Y-m-d H:i:s');
    $classified->photo_id = $photo->photo_id;
    $classified->save();

    $this->successResponseNoContent('no_content');
  }

}