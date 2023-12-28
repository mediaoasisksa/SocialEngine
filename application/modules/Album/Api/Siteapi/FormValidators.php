<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidation.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Album_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param array $formValidators: Form elements array.
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $flag = 1;
        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1))
            $flag=1;
        if (empty($subject) && empty($_REQUEST['create_new_album']) && $flag == 1 ) {
            $formValidators['album'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }

        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $categories = Engine_Api::_()->getDbtable('categories', 'album')->getCategoriesAssoc();
        if (count($categories) > 0)
            $formValidators['category_id'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('Int', true)
                )
            );

        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) > 1) {
            $formValidators['auth_view'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) > 1) {
            $formValidators['auth_comment'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }

        $formValidators['auth_tag'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

}
