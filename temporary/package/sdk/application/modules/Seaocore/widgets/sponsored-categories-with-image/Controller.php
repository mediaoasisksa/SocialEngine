<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Widget_SponsoredCategoriesWithImageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->contentModuleSponsoredCategories = $contentModuleSponsoredCategories = $this->_getParam('contentModuleSponsoredCategories');
        $this->view->width =  $this->_getParam('width',275);
        $this->view->height = $this->_getParam('height',275);
        if (empty($contentModuleSponsoredCategories)) {
            return $this->setNoRender();
        }
        switch ($contentModuleSponsoredCategories) {

            case 'sitevideo_video':
                //GET CATEGORY TABLE
                $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('videoCategories', 'sitevideo');

                //GET SPONSORED CATEGORIES
                $this->view->categories = $paginator = $tableCategory->getCategoriesPaginator(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'video_id', 'category_slug', 'cat_dependency', 'subcat_dependency', 'featured_tagline'), 'sponsored' => 1, 'cat_depandancy' => 0, 'limit' => 6, 'video_id' => true));
                
                
                break;
            case 'sitevideo_channel':
                //GET CATEGORY TABLE
                $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
                //GET SPONSORED CATEGORIES
                $this->view->categories = $paginator = $tableCategory->getCategoriesPaginator(array('fetchColumns' => array('category_id', 'category_name', 'cat_order', 'video_id', 'category_slug', 'cat_dependency', 'subcat_dependency', 'featured_tagline'), 'sponsored' => 1, 'cat_depandancy' => 0, 'limit' => 6, 'video_id' => true));
                break;
        }
        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        $paginator->setItemCountPerPage(6);
        //GET SPONSORED CATEGORIES COUNT
        $this->view->totalCategories = $paginator->getTotalItemCount();
        
        if ($this->view->totalCategories <= 0) {
            return $this->setNoRender();
        }
    }

}
