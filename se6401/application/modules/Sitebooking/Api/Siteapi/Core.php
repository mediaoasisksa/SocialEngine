<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitebooking_Api_Siteapi_Core extends Core_Api_Abstract {
    private $_validateSearchProfileFields = false;
    private $_profileFieldsArray = array();
    private $_create = false;

    public function getServiceProviderForm($provider = null, $edit = 0) {
        $providerForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
                'hasValidator' => true
            );
        $providerForm[] = array(
                'type' => 'Text',
                'name' => 'slug',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('URL')
            );
        $providerForm[] = array(
                'type' => 'Text',
                'name' => 'tags',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
                'description' => 'Separate tags with commas.',
            );
        $providerForm[] = array(
                'type' => 'Text',
                'name' => 'designation',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Designation'),
                'hasValidator' => true
            );

        $providerForm[] = array(
                'type' => 'Textarea',
                'name' => 'description_provider',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
                'hasValidator' => true
            );

        $providerForm[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Profile Photo'),
            );
       
        if(!isset($provider) || empty($provider->status)) {
            $status = array(
                '1'   => 'Published',
                '0'   => 'Draft',
            );
            $providerForm[] = array(
                    'type' => 'Select',
                    'name' => 'status',
                    'multiOptions' => $status,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                    'description' => 'If this entry is published, it cannot be switched back to draft mode.'
                );
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $providerForm[] = array(
                'type' => 'Select',
                'name' => 'timezone',
                'value' => Engine_Api::_()->getItem('user',$viewer)->timezone,
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
                'multiOptions' => array(
                    'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
                    'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
                    'US/Central' => '(UTC-6) Central Time (US & Canada)',
                    'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
                    'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
                    'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
                    'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
                    'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
                    'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
                    'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
                    'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
                    'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
                    'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
                    'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
                    'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
                    'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
                    'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
                    'Iran' => '(UTC+3:30) Tehran',
                    'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
                    'Asia/Kabul' => '(UTC+4:30) Kabul',
                    'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
                    'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
                    'Asia/Katmandu' => '(UTC+5:45) Nepal',
                    'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
                    'Indian/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
                    'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
                    'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
                    'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
                    'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
                    'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
                    'Asia/Magadan' => '(UTC+11) Magadan, Solomon Is., New Caledonia',
                    'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
                  ),
            );

        $locationFieldcoreSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.locationfield",'yes');

        if($locationFieldcoreSettings === "yes") {
            $providerForm[] = array(
                'type' => 'Text',
                'name' => 'location',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter Location'),
                'hasValidator' => true
            );
        }

        $providerForm[] = array(
                'type' => 'submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
            );

        return $providerForm;
        
    }

    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitebooking_ser');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('sitebooking_ser');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }
        return $profileFields;
    }

    private function _getProfileFields($fieldsForm = array(), $search = 0) {
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitebooking_ser');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (
                            (!isset($meta->search) || empty($meta->search))
                           &&
                           (!isset($meta->show) || empty($meta->show))
                    )
                        continue;

                    if (!empty($search) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required) && empty($search))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitebooking')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[$option_id][] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required) && empty($search))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitebooking_ser')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    public function getServiceForm($service = null, $edit = 0) {
        $user = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $user->getIdentity();
        $serviceForm = array();

        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $this->_create = 1;
        $createProfileFields = $this->_getProfileFields();

        $serviceForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
                'hasValidator' => true
            );
        if($edit == 0)
            $serviceForm[] = array(
                    'type' => 'Text',
                    'name' => 'slug',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('URL'),
                    'hasValidator' => true
                );

        $serviceForm[] = array(
                'type' => 'Text',
                'name' => 'description_service',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
                'hasValidator' => true
            );

        if(!isset($service) || empty($service->status)) {
            $status = array(
                '1'   => 'Published',
                '0'   => 'Draft',
            );
            $serviceForm[] = array(
                    'type' => 'Select',
                    'name' => 'status',
                    'multiOptions' => $status,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                    'hasValidator' => true
                );
        }
        $currency_type = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD');

        $serviceForm[] = array(
                'type' => 'Text',
                'name' => 'price',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Price'),
                'hasValidator' => true
            );

        $serviceForm[] = array(
                'type' => 'Text',
                'name' => 'tags',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
            );
        $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();

        $categories = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $getCategories = array();
                // $getCategories[0] = "";
                foreach ($categories as $category) {
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);
                }
                $serviceForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                    'hasValidator' => 'true'
                );

                $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($service->category_id);
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }
                if (isset($getSubCategories) && !empty($getSubCategories) && !isset($_REQUEST['getEditForm'])) {
                    $serviceForm[] = array(
                        'type' => 'Select',
                        'name' => 'first_level_category_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => $getSubCategories,
                    );
                }
                $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($service->second_level_category_id);
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories) && !isset($_REQUEST['getEditForm'])) {
                    $serviceForm[] = array(
                        'type' => 'Select',
                        'name' => 'second_level_category_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => $getSubSubCategories,
                    );
                }
            }
            $categoryProfileTypeMapping = array();
            $categories = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getCategories(array('category_id', 'category_name', 'profile_type'), null, 0, 0, 1);

            if (count($categories) != 0) {
                foreach ($categories as $category) {
                    $subCategories = array();
                    $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($category->category_id);
                    $getCategories[$category->category_id] = $category->category_name;

                    if (isset($category->profile_type) && !empty($category->profile_type)){

                        $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;
                    }

                    $getsubCategories = array();

                    foreach ($subCategoriesObj as $subcategory) {

                        $subsubCategories = array();
                        $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($subcategory->category_id);

                        $subsubCategories = array();
                        foreach ($subsubCategoriesObj as $subsubcategory) {
                            $subsubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                        }
                        if (isset($subsubCategories) && !empty($subsubCategories)) {

                            $subsubCategoriesForm[$subcategory->category_id] = array(
                                'type' => 'Select',
                                'name' => 'second_level_category_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                'multiOptions' => $subsubCategories,
                            );
                        }
                        $getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                    }
                    if (isset($getsubCategories) && !empty($getsubCategories) && count($getsubCategories) > 0) {
                        $subcategoriesForm = array(
                            'type' => 'Select',
                            'name' => 'first_level_category_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                            'multiOptions' => $getsubCategories,
                        );
                    }
                    if (isset($subcategoriesForm) && !empty($subcategoriesForm)) {
                        $form[$category->category_id]['form'] = $subcategoriesForm;
                        $subcategoriesForm = array();
                    }
                    if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0)
                        $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                    $subsubCategoriesForm = array();
                }
                $categoriesForm = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                    'hasValidator' => 'true'
                );
            }
            $defaultProfileId = !empty($service->profile_type) ? $service->profile_type : 0 ;
                    // Set profile fields along with create form on editing
            if (isset($service) && !empty($service) && isset($defaultProfileId) && !empty($defaultProfileId) && is_array($createProfileFields)) {
            
            if (isset($createProfileFields[$defaultProfileId]) && !empty($createProfileFields[$defaultProfileId])) {
                $serviceForm = array_merge($serviceForm, $createProfileFields[$defaultProfileId]);
            }
        }

        //Time Duration Slots
        $durationTable = Engine_Api::_()->getDbTable('durations','sitebooking');
        $durationItems = $durationTable->fetchAll($durationTable->select()->where('action = ?',1));
        foreach ($durationItems as $key => $value) {
          $seconds = $value->duration;
          $duration[$seconds] = Engine_Api::_()->sitebooking()->showServiceDuration($value->duration);
        }
    
        $serviceForm[] = array(
                'type' => 'Select',
                'name' => 'duration',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Duration in minutes / hours'),
                'multiOptions' => $duration,
                'hasValidator' => true
            );
        
        $serviceForm[] = array(
                'type' => 'File',
                'name' => 'photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Profile Photo'),
            );
        $serviceForm[] = array(
                'type' => 'submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
            );

        $responseForm['form'] = $serviceForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;

        if (_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION > '3.6.4') {

            if (is_array($createProfileFields) && is_array($categoryProfileTypeMapping)) {

                foreach ($categoryProfileTypeMapping as $key => $value) {

                    if (isset($createProfileFields[$value]) && !empty($createProfileFields[$value])) {
                        if(!$responseForm['fields'][$key]) {
                            $responseForm['fields'][$key] = array();
                        }

                        $responseForm['fields'][$key] = array_merge($responseForm['fields'][$key],$createProfileFields[$value]);

                        $createProfileFieldsForm[$key] = $createProfileFields[$value];
                    }
                }
            }
        }
        else{
            
            if (is_array($createProfileFields) && is_array($categoryProfileTypeMapping)) {
                if(count($categoryProfileTypeMapping) > 1){
                    foreach ($categoryProfileTypeMapping as $key => $value) {

                        if (isset($createProfileFields[$value]) && !empty($createProfileFields[$value])) {
                            $createProfileFieldsForm[$key] = $createProfileFields[$value];
                        }
                    }
                    if (isset($createProfileFieldsForm) && !empty($createProfileFieldsForm)) {
                        $responseForm['fields'] = $createProfileFieldsForm;
                    }
                }
                else{

                    foreach ($categoryProfileTypeMapping as $key => $value) {
                        $createProfileFieldsForm = $createProfileFields;
                        
                    }
                    if (isset($createProfileFieldsForm) && !empty($createProfileFieldsForm)) {
                        $responseForm['fields'] = $createProfileFieldsForm;
                    }

                }
            }
        }
        return $responseForm;   
    }

    public function getReviewCreateForm($subject = null, $edit = 0) {

        $res = ($subject->getType() == 'sitebooking_ser') ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview') :
        Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.providerReview');

        if(empty($edit)) {
            $createReview[] = array(
                'type' => 'Rating',
                'name' => 'rating',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Rating'),
            );
        }

        if (strstr($res, "Rating&Review")) {
            $createReview[] = array(
                'type' => 'Textarea',
                'name' => 'review',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Review'),
            );
        }
        
        $createReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add your Opinion'),
        );
        return $createReview;
    }

    public function getTellAFriendForm() {
        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        $tell[] = array(
            'type' => 'Checkbox',
            'name' => 'send_me',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
        );


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );
        return $tell;
    }
    public function getSubjectFieldsInfo(Core_Model_Item_Abstract $spec, $params = array()) {
        $structure = Engine_Api::_()->fields()->getFieldsStructurePartial($spec);
        $helper = new Siteapi_View_Helper_Fields_FieldValueLoop();
        $fieldsInfo = $helper->fieldValueLoop($spec, $structure, $params);
        return $fieldsInfo;
    }

    public function getSearchForm() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $createForm = array();

        // Page title
        $createForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Providers'),
        );

        $createForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => 'Most Recent',
                'view_count' => 'Most Viewed',
            ),
        );

        $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll();

        foreach ($table as $key => $value) {
          if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
          {
            $category_value[$value['category_id']]  = $value['category_name'];
          }
        }

        $createForm[] = array(
            'type' => 'Select',
            'name' => 'category',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
            'multiOptions' => $category_value,
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'location',
            'label' => 'Location',
        );

        $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
        if($unit === 'miles'){
            $createForm[] = array(
            'type' => 'Select',
            'name' => 'locationDistance',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Within'),
            'multiOptions' => array(
              '0' => '',
              '1' => '1 Mile',
              '2' => '2 Mile',
              '5' => '5 Mile',
              '10' => '10 Mile',
              '20' => '20 Mile',
              '50' => '50 Mile',
              '100' => '100 Mile',
              '200' => '200 Mile',
              '500' => '500 Mile',
              '750' => '750 Mile',
              '1000' => '1000 Mile',
            ),
            'value' => '50',
          );
        }else if($unit === 'kilometers'){
          $createForm[] = array(
            'type' => 'Select',
            'name' => 'locationDistance',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Within'),
            'multiOptions' => array(
              '0' => '',
              '1' => '1 Kilometer',
              '2' => '2 Kilometers',
              '5' => '5 Kilometers',
              '10' => '10 Kilometers',
              '20' => '20 Kilometers',
              '50' => '50 Kilometers',
              '100' => '100 Kilometers',
              '200' => '200 Kilometers',
              '500' => '500 Kilometers',
              '750' => '750 Kilometers',
              '1000' => '1000 Kilometers',
              ),
            'value' => '50',
          );
        }

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'city',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'country',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
        );

        $createForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );

        return $createForm;
    }

    public function getServiceSearchForm() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $createForm = array();

        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $this->_create = 1;
        $createProfileFields = $this->_getProfileFields($createForm,1);

        // Page title
        $createForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Services'),
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'provider',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Provider Name'),
        );

        $createForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => 'Most Recent',
                'view_count' => 'Most Viewed',
            ),
        );

        $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll();
        

        $categories = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $getCategories = array();
                
                foreach ($categories as $category) {
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);
                }
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                );

                $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($service->category_id);
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }
                if (isset($getSubCategories) && !empty($getSubCategories) && !isset($_REQUEST['getEditForm'])) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'first_level_category_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => $getSubCategories,
                    );
                }
                $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($service->second_level_category_id);
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories) && !isset($_REQUEST['getEditForm'])) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'second_level_category_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => $getSubSubCategories,
                    );
                }
            }
            $categoryProfileTypeMapping = array();
            $categories = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getCategories(array('category_id', 'category_name', 'profile_type'), null, 0, 0, 1);

            if (count($categories) != 0) {
                foreach ($categories as $category) {
                    $subCategories = array();
                    $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($category->category_id);
                    $getCategories[$category->category_id] = $category->category_name;

                    if (isset($category->profile_type) && !empty($category->profile_type)){

                        $categoryProfileTypeMapping[$category->category_id] = $category->profile_type;
                    }

                    $getsubCategories = array();

                    foreach ($subCategoriesObj as $subcategory) {

                        $subsubCategories = array();
                        $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitebooking')->getSubCategories($subcategory->category_id);

                        $subsubCategories = array();
                        foreach ($subsubCategoriesObj as $subsubcategory) {
                            $subsubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                        }
                        if (isset($subsubCategories) && !empty($subsubCategories)) {

                            $subsubCategoriesForm[$subcategory->category_id] = array(
                                'type' => 'Select',
                                'name' => 'second_level_category_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                'multiOptions' => $subsubCategories,
                            );
                        }
                        $getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                    }
                    if (isset($getsubCategories) && !empty($getsubCategories) && count($getsubCategories) > 0) {
                        $subcategoriesForm = array(
                            'type' => 'Select',
                            'name' => 'first_level_category_id',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                            'multiOptions' => $getsubCategories,
                        );
                    }
                    if (isset($subcategoriesForm) && !empty($subcategoriesForm)) {
                        $form[$category->category_id]['form'] = $subcategoriesForm;
                        $subcategoriesForm = array();
                    }
                    if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0)
                        $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                    $subsubCategoriesForm = array();
                }
                $categoriesForm = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                );
            }
            $defaultProfileId = !empty($service->profile_type) ? $service->profile_type : 0 ;
                    // Set profile fields along with create form on editing
            if (isset($service) && !empty($service) && isset($defaultProfileId) && !empty($defaultProfileId) && is_array($createProfileFields)) {
            
            if (isset($createProfileFields[$defaultProfileId]) && !empty($createProfileFields[$defaultProfileId])) {
                $serviceForm = array_merge($serviceForm, $createProfileFields[$defaultProfileId]);
            }
        }


        $createForm[] = array(
            'type' => 'Text',
            'name' => 'location',
            'label' => 'Location',
        );

        $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.search",'miles');
        if($unit === 'miles'){
            $createForm[] = array(
            'type' => 'Select',
            'name' => 'locationDistance',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Within'),
            'multiOptions' => array(
              '0' => '',
              '1' => '1 Mile',
              '2' => '2 Mile',
              '5' => '5 Mile',
              '10' => '10 Mile',
              '20' => '20 Mile',
              '50' => '50 Mile',
              '100' => '100 Mile',
              '200' => '200 Mile',
              '500' => '500 Mile',
              '750' => '750 Mile',
              '1000' => '1000 Mile',
            ),
            'value' => '50',
          );
        }else if($unit === 'kilometers'){
          $createForm[] = array(
            'type' => 'Select',
            'name' => 'locationDistance',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Within'),
            'multiOptions' => array(
              '0' => '',
              '1' => '1 Kilometer',
              '2' => '2 Kilometers',
              '5' => '5 Kilometers',
              '10' => '10 Kilometers',
              '20' => '20 Kilometers',
              '50' => '50 Kilometers',
              '100' => '100 Kilometers',
              '200' => '200 Kilometers',
              '500' => '500 Kilometers',
              '750' => '750 Kilometers',
              '1000' => '1000 Kilometers',
              ),
            'value' => '50',
          );
        }

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'city',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
        );

        $createForm[] = array(
            'type' => 'Text',
            'name' => 'country',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
        );

        $createForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );
          
        
        $responseForm['form'] = $createForm;

        if (isset($form) && !empty($form))
            $responseForm['subcategories'] = $form;

        if (_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION > '3.6.4') {

            if (is_array($createProfileFields) && is_array($categoryProfileTypeMapping)) {

                foreach ($categoryProfileTypeMapping as $key => $value) {

                    if (isset($createProfileFields[$value]) && !empty($createProfileFields[$value])) {
                        if(!$responseForm['fields'][$key]) {
                            $responseForm['fields'][$key] = array();
                        }

                        $responseForm['fields'][$key] = array_merge($responseForm['fields'][$key],$createProfileFields[$value]);

                        $createProfileFieldsForm[$key] = $createProfileFields[$value];
                    }
                }
            }
        }
        else{
            
            if (is_array($createProfileFields) && is_array($categoryProfileTypeMapping)) {
                if(count($categoryProfileTypeMapping) > 1){
                    foreach ($categoryProfileTypeMapping as $key => $value) {

                        if (isset($createProfileFields[$value]) && !empty($createProfileFields[$value])) {
                            $createProfileFieldsForm[$key] = $createProfileFields[$value];
                        }
                    }
                    if (isset($createProfileFieldsForm) && !empty($createProfileFieldsForm)) {
                        $responseForm['fields'] = $createProfileFieldsForm;
                    }
                }
                else{

                    foreach ($categoryProfileTypeMapping as $key => $value) {
                        $createProfileFieldsForm = $createProfileFields;
                        
                    }
                    if (isset($createProfileFieldsForm) && !empty($createProfileFieldsForm)) {
                        $responseForm['fields'] = $createProfileFieldsForm;
                    }

                }
            }
        }
        return $responseForm;
    }


    /**
     * Set the profile fields value to newly created listing.
     * 
     * @return array
     */
    public function setProfileFields($service, $data) {
// Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($service);

        $fVals = $data;
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();
        foreach ($fVals as $key => $value) {
            if (strstr($key, 'oauth'))
                continue;
            $parts = explode('_', $key);
            if (count($parts) < 3)
                continue;
            list($parent_id, $option_id, $field_id) = $parts;

            $valueParts = explode(',', $value);

// Array mode
            if (is_array($valueParts) && count($valueParts) > 1) {
// Lookup
                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $service->getIdentity()
                ));
// Delete all
                foreach ($valueRows as $valueRow) {
                    $valueRow->delete();
                }
                if ($field_id == 0)
                    continue;
// Insert all
                $indexIndex = 0;
                if (is_array($valueParts) || !empty($valueParts)) {
                    foreach ((array) $valueParts as $singleValue) {

                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $service->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $service->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

// Scalar mode
            else {
                try {
// Lookup
                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $service->getIdentity()
                    ));

                    if ($field_id == 0)
                        continue;
// Lookup
                    $valueRow = $values->getRowMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $service->getIdentity(),
                        'index' => 0
                    ));
// Create if missing
                    $isNew = false;
                    if (!$valueRow) {
                        $isNew = true;
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $service->getIdentity();
                    }
                    $valueRow->value = htmlspecialchars($value);
                    $valueRow->save();
                } catch (Exception $ex) {
                   
                }
            }
        }

        return;
    }

    public function getInformation($service, $edit = 0) {
        $profileFields = $this->getProfileTypes();

        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        if (isset($edit) && !empty($edit))
            $information = $this->getProfileInfo($service, $edit);
        else {
            $information = $this->getProfileInfo($service);
            ;
            foreach ($information as $key => $value) {
                if (isset($value) && !empty($value) && is_array($value)) {
                    $information[$key] = @implode(", ", $value);
                }
            }
        }
        return $information;
    }

        // Get the Profile Fields Information, which will show on profile page.
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitebooking')->defaultProfileId();
        // Getting the default Profile Type id.
        $getFieldId = (!empty($subject->profile_type)) ? $subject->profile_type : $defaultProfileId;
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);

        $fieldValues = array();

        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {

            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();

            $specificProfileFields[$getFieldId] = $_getProfileFields[$getFieldId];

            foreach ($specificProfileFields as $heading => $tempValue) {

                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;
                
                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {
                            $tempValue = $fieldRow->value;
                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = !empty($setKeyAsResponse) ? $fieldRow->value : $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            if (isset($fieldValues[$tempKey]) && !empty($fieldValues[$tempKey])) {
                                if (is_array($fieldValues[$tempKey])) {
                                    $fieldValues[$tempKey][] = $tempValue;
                                } else {
                                    $fieldValues[$tempKey] = array($fieldValues[$tempKey], $tempValue);
                                }
                            } else if (isset($value['type']) && !empty($value['type']) && ($value['type'] == 'Multi_checkbox' || $value['type'] == 'Multiselect') && isset($value['multiOptions']) && !empty($value['multiOptions'])) {
                                $fieldValues[$tempKey][] = $tempValue;
                            } else {
                                $fieldValues[$tempKey] = $tempValue;
                            }
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields();

            foreach ($_getProfileFields as $value) {
                if (!isset($value['name']))
                    continue;
                if (!isset($value['label']))
                    continue;
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $fieldRow->value;
                        }
                    }
                }
            }
        }
        return $fieldValues;
    }
}