<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewMapsListingTypeController.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_AdminProfileMapsContactController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
    public function manageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_maping');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $this->view->profileTypes = $profileTypes = Engine_Api::_()->getApi('fields', 'siteapi')->getProfileTypes();

        $this->view->mappedFieldLabels = Engine_Api::_()->getApi('fields', 'siteapi')->getMappedLabels($profileTypes);
    }

    //ACTION FOR MAP THE PROFILE WITH CATEGORY
    public function mapAction() {
        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->option_id = $option_id = $this->_getParam('option_id');

        //GENERATE THE FORM
        $this->view->form = $form = new Siteapi_Form_Admin_ProfileMapsContact_Map(array('optionId' => $option_id));


        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $value = $this->getRequest()->getPost();
                if (isset($value['contactfield']) && !empty($value['contactfield'])) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting("siteapi_contact_profile_" . $option_id, $value['contactfield']);
                    $db->commit();
                }
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('View Type Mapping has been done successfully.'))
            ));
        }
    }

}
