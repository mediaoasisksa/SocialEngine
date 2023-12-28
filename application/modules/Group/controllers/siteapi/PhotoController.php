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
class Group_PhotoController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
                null !== ($photo = Engine_Api::_()->getItem('group_photo', $photo_id))) {
            Engine_Api::_()->core()->setSubject($photo);
        } else if (0 !== ($group_id = (int) $this->_getParam('group_id')) &&
                null !== ($group = Engine_Api::_()->getItem('group', $group_id))) {
            Engine_Api::_()->core()->setSubject($group);
        } else {
            $this->_forward('throw-error', 'photo', 'group', array(
                "error_code" => "parameter_missing",
                "message" => "photo_id OR group_id"
            ));
            return;
        }

        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->_forward('throw-error', 'photo', 'group', array(
                "error_code" => "no_record"
            ));
            return;
        }
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    /**
     * RETURN THE LIST OF ALL PHOTOS OF GROUP AND UPLOAD PHOTOS ALSO.
     * 
     * @return array
     */
    public function listAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // CHECK AUTHENTICATION
        $subject = Engine_Api::_()->core()->getSubject('group');
        if (!$subject->authorization()->isAllowed($viewer, 'view'))
            $this->respondWithError('unauthorized');

        $bodyResponse = array();
        $bodyResponse['canUpload'] = $subject->authorization()->isAllowed(null, 'photo');

        /* RETURN THE LIST OF IMAGES, IF FOLLOWED THE FOLLOWING CASES:   
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - iF THERE ARE NO $_FILES AVAILABLE.
         */
        if (empty($_FILES) && $this->getRequest()->isGet()) {
            $select = $subject->getSingletonAlbum()->getCollectiblesSelect();

            $requestLimit = $this->getRequestParam("limit", 10);
            $requestPage = $this->getRequestParam("page", 1);

            $paginator = Zend_Paginator::factory($select);
            $paginator->setCurrentPageNumber($requestPage);
            $paginator->setItemCountPerPage($requestLimit);
            $bodyResponse['totalItemCount'] = $paginator->getTotalItemCount();
            foreach ($paginator as $photo) {
                $tempImages = $photo->toArray();

                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
                $tempImages = array_merge($tempImages, $getContentImages);

                $tempImages["canLike"] = $tempImages["is_like"] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
                $tempImages["canComment"] = $photo->authorization()->isAllowed($viewer, 'comment');
                $tempImages['user_title'] = $photo->getOwner()->getTitle();
                $tempImages['like_count'] = $tempImages['likes_count'] = $photo->likes()->getLikeCount();
                $tempImages['is_like'] = ($photo->likes()->isLike($viewer)) ? 1 : 0;

                if (!empty($viewer) && ($tempMenu = $this->getRequestParam('menu', 1)) && !empty($tempMenu)) {
                    if ($photo->canEdit(Engine_Api::_()->user()->getViewer())) {
                        $menu = array();
                        $menu[] = array(
                            'label' => $this->translate('Edit'),
                            'name' => 'edit',
                            'url' => 'groups/photo/edit/' . $photo->getIdentity(),
                            'urlParams' => array(
                            )
                        );

                        $menu[] = array(
                            'label' => $this->translate('Delete'),
                            'name' => 'delete',
                            'url' => 'groups/photo/delete/' . $photo->getIdentity(),
                            'urlParams' => array(
                            )
                        );
                    }

                    $menu[] = array(
                        'label' => $this->translate('Share'),
                        'name' => 'share',
                        'url' => 'activity/share',
                        'urlParams' => array(
                            "type" => $photo->getType(),
                            "id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Report'),
                        'name' => 'report',
                        'url' => 'report/create/subject/' . $photo->getGuid(),
                        'urlParams' => array(
                            "type" => $photo->getType(),
                            "id" => $photo->getIdentity()
                        )
                    );

                    $menu[] = array(
                        'label' => $this->translate('Make Profile Photo'),
                        'name' => 'make_profile_photo',
                        'url' => 'members/edit/external-photo',
                        'urlParams' => array(
                            "photo" => $photo->getGuid()
                        )
                    );

                    $tempImages['menu'] = $menu;
                }

                $bodyResponse['images'][] = $tempImages;
            }

            $this->respondWithSuccess($bodyResponse, true);
        } else if (!empty($_FILES) && $this->getRequest()->isPost()) { // UPLOAD IMAGES TO RESPECTIVE GROUP.
            foreach ($_FILES as $value) {
                Engine_Api::_()->getApi('Siteapi_Core', 'group')->setPhoto($value, $subject, true);
            }

            $this->successResponseNoContent('no_content', true);
        }
    }

    /**
     * VIEW THE PHOTO
     * 
     * @return array
     */
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        $viewer = Engine_Api::_()->user()->getViewer();
        $photo = Engine_Api::_()->core()->getSubject();
        $tempPhoto = $photo->toArray();
        $group = $photo->getGroup();

        if (!$viewer || !$viewer->getIdentity() || !$group->isOwner($viewer)) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        // Add images
        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo);
        $tempPhoto = array_merge($tempPhoto, $getContentImages);

        if (!empty($viewer) && ($tempMenu = $this->getRequestParam('menu', true)) && !empty($tempMenu)) {
            if ($photo->canEdit(Engine_Api::_()->user()->getViewer())) {
                $menu = array();
                $menu[] = array(
                    'label' => $this->translate('Edit'),
                    'name' => 'edit',
                    'url' => 'groups/photo/edit/' . $photo->getIdentity(),
                    'urlParams' => array(
                    )
                );

                $menu[] = array(
                    'label' => $this->translate('Delete'),
                    'name' => 'delete',
                    'url' => 'groups/photo/delete/' . $photo->getIdentity(),
                    'urlParams' => array(
                    )
                );
            }

            $menu[] = array(
                'label' => $this->translate('Share'),
                'name' => 'share',
                'url' => 'activity/index/share',
                'urlParams' => array(
                    "type" => $photo->getType(),
                    "id" => $photo->getIdentity()
                )
            );

            $menu[] = array(
                'label' => $this->translate('Report'),
                'name' => 'report',
                'url' => 'report/create/subject/' . $photo->getGuid()
            );

            $menu[] = array(
                'label' => $this->translate('Make Profile Photo'),
                'name' => 'make_profile_photo',
                'url' => 'members/edit/external-photo',
                'urlParams' => array(
                    "photo" => $photo->getGuid()
                )
            );

            $tempPhoto['menu'] = $menu;
        }

        $this->respondWithSuccess($tempPhoto);
    }

    /**
     * EDIT PHOTO - ADD TITLE AND DESCRIPTION
     * 
     * @return array
     */
    public function editAction() {
        $photo = Engine_Api::_()->core()->getSubject();
        $group = $photo->getParent('group');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$photo->authorization()->isAllowed($viewer, 'edit'))
            $this->respondWithError('unauthorized');

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        /* RETURN THE GROUP PHOTO EDIT FORM IN THE FOLLOWING CASES:      
         * - IF THERE ARE GET METHOD AVAILABLE.
         * - IF THERE ARE NO FORM POST VALUES AVAILABLE.
         */
        if ($this->getRequest()->isGet()) {
            $formValues = $photo->toArray();
            $this->respondWithSuccess(array(
                'form' => Engine_Api::_()->getApi('Siteapi_Core', 'group')->getPhotoEditForm(),
                'formValues' => $formValues
            ));
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            /* SAVE VALUES IN DATABASE AND THEN RETURN RESPONSE ACCORDINGLY. IN THE FOLLOWING CASES:
             * - IF THERE ARE POST METHOD AVAILABLE.
             * - IF THERE ARE FORM POST VALUES AVAILABLE IN VALUES PARAMETER.
             */

            $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
            $db->beginTransaction();
            try {
                // CONVERT POST DATA INTO THE ARRAY.
                $values = array();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'group')->getPhotoEditForm();
                foreach ($getForm as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }

                // START FORM VALIDATION
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'group')->getPhotoEditValidators();
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);
                if (!empty($validationMessage) && @is_array($validationMessage))
                    $this->respondWithValidationError('validation_fail', $validationMessage);

                $photo->setFromArray($values)->save();
                $db->commit();

                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Delete Image
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $photo = Engine_Api::_()->core()->getSubject();
        $group = $photo->getParent('group');
        if (!$photo->authorization()->isAllowed($viewer, 'delete'))
            $this->respondWithError('unauthorized');

        $db = Engine_Api::_()->getDbtable('photos', 'group')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

}
