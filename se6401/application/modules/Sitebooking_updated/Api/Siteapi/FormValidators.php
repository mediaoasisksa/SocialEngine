<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitebooking
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormValidators.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitebooking_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {
    public function getFormValidators($formValidators = array()) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $formValidators['slug'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63)),
                array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitebooking_pros', 'slug'), array('field' => 'pro_id', 'value != ?' => 1))
            )
        );

        $formValidators['designation'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 252))
            )
        );

        $formValidators['description_provider'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 300))
            )
        );

        $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

        if($locationFieldcoreSettings === "yes") {
            $formValidators['location'] = array(
                'required' => true,
                'allowEmpty' => false,
            );
        }
        
        return $formValidators;
    }

    public function getServiceFormValidators($formValidators = array(), $edit = 0) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );
        if(empty($edit)) {
            $formValidators['slug'] = array(
                'required' => true,
                'allowEmpty' => false,
                'validators' => array(
                    array('NotEmpty', true),
                    array('StringLength', false, array(3, 63)),
                    array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
                    array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'sitebooking_pros', 'slug'), array('field' => 'pro_id', 'value != ?' => 1))
                )
            );
        }

        $formValidators['description_service'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 300))
            )
        );

        
        $formValidators['price'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();

        $categories["-1"] = null;
        foreach ($table as $key => $value) { 
          if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
            $categories[$value['category_id']] = $value['category_name'];
        }


        if(count($categories) > 0){
            $formValidators['category_id'] = array(
                'required' => true,
                'allowEmpty' => false,
                );
        }

        $formValidators['duration'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
       
        return $formValidators;
    }

    public function getFieldsFormValidations($values) {
        $option_id = $values['profile_type'];

        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitebooking');
        $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
        $fieldArray = array();
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            $type = $meta->type;
             $label = $meta->label;
            if (!empty($type) && ($type == 'heading'))
                continue;

            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            if (!empty($meta->alias))
                $key = $key . '_' . ( $meta->alias ? 'alias_' . $meta->alias : sprintf('field_%d', $meta->alias->field_id) );
            else {
                $key = $key . '_' . 'field_' . $meta->field_id;
            }
            if (isset($meta->required) && !empty($meta->required))
                $fieldArray[$key] = array(
                    'required' => true,
                    'label'=>$label,
                    'allowEmpty' => false
                );

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }
        return $fieldArray;
    }

    public function getReviewFormValidator($subject = null, $edit = 0) {
        $res = ($subject->getType() == 'sitebooking_ser') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview') :
        Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

        if(empty($edit)){
            $formValidators['rating'] = array(
                'required' => true,
                'allowEmpty' => false,
            );
        }

        if (!strstr($res, "onlyRating")) {
            $formValidators['review'] = array(
                    'required' => true,
                    'allowEmpty' => false,
                    );
        }

        return $formValidators;
    }
}