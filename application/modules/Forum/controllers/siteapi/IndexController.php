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
class Forum_IndexController extends Siteapi_Controller_Action_Standard {

    /**
     * Get forum home page
     * 
     * @return array
     */
    public function indexAction() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        // Validate request methods
        $this->validateRequestMethod();

        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
        
        if (!$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid())
            $this->respondWithError('unauthorized');

        $search_text = $this->getRequestParam('search', '');
        $viewer = Engine_Api::_()->user()->getViewer();
        $categoryTable = Engine_Api::_()->getItemTable('forum_category');
        $categories = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));

        $forumTable = Engine_Api::_()->getItemTable('forum_forum');
        if($search_text){
        $forumSelect = $forumTable->select()
                ->where("(`description` LIKE '%$search_text%' OR `title` LIKE '%$search_text%')")
                ->order('order ASC');
        }
        else{
    $forumSelect = $forumTable->select()
      ->order('order ASC')
      ;
        }

        $forums = array();
        foreach ($forumTable->fetchAll($forumSelect) as $forum) {
            if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'view')) {
                $order = $forum->order;
                while (isset($forums[$forum->category_id][$order])) {
                    $order++;
                }
                $forums[$forum->category_id][$order] = $forum;
                ksort($forums[$forum->category_id]);
            }
        }

        foreach ($categories as $category) {
            if (empty($forums[$category->category_id]))
                continue;

            $tempResponse = array();
            $tempResponse = $category->toArray();
            if (isset($tempResponse['title']) && !empty($tempResponse['title']))
                $tempResponse['title'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempResponse['title']);
            $getForums = array();
            foreach ($forums[$category->category_id] as $forum) {
                $getForums = $forum->toArray();

                if (isset($getForums['title']) && !empty($getForums['title']))
                    $getForums['title'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getForums['title']);

                $getForums['slug'] = $forum->getSlug();
                $getForums['image'] = Engine_Api::_()->getApi('Core', 'siteapi')->getDefaultImage('forum');

                $isAllowedView = $forum->authorization()->isAllowed($viewer, 'view');
                $getForums["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                $tempResponse['forums'][] = $getForums;
            }
            $response[] = $tempResponse;
        }

        $this->respondWithSuccess($response, true);
    }

}
