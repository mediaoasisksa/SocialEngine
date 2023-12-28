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
class Seaocore_FavouriteController extends Core_Controller_Action_Standard {

    public function favouriteAction() {

        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND FAVOURITE ID.
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $favourite_id = $this->_getParam('favourite_id');
        $status = $this->_getParam('smoothbox', 1);
        $this->view->status = true;
        //GET THE FAVOURITE BUTTON SETTINGS.
        $this->view->favourite_setting_button = Engine_Api::_()->getApi('settings', 'core')->getSetting('favourite.setting.button');

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
        $this->view->viewer_id = $loggedin_user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
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
                        if (!strstr($resource_type, 'siteestore_product')) {
                            $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;
                        }
                        $object_type = $resource_type;
                    }
                    if ($object_type == 'sitestore_store')
                        $label = '';

                    if (!empty($resource)) {
                        if ($resource->getOwner()->getIdentity() != $loggedin_user_id) {
                            //ADD NOTIFICATION 
                            $notifyData = $notify_table->createRow();
                            $notifyData->user_id = $getOwnerId;
                            $notifyData->subject_type = $viewer->getType();
                            $notifyData->subject_id = $viewer->getIdentity();
                            $notifyData->object_type = $object_type;
                            $notifyData->object_id = $resource_id;
                            $notifyData->type = 'favourited';
                            $notifyData->params = $label;
                            $notifyData->date = date('Y-m-d h:i:s', time());
                            $notifyData->save();
                        }
                        //ADD FAVOURITE
                        $favourite_id = $favouriteTable->addFavourite($resource, $viewer)->favourite_id;
                    }

                    //PASS THE FAVOURITE ID VALUE.
                    $this->view->favourite_id = $favourite_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $favourite_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Favourited.');
            } else {
                $this->view->favourite_id = $favourite_id_temp[0]['favourite_id'];
            }
        } else {

            //START DELETE NOTIFICATION
            Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'favourited', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
            //END DELETE NOTIFICATION
            ////START UNFAVOURITE WORK.
            //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNFAVOURITE
            if (!empty($resource) && isset($resource->favourite_count)) {
                $resource->favourite_count--;
                $resource->save();
            }
            $contentTable = Engine_Api::_()->getDbTable('favourites', 'seaocore')->delete(array('favourite_id =?' => $favourite_id));
            //END UNFAVOURITE WORK.
            $favourite_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unfavourited.');
        }
        if (empty($status)) {
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array($favourite_msg)
                    )
            );
        }
        //HERE THE CONTENT TYPE MEANS MODULE NAME
        $num_of_contenttype = Engine_Api::_()->getApi('favourite', 'seaocore')->favouriteCount($resource_type, $resource_id);
        $favourites_number = $this->view->translate(array('%s favourite', '%s favourites', $num_of_contenttype), $this->view->locale()->toNumber($num_of_contenttype));
        $this->view->num_of_favourite = "<a href='javascript:void(0);' onclick='showSmoothBox(); return false;' >" . $favourites_number . "</a>";
    }

    //ACTION FOR FAVOURITES THE LISTING
    public function favouritelistAction() {

        //GET SETTINGS
        $favourite_user_str = 0;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');

        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);

        $search = $this->_getParam('search', '');
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);

        $this->view->search = $search;
        //if (empty($search)) {
        // $this->view->search = $this->view->translate('Search Members');
        //}

        $favouriteTableName = Engine_Api::_()->getDBTable('favourites', 'seaocore')->info('name');
        $memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');
        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        if ($call_status == 'friend') {

            $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($favouriteTableName, array('poster_id'))
                    ->joinInner($memberTableName, "$memberTableName . user_id = $favouriteTableName . poster_id", NULL)
                    ->joinInner($userTableName, "$userTableName . user_id = $memberTableName . user_id")
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($favouriteTableName . '.resource_type = ?', $resource_type)
                    ->where($favouriteTableName . '.resource_id = ?', $resource_id)
                    ->where($favouriteTableName . '.poster_id != ?', $viewer_id)
                    ->where($favouriteTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order('	favourite_id DESC');
        } else if ($call_status == 'public') {

            $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($favouriteTableName, array('poster_id'))
                    ->joinInner($userTableName, "$userTableName . user_id = $favouriteTableName . poster_id")
                    ->where($favouriteTableName . '.resource_type = ?', $resource_type)
                    ->where($favouriteTableName . '.resource_id = ?', $resource_id)
                    ->where($favouriteTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order($favouriteTableName . '.favourite_id DESC');
        }

        $fetch_sub = Zend_Paginator::factory($sub_status_select);
        $fetch_sub->setCurrentPageNumber($page);
        $fetch_sub->setItemCountPerPage(10);
        $check_object_result = $fetch_sub->getTotalItemCount();

        $this->view->user_obj = array();
        if (!empty($check_object_result)) {
            $this->view->user_obj = $fetch_sub;
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }

        //TOTAL FAVOURITES
        $this->view->public_count = Engine_Api::_()->sitevideo()->favouriteCount($resource_type, $resource_id);

        //NUMBER OF FRIENDS FAVOURITES
        $this->view->friend_count = Engine_Api::_()->getApi('favourite', 'seaocore')->userFriendNumberOffavourite($resource_type, $resource_id, 'friendNumberOfFavourite');

        //GET FAVOURITE TITLE
        if ($resource_type == 'member') {
            $this->view->favourite_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
        } else {
            $this->view->favourite_title = Engine_Api::_()->getItem($resource_type, $resource_id)->title;
        }
    }

}
