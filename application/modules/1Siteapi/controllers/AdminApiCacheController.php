<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminApiCacheController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_AdminApiCacheController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_caching');

        $this->view->form = $form = new Siteapi_Form_Admin_ApiCaching_Create();

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            try {
                include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';

                $this->view->form = $form = new Siteapi_Form_Admin_ApiCaching_Create();
                $form->addNotice("Your changes have been saved.");
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

}
