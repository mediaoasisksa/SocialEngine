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
class Music_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Return the "Create Music" and "Edit Music" form elements validators array.
     * 
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(array('NotEmpty', true), array('StringLength', false, array(3, 63)))
        );

        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['auth_view'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['auth_comment'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

}
