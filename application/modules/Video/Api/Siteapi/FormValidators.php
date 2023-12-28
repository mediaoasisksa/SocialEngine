<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Video_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {
    /**
     * Validations of Create OR Edit Form.
     * 
     * @param object $subject get blog object
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array(), $post_attach = 0) {
        $viewer = Engine_Api::_()->user()->getViewer();

        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(array('NotEmpty', true), array('StringLength', false, array(3, 63)))
        );

        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $categories = Engine_Api::_()->video()->getCategories();
        if (count($categories) != 0) {
            $categories_prepared[0] = "";
            foreach ($categories as $category) {
                $categories_prepared[$category->category_id] = $category->category_name;
            }

            if (!empty($categories_prepared)) {
                $formValidators['category_id'] = array(
                    'required' => true,
                    'allowEmpty' => false
                );
            }
        }

        if (empty($subject)) {
            $formValidators['type'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }
        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

        // Element: auth_view
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) != 1) {
                $formValidators['auth_view'] = array(
                    'required' => true,
                    'allowEmpty' => false
                );
            }
        }

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            if (count($commentOptions) != 1) {
                $formValidators['auth_comment'] = array(
                    'required' => true,
                    'allowEmpty' => false
                );
            }
        }

        if (isset($post_attach) && !empty($post_attach) && $post_attach == 1) {
            $formValidators = array();
            if (empty($subject)) {
                $formValidators['type'] = array(
                    'required' => true,
                    'allowEmpty' => false
                );
            }
        }
        return $formValidators;
    }

}
