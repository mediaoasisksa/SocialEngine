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
class Core_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Return the "Contact" form elements validators array.
     * 
     * @param array $formValidators array variable
     * @return array
     */
    public function getContactFormValidators($formValidators = array()) {
        $formValidators['name'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['email'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                'EmailAddress'
            )
        );

        $formValidators['body'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

    public function getReportFormValidators($formValidators = array()) {
        $formValidators['category'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        $formValidators['description'] = array(
            'required' => true,
            'allowEmpty' => false
        );

        return $formValidators;
    }

    public function createformvalidators() {
        
        $formValidators = array();
        
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('string', true)
            )
        );
        
        $formValidators['type'] = array(
            'required' => true,
        );
        
        return $formValidators;
        
    }

    public function getcommentValidation() {

        $formValidators['body'] = array(
            'required' => true,
        );

        return $formValidators;
    }

    public function editformvalidators() {
        
        $formValidators = array();
        
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('string', true)
            )
        );
        
        return $formValidators;

    }

}
