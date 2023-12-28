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
class Event_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param object $subject get object
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
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
            'allowEmpty' => false
        );

        $formValidators['starttime'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['endtime'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['category_id'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true)
            )
        );

        $formValidators['auth_view'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['auth_comment'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['auth_photo'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

    /**
     * Validations of event photo edit form
     * 
     * @param object $subject get object
     * @param array $formValidators array variable
     * @return array
     */
    public function getPhotoEditValidators($subject = array(), $formValidators = array()) {
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
            'allowEmpty' => false
        );

        return $formValidators;
    }

}
