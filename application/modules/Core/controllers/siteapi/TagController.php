<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    IndexController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Core_TagController extends Siteapi_Controller_Action_Standard {

    public function addAction() {
        $this->validateRequestMethod('POST');
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $subject_type = $this->_getParam('subject_type');
        $subject_id = $this->_getParam('subject_id');
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

        $extra = Zend_Json::decode($this->_getParam('extra'));

        if (!method_exists($subject, 'tags')) {
            $this->respondWithError('unauthorized', 'whoops! doesn\'t support tagging');
        }

        // GUID tagging
        if (null !== ($guid = $this->_getParam('guid'))) {
            $tag = Engine_Api::_()->getItemByGuid($this->_getParam('guid'));
        }

        // STRING tagging
        else if (null !== ($text = $this->_getParam('label'))) {
            $tag = $text;
        }
        $tagmap = $subject->tags()->addTagMap($viewer, $tag, $extra);

        if (empty($tagmap)) {
            $this->respondWithError('unauthorized', 'Item has already been tagged');
        }

        if (!$tagmap instanceof Core_Model_TagMap) {
            $this->respondWithError('unauthorized', 'Tagmap was not recognised');
        }

        // Do stuff when users are tagged
        if ($tag instanceof User_Model_User && !$subject->isOwner($tag) && !$viewer->isSelf($tag)) {
            // Add activity
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(
                    $viewer, $tag, 'tagged', '', array(
                'label' => str_replace('_', ' ', $subject->getShortType())
                    )
            );
            if ($action)
                $action->attach($subject);

            // Add notification
            $type_name = $this->translate(str_replace('_', ' ', $subject->getShortType()));
            try {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                        $tag, $viewer, $subject, 'tagged', array(
                    'object_type_name' => $type_name,
                    'label' => $type_name,
                        )
                );
            } catch (Exception $ex) {
                
            }
        }

        if (CLIENT_TYPE && ((_CLIENT_TYPE == 'android'))) {
            foreach ($subject->tags()->getTagMaps() as $tagmap) {
                if (($viewer->getIdentity() == $tagmap->tag_id) || ($subject->owner_id == $viewer->getIdentity()))
                    $isRemove = 1;
                else
                    $isRemove = 0;

                $data[] = array_merge($tagmap->toArray(), array(
                    'id' => $tagmap->getIdentity(),
                    'text' => $tagmap->getTitle(),
                    'href' => $tagmap->getHref(),
                    'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id,
                    'isRemove' => $isRemove
                ));
            }
            $this->respondWithSuccess($data);
        }
        $this->successResponseNoContent('no_content');
    }

    public function removeAction() {
        $this->validateRequestMethod('DELETE');
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $subject_type = $this->_getParam('subject_type');
        $subject_id = $this->_getParam('subject_id');
        $subject = Engine_Api::_()->getItem($subject_type, $subject_id);

        // Subject doesn't have tagging
        if (!method_exists($subject, 'tags')) {
            $this->respondWithError('unauthorized', 'Subject doesn\'t support tagging');
        }

        // Get tagmao
        $tagmap_id = $this->_getParam('tagmap_id');
        $tagmap = $subject->tags()->getTagMapById($tagmap_id);
        if (!($tagmap instanceof Core_Model_TagMap)) {
            $this->respondWithError('unauthorized', 'Tagmap missing');
        }

        if ($viewer->getGuid() != $tagmap->tagger_type . '_' . $tagmap->tagger_id &&
                $viewer->getGuid() != $tagmap->tag_type . '_' . $tagmap->tag_id &&
                !$subject->isOwner($viewer)) {
            $this->respondWithError('unauthorized');
        }

        $tagmap->delete();
        if (CLIENT_TYPE && ((_CLIENT_TYPE == 'android'))) {
            foreach ($subject->tags()->getTagMaps() as $tagmap) {
                if (($viewer->getIdentity() == $tagmap->tag_id) || ($subject->owner_id == $viewer->getIdentity()))
                    $isRemove = 1;
                else
                    $isRemove = 0;

                $data[] = array_merge($tagmap->toArray(), array(
                    'id' => $tagmap->getIdentity(),
                    'text' => $tagmap->getTitle(),
                    'href' => $tagmap->getHref(),
                    'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id,
                    'isRemove' => $isRemove
                ));
            }
            $this->respondWithSuccess($data);
        }

        $this->successResponseNoContent('no_content');
    }

    public function suggestAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');

            $usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

            if ((bool) $this->_getParam('message') && $usersAllowed == "everyone") {
                $select = Engine_Api::_()->getDbtable('users', 'user')->select();
                $select->where('user_id <> ?', $viewer->user_id);
            } else {
                $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();
            }

            if ($this->_getParam('includeSelf', false)) {
                $image = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($viewer);
                $data[] = array(
                    'type' => 'user',
                    'id' => $viewer->getIdentity(),
                    'guid' => $viewer->getGuid(),
                    'label' => $viewer->getTitle() . ' ' . $this->translate('(you)'),
                    'photo' => $image['image_icon'],
                    'url' => $viewer->getHref(),
                );
            }

            if (0 < ($limit = (int) $this->_getParam('limit', 10))) {
                $select->limit($limit);
            }

            if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
            }

            $ids = array();
            foreach ($select->getTable()->fetchAll($select) as $friend) {
                $image = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($friend);
                $data[] = array(
                    'type' => 'user',
                    'id' => $friend->getIdentity(),
                    'guid' => $friend->getGuid(),
                    'label' => $friend->getTitle(),
                    'photo' => $image['image_icon'],
                    'url' => $friend->getHref(),
                );
                $ids[] = $friend->getIdentity();
                $friend_data[$friend->getIdentity()] = $friend->getTitle();
            }

            // first get friend lists created by the user
            $listTable = Engine_Api::_()->getItemTable('user_list');
            $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
            $listIds = array();
            foreach ($lists as $list) {
                $listIds[] = $list->list_id;
                $listArray[$list->list_id] = $list->title;
            }

            // check if user has friend lists
            if ($listIds) {
                // get list of friend list + friends in the list
                $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
                $uName = Engine_Api::_()->getDbtable('users', 'user')->info('name');
                $iName = $listItemTable->info('name');

                $listItemSelect = $listItemTable->select()
                        ->setIntegrityCheck(false)
                        ->from($iName, array($iName . '.listitem_id', $iName . '.list_id', $iName . '.child_id', $uName . '.displayname'))
                        ->joinLeft($uName, "$iName.child_id = $uName.user_id")
                        //->group("$iName.child_id")
                        ->where('list_id IN(?)', $listIds);

                $listItems = $listItemTable->fetchAll($listItemSelect);

                $listsByUser = array();
                foreach ($listItems as $listItem) {
                    $listsByUser[$listItem->list_id][$listItem->user_id] = $listItem->displayname;
                }

                foreach ($listArray as $key => $value) {
                    if (!empty($listsByUser[$key])) {
                        $data[] = array(
                            'type' => 'list',
                            'friends' => $listsByUser[$key],
                            'label' => $value,
                        );
                    }
                }
            }
        }
        $this->respondWithSuccess($data);
    }

}
