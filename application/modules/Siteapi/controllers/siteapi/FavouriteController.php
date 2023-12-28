<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FavouriteController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_FavouriteController extends Siteapi_Controller_Action_Standard {

    public function favouriteAction() {
        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND FAVOURITE ID.
        $resource_id = $this->_getParam('resource_id');
        $resource_type = $this->_getParam('resource_type');
        $favourite_id = $this->_getParam('favourite_id');
    
        //GET THE RESOURCE.
        if ($resource_type == 'member') {
            $resource = Engine_Api::_()->getItem('user', $resource_id);
        } else {
            $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
            $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
        }
        //GET THE CURRENT UESRID AND SETTINGS.
        $viewer_id = $loggedin_user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ((empty($loggedin_user_id))) {
            return;
        }
        //CHECK THE FAVOURITE ID.
        if (empty($favourite_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF FAVOURITING AN APPLICATION.
            $favourite_id_temp = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id);
            //CHECK THE THE ITEM IS FAVOURITED OR NOT.
            if (empty($favourite_id_temp[0]['favourite_id'])) {

                $favouriteTable = Engine_Api::_()->getItemTable('seaocore_favourite');
                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $favouriteTable->getAdapter();
                $db->beginTransaction();
                try {

                    //START NOTIFICATION WORK.
                    if ($resource_type == 'forum_topic') {
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->user_id;
                        $label = '{"label":"forum topic"}';
                        $object_type = $resource_type;
                    } else if ($resource_type == 'user') {
                        $getOwnerId = $resource_id;
                        $label = '{"label":"profile"}';
                        $object_type = 'user';
                    } else {
                        if ($resource_type == 'album_photo') {
                            $label = '{"label":"photo"}';
                        } else if ($resource_type == 'group_photo') {
                            $label = '{"label":"group photo"}';
                        } else if ($resource_type == 'sitepageevent_event') {
                            $label = '{"label":"page event"}';
                        } else if ($resource_type == 'sitepage_page') {
                            $label = '{"label":"page"}';
                        } else if ($resource_type == 'sitebusiness_business') {
                            $label = '{"label":"business"}';
                        } else if ($resource_type == 'video') {
                            $label = '{"label":"video"}';
                        } else {
                            $label = '{"label":"' . $resource->getShortType() . '"}';
                        }
                        // if (!strstr($resource_type, 'siteestore_product')) {
                        //     $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;
                        // }
                        $object_type = $resource_type;
                    }
                    if ($object_type == 'sitestore_store')
                        $label = '';

                    if (!empty($resource)) {
                        // if ($resource->getOwner()->getIdentity() != $loggedin_user_id) {
                        //     //ADD NOTIFICATION 
                        //     $notifyData = $notify_table->createRow();
                        //     $notifyData->user_id = $getOwnerId;
                        //     $notifyData->subject_type = $viewer->getType();
                        //     $notifyData->subject_id = $viewer->getIdentity();
                        //     $notifyData->object_type = $object_type;
                        //     $notifyData->object_id = $resource_id;
                        //     $notifyData->type = 'favourited';
                        //     $notifyData->params = $label;
                        //     $notifyData->date = date('Y-m-d h:i:s', time());
                        //     $notifyData->save();
                        // }
                        //ADD FAVOURITE
                        $favourite_id = $favouriteTable->addFavourite($resource, $viewer)->favourite_id;
                    }
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->respondWithValidationError('internal_server_error', $e->getMessage());
                }
                $this->successResponseNoContent('no_content', true);
            } else {
                $response = array();
                $response['response']['favourite_id'] = $favourite_id_temp[0]['favourite_id'];
                $this->respondWithSuccess($response);
            }
        } else {

            //START DELETE NOTIFICATION
            // Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'favourited', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
            //END DELETE NOTIFICATION
            ////START UNFAVOURITE WORK.
            //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNFAVOURITE
            if (!empty($resource) && isset($resource->favourite_count)) {
                $resource->favourite_count--;
                $resource->save();
            }
            $contentTable = Engine_Api::_()->getDbTable('favourites', 'seaocore')->delete(array('favourite_id =?' => $favourite_id));
            //END UNFAVOURITE WORK.
            $this->successResponseNoContent('no_content', true);
        }
    }
}
