<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    EditController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_EditController extends Siteapi_Controller_Action_Standard {

    public function init() {
        if (!Engine_Api::_()->core()->hasSubject()) {
            // Can specifiy custom id
            $id = $this->getRequestParam('id');
            $subject = null;
            if (null === $id) {
                $subject = Engine_Api::_()->user()->getViewer();
                Engine_Api::_()->core()->setSubject($subject);
            } else {
                $subject = Engine_Api::_()->getItem('user', $id);
                Engine_Api::_()->core()->setSubject($subject);
            }
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
    }

    /**
     * Make the pass photo as my profile photo.
     * 
     * @return array
     */
    public function externalPhotoAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');

        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('unauthorized');

        $user = Engine_Api::_()->core()->getSubject();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        // Get photo
        $requestPhoto = $this->getRequestParam('photo');
        $photo = Engine_Api::_()->getItemByGuid($requestPhoto);
        if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id))
            $this->respondWithError('no_record');

        if (!$photo->authorization()->isAllowed(null, 'view'))
            $this->respondWithError('unauthorized');

        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Get the owner of the photo
            $photoOwnerId = null;
            if (isset($photo->user_id)) {
                $photoOwnerId = $photo->user_id;
            } else if (isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user')) {
                $photoOwnerId = $photo->owner_id;
            }

            // if it is from your own profile album do not make copies of the image
            if ($photo instanceof Album_Model_Photo &&
                    ($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
                    $photoParent->owner_id == $photoOwnerId &&
                    $photoParent->type == 'profile') {

                // ensure thumb.icon and thumb.profile exist
                $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
                $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
                if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile')) {
                    try {
                        $tmpFile = $newStorageFile->temporary();
                        $image = Engine_Image::factory();
                        $image->open($tmpFile)
                                ->resize(200, 400)
                                ->write($tmpFile)
                                ->destroy();
                        $iProfile = $filesTable->createFile($tmpFile, array(
                            'parent_type' => $user->getType(),
                            'parent_id' => $user->getIdentity(),
                            'user_id' => $user->getIdentity(),
                            'name' => basename($tmpFile),
                        ));
                        $newStorageFile->bridge($iProfile, 'thumb.profile');
                        @unlink($tmpFile);
                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->respondWithValidationError('internal_server_error', $e->getMessage());
                    }
                }

                if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon')) {
                    try {
                        $tmpFile = $newStorageFile->temporary();
                        $image = Engine_Image::factory();
                        $image->open($tmpFile);
                        $size = min($image->height, $image->width);
                        $x = ($image->width - $size) / 2;
                        $y = ($image->height - $size) / 2;
                        $image->resample($x, $y, $size, $size, 48, 48)
                                ->write($tmpFile)
                                ->destroy();
                        $iSquare = $filesTable->createFile($tmpFile, array(
                            'parent_type' => $user->getType(),
                            'parent_id' => $user->getIdentity(),
                            'user_id' => $user->getIdentity(),
                            'name' => basename($tmpFile),
                        ));
                        $newStorageFile->bridge($iSquare, 'thumb.icon');
                        @unlink($tmpFile);
                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->respondWithValidationError('internal_server_error', $e->getMessage());
                    }
                }

                // Set it
                $user->photo_id = $photo->file_id;
                $user->save();

                // Insert activity
                // @todo maybe it should read "changed their profile photo" ?
                $action = Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($user, $user, 'profile_photo_update', '{item:$subject} changed their profile photo.');
                if ($action) {
                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')
                            ->attachActivity($action, $photo);
                }
            }

            // Otherwise copy to the profile album
            else {
                $user->setPhoto($photo);

                // Insert activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')
                        ->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

                // Hooks to enable albums to work
                $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
                $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUserProfilePhotoUpload', array(
                    'user' => $user,
                    'file' => $newStorageFile,
                ));

                $attachment = $event->getResponse();
                if (!$attachment) {
                    $attachment = $newStorageFile;
                }

                if ($action) {
                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')
                            ->attachActivity($action, $attachment);
                }
            }

            $db->commit();
            $this->successResponseNoContent('no_content');
        }

        // Otherwise it's probably a problem with the database or the storage system (just throw it)
        catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Edit profile fields
     * 
     * @return array
     */
    public function profileAction() {
        $siteapiEditProfileFields = Zend_Registry::isRegistered('siteapiEditProfileFields') ? Zend_Registry::get('siteapiEditProfileFields') : null;
        $user = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // RETURN IF NO VIEWER AVAILABLE.
        if (empty($siteapiEditProfileFields) || empty($viewer_id))
            $this->respondWithError('unauthorized');

        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($user);
            $getFieldId = $optionId->value;
        }

        if (empty($getFieldId))
            $this->respondWithError('profile_type_missing');

        if ($this->getRequest()->isGet()) {
            $getFieldEditForm = Engine_Api::_()->getApi('Siteapi_Core', 'user')->getFieldEditForm($getFieldId);
            $this->respondWithSuccess($getFieldEditForm);
        } else if ($this->getRequest()->isPut() || $this->getRequest()->isPost()) {
            $data = $values = $_REQUEST;

            $values['profile_type'] = $data['profile_type'] = $getFieldId;
            $data['account_validation'] = 0;
            $data['fields_validation'] = 1;

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'user')->getSignupFormValidators($data);
            $data['validators'] = $validators;
            $validationMessage = $this->isValid($data);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            try {
                // Profile Fields: start work to save profile fields.
                $profileTypeField = null;
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                }

                // Save the profile fields information.
                if ($profileTypeField)
                    Engine_Api::_()->getApi('Siteapi_Core', 'user')->setProfileFields($user, $data);
                //save data in search Table
                Engine_Api::_()->getApi('Siteapi_Core', 'user')->setSerachVal($user,$data, $profileTypeField);

                // Set Displayname
                $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
                $user->setDisplayName($aliasValues);
                $user->save();

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Edit profile photo
     * 
     * @return array
     */
    public function photoAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // RETURN IF NO VIEWER AVAILABLE.
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');


        if ($this->getRequest()->isGet()) {
            $viewerArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($viewer);

            // Add images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($viewer);
            $viewerArray = array_merge($viewerArray, $getContentImages);
            $this->respondWithSuccess($viewerArray);
        } else if ($this->getRequest()->isPost()) {
            if (empty($_FILES))
                $this->respondWithError('unauthorized');

            try {
                // Set photo
                if (!empty($_FILES['photo']))
                    Engine_Api::_()->getApi('Siteapi_Core', 'user')->setPhoto($_FILES['photo'], $viewer);

                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /**
     * Remove the photo
     * 
     * @return array
     */
    public function removePhotoAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        // RETURN IF NO VIEWER AVAILABLE.
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $viewer->photo_id = 0;
        $viewer->save();

        $this->successResponseNoContent('no_content');
    }

}
