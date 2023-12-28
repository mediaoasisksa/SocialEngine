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
class Poll_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param array $formValidators: Form elements array.
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
        if (empty($subject)) { // Following validators will work only for create form.
            $formValidators['title'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(3, 63))
                )
            );

            $formValidators['description'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('StringLength', false, array(1, 10000))
                )
            );

            $formValidators['options_1'] = array(
                'required' => true,
                'allowEmpty' => false,
            );

            $formValidators['options_2'] = array(
                'required' => true,
                'allowEmpty' => false,
            );
        }

        $formValidators['auth_view'] = array(
            'required' => true
        );

        $formValidators['auth_comment'] = array(
            'required' => true
        );

        return $formValidators;
    }

}
