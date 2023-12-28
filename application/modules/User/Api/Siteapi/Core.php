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
class User_Api_Siteapi_Core extends User_Api_Core {

    private $_profileFieldsArray = array();
    private $_validateSearchProfileFields = false;

    /**
     * Get the "User Signup" form.
     * 
     * @return array
     */
    public function getSignupForm() {
        if ($_GET['subscriptionForm'] == 1) {
            $accountForm['subscription'] = $this->_getChooseSubscriptionForm();
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteiosapp')) {
                $accountForm['siteiosappMode'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.current.mode', 1);
                $accountForm['siteiosappSharedSecretKey'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.shared.secret');
            }
        }
        //For OTP Plugin
        $enableOtp = $this->hasEnableOtp();

        if ($enableOtp) {
            $accountForm['account'] = Engine_Api::_()->getApi('Siteapi_Core', 'siteotpverifier')->getSignupAccountForm();
            $accountForm['otpForm'][] = $this->_otpForm();
            $accountForm['isEnableotp'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.enableotp', 1);
            $accountForm['isDiffrentField'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.singupShowBothPhoneAndEmail', 1);

            $profileFields = $this->getProfileTypes();
            if (!empty($profileFields)) {
                $this->_profileFieldsArray = $profileFields;
            }
        } else {
            $accountForm['account'] = $this->_getSignupAccountForm();
            $accountForm['isEnableotp'] = 0;
        }

        $isEmailVerificationEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.verifyemail', 0);
        $accountForm['isEmailVerificationEnable'] = 0;
        if ($isEmailVerificationEnable == 2 || $isEmailVerificationEnable == 3)
            $accountForm['isEmailVerificationEnable'] = 1;

        $accountForm['fields'] = $this->_getUserProfileFields();
        $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
        $stepSelect = $stepTable->select()->where('class = ?', 'User_Plugin_Signup_Photo');
        $row = $stepTable->fetchRow($stepSelect);

        if (empty($row) || empty($row->enable)) {
            $stepSelect = $stepTable->select()->where('class = ?', 'Whcore_Plugin_Signup_Photo');
            $row = $stepTable->fetchRow($stepSelect);
        }

        if (empty($row) || empty($row->enable)) {
            $stepSelect = $stepTable->select()->where('class = ?', 'Sitelogin_Plugin_Signup_Photo');
            $row = $stepTable->fetchRow($stepSelect);
        }

        if (!empty($row) && !empty($row->enable))
            $accountForm['photo'] = $this->_getSignupPhotoForm();



        return $accountForm;
    }

    // Search Profile Fields
    public function getSearchProfileFields() {
        $this->_validateSearchProfileFields = true;
        $this->_profileFieldsArray = $this->getProfileTypes();
        $getProfileFields = $this->_getUserProfileFields();

        return $getProfileFields;
    }

    public function getFieldEditForm($getFieldId) {
        $user = Engine_Api::_()->user()->getViewer();
        $display = true;

        // Set the default profile type.
        $this->_profileFieldsArray[$getFieldId] = $getFieldId;
        $getProfileFields = $this->_getUserProfileFields(null, $display);

        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($user);
        $fieldValues = array();
        foreach ($getProfileFields as $tempValue) {
            foreach ($tempValue as $value) {
                $key = $value['name'];
                $parts = @explode('_', $key);
                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $user->getIdentity()
                ));

                try {
                     $fieldObj = Engine_Api::_()->fields()->getField($field_id, 'user');
                } catch (Exception $ex) {
                    //blank Exception
                }
                $multiCheckBox=array();
                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {

                        if(strstr($key, 'birthdate') && empty($fieldRow->value))
                            continue;
                        if ($fieldObj && $fieldObj->type == 'multi_checkbox' || $fieldObj->type == 'multiselect' || $fieldObj->type == strtolower('MultiCheckbox'))
                        {
                            $multiCheckBox[] = $fieldRow->value;
                            $fieldValues[$key]['value'] = $multiCheckBox;
                        }
                        else
                        {
                           $fieldValues[$key]['value'] = $fieldRow->value;
                           $fieldValues[$key]['privacy'] = isset($fieldRow->privacy) ? $fieldRow->privacy : ''; 
                        }

                    }
                }
            }
        }

        return array(
            'form' => $getProfileFields,
            'formValues' => $fieldValues
        );
    }

    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }

        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    // Get the Profile Fields Information, which will show on profile page.
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($subject);
        $display = true;
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();

        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $getProfileFields = $this->_getUserProfileFields(null, $display);
            foreach ($getProfileFields as $heading => $tempValue) {
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
                                $tempValue = $value['multiOptions'][$fieldRow->value];

                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;

                            if ($tempKey == 'Country') {
                                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                                $tempValue = (isset($countries[$tempValue]) && !empty($countries[$tempValue])) ? $countries[$tempValue] : $tempValue;
                            }
                            if (strstr($key,"birthdate")) {
                                if ($subject instanceof User_Model_User && $subject->birthday_format == 'monthday')
                                    $tempValue = date('F j', strtotime($tempValue));
                                else 
                                    $tempValue = date('F j,Y', strtotime($tempValue));
                            }
                            if (isset($fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)])) {
                                if (is_array($fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)])) {
                                    $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)][] = $tempValue;
                                } else {
                                    $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)] = array($fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)], $tempValue);
                                }
                            } else if (isset($value['type']) && !empty($value['type']) && ($value['type'] == 'MultiCheckbox' || $value['type'] == 'Multiselect') && isset($value['multiOptions']) && !empty($value['multiOptions'])) {
                                $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)][] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempValue);
                            } else
                                $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempValue);
                        }
                    }
                }
                if (empty($setKeyAsResponse) && isset($fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)])) {
                    foreach ($fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)] as $key => $value) {
                        if (isset($value) && !empty($value) && is_array($value)) {
                            $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($heading)][$key] = @implode(", ", $value);
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $getContentProfileFields = $this->getContentProfileFields($getType);

            foreach ($getContentProfileFields as $value) {
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

                            if ($tempKey == 'Country') {
                                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                                $fieldRow->value = $countries[$fieldRow->value];
                            }

                            $subject_type = $subject->getType();

                            // In case of Select or Multi Select the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($fieldRow->value) && $value['type'] == 'Multiselect') {
                                $fieldRowValue = "";
                                $counter = 0;
                                $tempMultiselectValues = @explode(',', $fieldRow->value);

                                if (!empty($setKeyAsResponse)) {
                                    $fieldRowValue = $tempMultiselectValues;
                                } else {
                                    $multiSelectCount = @count($tempMultiselectValues);
                                    foreach ($tempMultiselectValues as $multiSelectValue) {
                                        if (isset($value['multiOptions'][$multiSelectValue]) && !empty($value['multiOptions'][$multiSelectValue])) {
                                            $counter++;
                                            if ($counter < $multiSelectCount)
                                                $fieldRowValue = $fieldRowValue . $value['multiOptions'][$multiSelectValue] . ", ";
                                            else if ($counter == $multiSelectCount)
                                                $fieldRowValue = $fieldRowValue . $value['multiOptions'][$multiSelectValue];
                                        }
                                    }
                                }
                                if (isset($fieldRowValue) && !empty($fieldRowValue))
                                    $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($fieldRowValue);
                            }
                            else {
                                $fieldValues[Engine_Api::_()->getApi('Core', 'siteapi')->translate($tempKey)] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($fieldRow->value);
                            }
                        }
                    }
                }
            }
        }
        return $fieldValues;
    }

    public function encloseInLink($subject, $field, $value, $label, $isRange = false) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $view = Zend_Registry::get('Zend_Translate');
        if ($field->display != 2 || $field->search < 1) {
            return $label;
        }

        // Get base url
        $url = $view->url(array(), 'user_general', true);
        $params = array();

        // Add parent field structure
        if ($field->search == 1 && $this->map) {
            // Add all parent options
            $parentMap = $this->map;
            do {
                $parentField = Engine_Api::_()->fields()->getFieldsMeta($subject)
                        ->getRowMatching('field_id', $parentMap->field_id);
                if ($parentField) {
                    $parentAlias = ( $parentField->alias ? $parentField->alias : sprintf('field_%d', $parentField->field_id) );
                    $params[$parentAlias] = $parentMap->option_id;
                    $parentMap = Engine_Api::_()->fields()->getFieldsMaps($subject)
                            ->getRowMatching('child_id', $parentField->field_id);
                }
            } while ($parentMap && $parentField);
        }

        // Add field
        $key = null;
        if ($this->map) {
            $key = $this->map->getKey() . '_';
        }

        $alias = $key . ( $field->alias ? 'alias_' . $field->alias : sprintf('field_%d', $field->field_id) );

        if (!$isRange) {
            $params[$alias] = $value;
        } else {
            $params[$alias]['min'] = $value;
            $params[$alias]['max'] = $value;
        }

        $url .= '?' . http_build_query($params);

        return $view->htmlLink($url, $label);
    }

    /**
     * Set the profile fields value to newly created user.
     * 
     * @return array
     */
    public function setProfileFields($user, $data) {
        // Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($user);

        $fVals = $data;
//        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();

        foreach ($fVals as $key => $value) {

            if (strstr($key, 'oauth'))
                continue;

            $parts = explode('_', $key);
            if (count($parts) < 3)
                continue;
            list($parent_id, $option_id, $field_id) = $parts;

            try {
                $fieldObj = $field = Engine_Api::_()->fields()->getField($field_id, 'user');
            } catch (Exception $ex) {
                //blank Exception
            }
            if ($fieldObj && $fieldObj->type == 'multi_checkbox' || $fieldObj->type == 'multiselect' || $fieldObj->type == strtolower('MultiCheckbox'))
                $valueparts = explode(',', $value);
            elseif (strstr($field->type, 'birthdate')) {
                $valueparts = date('m-d-Y', strtotime($value));
            } else
                $valueparts = $value;

            // Array mode
            if (is_array($valueparts) && count($valueparts) > 0) {

                // Lookup
                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $user->getIdentity()
                ));

                // Delete all
                $prevPrivacy = null;
                foreach ($valueRows as $valueRow) {
                    if (!empty($valueRow->privacy)) {
                        $prevPrivacy = $valueRow->privacy;
                    }
                    $valueRow->delete();
                }

                if ($field_id == 0)
                    continue;

                // Insert all
                $indexIndex = 0;
                if (is_array($valueparts) || !empty($valueparts)) {
                    foreach ((array) $valueparts as $singleValue) {
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $user->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $user->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

            // Scalar mode
            else {
                // Lookup
                $valueRow = $values->getRowMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $user->getIdentity(),
                    'index' => 0
                ));
                // Remove value row if empty
                if (empty($value)) {
                    if ($valueRow) {
                        $valueRow->delete();
                    }
                    continue;
                }

                if ($field_id == 0)
                    continue;

                // Create if missing
                $isNew = false;
                if (!$valueRow) {
                    $isNew = true;
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $user->getIdentity();
                }

                $valueRow->value = htmlspecialchars($value);
                $valueRow->save();
            }
        }
        return;
    }

    public function getProfileTypes($profileFields = array()) {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('user');
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

    public function getLanguages() {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();

        // Get the default local.
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = '';
            }
        }

        // Find out the local.
        $locale = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        try {
            $locale = Zend_Locale::findLocale($locale);
        } catch (Exception $e) {
            $locale = 'en_US';
        }
        $localeObject = new Zend_Locale($locale);

        $languageDataList = $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territoryDataList = $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        // Make the language array.
        $languageNameList = array();
        foreach ($languageList as $key) {
            $languageNameList[$key] = Zend_Locale::getTranslation($key, 'language', $key);

            if (empty($languageNameList[$key])) {
                list($locale, $territory) = explode('_', $key);
                $languageNameList[$key] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
            }
        }

        // Set default language at first place.
        $languageNameList = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $languageNameList);

        return $languageNameList;
    }

    public $_getTimeZone = array(
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
        'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow,Israel',
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
    );

    /**
     * Get the classified profile fields.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getContentProfileFields($itemType, $fieldsForm = array()) {
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        $getRowsMatching = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($itemType);
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();

            if ( !isset($meta->type))
                continue;
            $type = $meta->type;

            if ((!isset($meta->show) || empty($meta->show)) && (!isset($meta->search) || empty($meta->search)))
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
                    if (isset($meta->alias) && !empty($meta->alias))
                        $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                    else
                        $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                    $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                    $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                    // Add multiOption, If available.
                    if (!empty($getMultiOptions)) {
                        $fieldForm['multiOptions'] = $getMultiOptions;
                    }

                    // Add validator, If available.
                    if (isset($meta->required) && !empty($meta->required))
                        $fieldForm['hasValidator'] = true;

                    if (COUNT($this->_profileFieldsArray) > 1)
                        $fieldsForm[$option_id][] = $fieldForm;
                    else
                        $fieldsForm[] = $fieldForm;
                }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                    // Prepare Specific form.
                    $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                    if (isset($meta->alias) && !empty($meta->alias))
                        $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                    else
                        $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                    $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                    $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                    // Add multiOption, If available.
                    if (isset($getFormFieldTypeArray['multiOptions']) && !empty($getFormFieldTypeArray['multiOptions'])) {
                        $fieldForm['multiOptions'] = $getFormFieldTypeArray['multiOptions'];
                    }

                    // Add validator, If available.
                    if (isset($meta->required) && !empty($meta->required))
                        $fieldForm['hasValidator'] = true;

                    if (COUNT($this->_profileFieldsArray) > 1)
                        $fieldsForm[$option_id][] = $fieldForm;
                    else
                        $fieldsForm[] = $fieldForm;
                }
            }
        }

        return $fieldsForm;
    }

    /**
     * Validation: user signup field form
     * 
     * @return array
     */
    public function getContentFieldsFormValidations($itemType, $fieldArray = array()) {
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        $getRowsMatching = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps($itemType);

        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            $type = $meta->type;

            if (!empty($type) && ($type == 'heading'))
                continue;

            if (!isset($meta->show) || empty($meta->show))
                continue;

            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            // Bypass the profile field validation problem in Classified Plugin.
//            if ($itemType == 'classified')
//                $key = $key . '_alias_' . $meta->alias;

            if (isset($meta->alias) && !empty($meta->alias))
                $key = $key . '_alias_' . $meta->alias;
            else
                $key = $key . '_field_' . $meta->field_id;

            if (isset($meta->required) && !empty($meta->required))
                $fieldArray[$key] = array(
                    'required' => true,
                    'allowEmpty' => false
                );

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }

        return $fieldArray;
    }

    /**
     * Get the "User Loggedin" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getLoginForm($subject = null) {
        $accountForm = array();
        $enableOTP = $this->hasEnableLoginOtp();
        if ($enableOTP) {
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'email',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address or Mobile number'),
                'hasValidator' => true
            );
        } else {
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'email',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address'),
                'hasValidator' => true
            );
        }


        $accountForm[] = array(
            'type' => 'Password',
            'name' => 'password',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password'),
            'hasValidator' => true
        );
        $loginoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteotpverifier.allowoption', 'default');
        if ($loginoption == "both" && $enableOTP) {
            $accountForm[] = array(
                'type' => 'Submit',
                'name' => 'sendotp',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Signin with OTP')
            );
        }
        $accountForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sign In')
        );

        return $accountForm;
    }

    /**
     * User Settings: Get "Change Password" form.
     * 
     * @param object $user user object
     * @param object $viewer viewer object
     * @return array
     */
    public function getChangePasswordForm($user, $viewer = null) {
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;

        $changePasswordForm[] = array(
            'type' => 'Password',
            'name' => 'oldPassword',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Old Password'),
            'hasValidator' => true
        );

        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->seaocore()->checkVersion($coreversion, '5.4.0'))
            $description = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password must be at least 6 characters and contain one upper and one lower case letter, one number and one special character.');
        else
            $description = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Passwords must be at least 6 characters in length.');

        $changePasswordForm[] = array(
            'type' => 'Password',
            'name' => 'password',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New Password'),
            'description' => $description,
            'hasValidator' => true
        );


        $changePasswordForm[] = array(
            'type' => 'Password',
            'name' => 'passwordConfirm',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New Password (again)'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter your password again for confirmation.'),
            'hasValidator' => true
        );


        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->seaocore()->checkVersion($coreversion, '5.4.0'))
            $changePasswordForm[] = array(
                'type' => 'Checkbox',
                'name' => 'resetalldevice',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Do you want to Logout from all the devices')
            );

        $changePasswordForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Change Password')
        );

        return $changePasswordForm;
    }

    /**
     * User Settings: Get "Privacy" form.
     * 
     * @param object $user user object
     * @param object $viewer viewer object
     * @return array
     */
    public function getPrivacyForm($user, $viewer = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search')) {
            $privacyForm[] = array(
                'type' => 'Checkbox',
                'name' => 'search',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Do not display me in searches, browsing members, or the "Online Members" list.')
            );
        }

        $availableLabels = array(
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Me'),
            'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends'),
            'network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends & Networks'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
        );

        // Init profile view
        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        if (count($view_options) > 1) {
            $privacyForm[] = array(
                'type' => 'Radio',
                'name' => 'privacy',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your profile?'),
                'multiOptions' => $view_options,
            );
        }

        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->siteapi()->isCoreLatestVersion()) {

            // Init profile last login date
            $lastLoginDate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'lastLoginDate');
            $lastLoginDate_options = array_intersect_key($availableLabels, array_flip($lastLoginDate_options));

            if (count($lastLoginDate_options) > 1) {
                $privacyForm[] = array(
                    'type' => 'Radio',
                    'name' => 'lastLoginDate',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Last Login Date'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Last Login Date?'),
                    'multiOptions' => $lastLoginDate_options,
                );
            }

            // Init profile last update date
            if ($viewer->isAllowed('user', 'lastUpdateShow')) {
                $lastUpdateDate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'lastUpdateDate');
                $lastUpdateDate_options = array_intersect_key($availableLabels, array_flip($lastUpdateDate_options));

                if (count($lastUpdateDate_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'lastUpdateDate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Last Update Date'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Last Update Date?'),
                        'multiOptions' => $lastUpdateDate_options,
                    );
                }
            }

            // Init profile invitee
            if ($viewer->isAllowed('user', 'inviteeShow')) {
                $inviteeName_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'inviteeName');
                $inviteeName_options = array_intersect_key($availableLabels, array_flip($inviteeName_options));

                if (count($inviteeName_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'inviteeName',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Name of Invitee'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Invitee Name?'),
                        'multiOptions' => $inviteeName_options,
                    );
                }
            }

            // Init profile view
            if ($viewer->isAllowed('user', 'profileTypeShow')) {
                $profileType_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'profileType');
                $profileType_options = array_intersect_key($availableLabels, array_flip($profileType_options));

                if (count($profileType_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'profileType',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Type'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Profile Type?'),
                        'multiOptions' => $profileType_options,
                    );
                }
            }

            if ($viewer->isAllowed('user', 'memberLevelShow')) {
                // Init profile view
                $memberLevel_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'memberLevel');
                $memberLevel_options = array_intersect_key($availableLabels, array_flip($memberLevel_options));
                if (count($memberLevel_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'memberLevel',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Member Level'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Member Level?'),
                        'multiOptions' => $memberLevel_options,
                    );
                }

            }

            if ($viewer->isAllowed('user', 'profileViewsShow')) {
                // Init Member Level
                $profileViews_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'profileViews');
                $profileViews_options = array_intersect_key($availableLabels, array_flip($profileViews_options));

                if (count($profileViews_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'profileViews',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Views'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Profile Views?'),
                        'multiOptions' => $profileViews_options,
                    );
                }
            }

            if ($viewer->isAllowed('user', 'joinedDateShow')) {
                // Init Joined Date Level
                $joinedDate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'joinedDate');
                $joinedDate_options = array_intersect_key($availableLabels, array_flip($joinedDate_options));

                if (count($joinedDate_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'joinedDate',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Joined Date'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Joined Date?'),
                        'multiOptions' => $joinedDate_options,
                    );
                }
            }

            if ($viewer->isAllowed('user', 'friendsCountShow')) {
                // Init Friends Count Level
                $friendsCount_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'friendsCount');
                $friendsCount_options = array_intersect_key($availableLabels, array_flip($friendsCount_options));

                if (count($friendsCount_options) > 1) {
                    $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'friendsCount',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Count'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can view your Friends Count?'),
                        'multiOptions' => $friendsCount_options,
                    );
                }
            }
        }

        $availableLabelsComment = array(
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Me'),
            'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends'),
            'network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends & Networks'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
        );

        // Init profile comment
        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment');
        $comment_options = array_intersect_key($availableLabelsComment, array_flip($comment_options));

        if (count($comment_options) > 1) {
            $privacyForm[] = array(
                'type' => 'Radio',
                'name' => 'comment',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Posting Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can post on your profile?'),
                'multiOptions' => $comment_options,
            );
        }

        if ((_IOS_VERSION >= '3.0.0' || _ANDROID_VERSION >= '4.3.0') && Engine_Api::_()->siteapi()->isCoreLatestVersion()) {
            $availableOptions = array(
                'owner' => 'Only Me',
                'member' => 'My Friends',
                'network' => 'Friends & Networks',
                'owner_network' => 'Network',
                'registered' => 'All Registered Members',
            );
            $userMention_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'mention');
            $userMention_options = array_intersect_key($availableOptions, array_flip($userMention_options));

            if (count($userMention_options) > 1) {
                $privacyForm[] = array(
                    'type' => 'Radio',
                    'name' => 'mention',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('User @ Mentions'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who can @ mention you?'),
                    'multiOptions' => $userMention_options,
                );
            }

            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;

            if (Engine_Api::_()->seaocore()->checkVersion($coreversion, '5.4.0')) {
                $birthdayOptions = array(
                  'monthday' => 'Month/Day',
                  'monthdayyear' => 'Month/Day/Year',
                );
                $birthday_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'birthday_options');

                if ($viewer->isAllowed('user', 'allow_birthday') && count($birthday_options) > 1) {
                  $birthday_options = array_intersect_key($birthdayOptions, array_flip($birthday_options));
                  $privacyForm[] = array(
                        'type' => 'Radio',
                        'name' => 'birthday_format',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Birthday Privacy Setting'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('How to show your Birthday?'),
                        'multiOptions' => $birthday_options,
                    );
                }
            }
        }

        $actionTypes = Engine_Api::_()->getApi('Core', 'siteapi')->getEnabledActionTypesAssoc();
        unset($actionTypes['signup']);
        unset($actionTypes['postself']);
        unset($actionTypes['post']);
        unset($actionTypes['status']);
        unset($actionTypes['birthday_post']);

        //Remove some some key which transalation does not exist
        foreach (array_keys($actionTypes) as $key) {
            if (substr($key, 0, 5) == 'post_') {
                $defaultPublishTypes[] = $key;
                unset($actionTypes[$key]);
            }
        }

        $canDisable = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.publish', true);
        if ($canDisable) {
            $privacyForm[] = array(
                'type' => 'MultiCheckbox',
                'name' => 'publishTypes',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recent Activity Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Which of the following things do you want to have published about you in the recent activity feed? Note that changing this setting will only affect future news feed items.'),
                'multiOptions' => $actionTypes,
            );
        }
        $privacyForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Changes')
        );

        return $privacyForm;
    }

    /**
     * User Settings: Get "General" form.
     * 
     * @param object $user user object
     * @param object $viewer viewer object
     * @return array
     */
    public function getGeneralForm($user, $viewer = null, $facebookTwitterIntegrate) {
        $generalForm[] = array(
            'type' => 'Text',
            'name' => 'email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address'),
            'hasValidator' => true
        );

        if (Engine_Api::_()->authorization()->isAllowed('user', $user, 'username') || Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.username', 1) > 0) {
            $generalForm[] = array(
                'type' => 'Text',
                'name' => 'username',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Address'),
                'hasValidator' => true
            );
        }

//    $generalForm[] = array(
//        'type' => 'Select',
//        'name' => 'accountType',
//        'label' => 'Account Type'
//    );
// Init Facebook
        if (isset($facebookTwitterIntegrate) && !empty($facebookTwitterIntegrate) && _ANDROID_VERSION >= '1.8.5') {
            $facebook_enable = Engine_Api::_()->getApi('settings', 'core')
                    ->getSetting('core_facebook_enable', 'none');
            if ('none' != $facebook_enable) {
                $desc = 'Linking your Facebook account will let you login with Facebook';
                if ('publish' == $facebook_enable) {
                    $desc .= ' and publish content to your Facebook wall.';
                } else {
                    $desc .= '.';
                }
                $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                $info = $facebookTable->select()
                        ->from($facebookTable)
                        ->where('user_id = ?', $viewer->getIdentity())
                        ->query()
                        ->fetch();
                if (is_array($info) && !empty($info['facebook_uid']) && !empty($info['access_token']) && !empty($info['code'])) {
                    $generalForm[] = array(
                        'type' => 'Checkbox',
                        'name' => 'facebook_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Integrate with my Facebook'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Facebook Integration'),
                        'value' => 1
                    );
                } else {
                    $generalForm[] = array(
                        'type' => 'Dummy',
                        'name' => 'facebook',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Facebook Integration'),
                        'description' => $desc,
                    );
                }
            }

            // Init Twitter
            $twitter_enable = Engine_Api::_()->getApi('settings', 'core')
                    ->getSetting('core_twitter_enable', 'none');
            if ('none' != $twitter_enable) {
                $desc = 'Linking your Twitter account will let you login with Twitter';
                if ('publish' == $twitter_enable) {
                    $desc .= ' and publish content to your Twitter feed.';
                } else {
                    $desc .= '.';
                }
                $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                $twitter = $twitterTable->getApi();
                if ($twitter && $twitterTable->isConnected()) {
                    $generalForm[] = array(
                        'type' => 'Checkbox',
                        'name' => 'twitter_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Integrate with my Twitter'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Twitter Integration')
                    );
                } else {
                    $generalForm[] = array(
                        'type' => 'Dummy',
                        'name' => 'twitter',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Twitter Integration'),
                        'description' => $desc,
                    );
                }
            }
        }
        $generalForm[] = array(
            'type' => 'Select',
            'name' => 'timezone',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select the city closest to you that shares your same timezone.'),
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
                'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
                'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
                'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
                'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
                'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
                'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
                'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
                'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
            )
        );

        // Init default locale
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $locale = $viewer->locale;
        } else if (!empty($_COOKIE['en4_language']) && !empty($_COOKIE['en4_locale'])) {
            $locale = $_COOKIE['en4_locale'];
        } else if (!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $l = new Zend_Locale(Zend_Locale::BROWSER);
            $locale = $l->toString();
        } else {
            $locale = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        }

        // Make sure it's valid
        try {
            $locale = Zend_Locale::findLocale($locale);
        } catch (Exception $e) {
            $locale = 'en_US';
        }
        $locale = new Zend_Locale($locale);

        $localeMultiKeys = array_merge(
                array_keys(Zend_Locale::getLocaleList())
        );
        $localeMultiOptions = array();
        $languages = Zend_Locale::getTranslationList('language', $locale);
        $territories = Zend_Locale::getTranslationList('territory', $locale);
        foreach ($localeMultiKeys as $key) {
            if (!empty($languages[$key])) {
                $localeMultiOptions[$key] = $languages[$key];
            } else {
                $locale = new Zend_Locale($key);
                $region = $locale->getRegion();
                $language = $locale->getLanguage();
                if ((!empty($languages[$language]) && (!empty($territories[$region])))) {
                    $localeMultiOptions[$key] = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }
        }
        $localeMultiOptions = array_merge(array('auto' => '[Automatic]'), $localeMultiOptions);
        $generalForm[] = array(
            'type' => 'Select',
            'name' => 'locale',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Locale'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Dates, times, and other settings will be displayed using this locale setting.'),
            'multiOptions' => $localeMultiOptions
        );

        $generalForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Changes')
        );

        return $generalForm;
    }

    /**
     * User Settings: Get "User Friendship" form.
     * 
     * @param object $user user object
     * @param object $viewer viewer object
     * @return array
     */
    public function userFriendship($user, $viewer = null) {
        if (null === $viewer) {
            $viewer = Engine_Api::_()->user()->getViewer();
        }

        if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer)) {
            return '';
        }

        $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

        // Get data
        if (!$direction) {
            $row = $user->membership()->getRow($viewer);
        } else
            $row = $viewer->membership()->getRow($user);

        // Render
        // Check if friendship is allowed in the network
        $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
        if ($eligible == 0) {
            return '';
        }

        // check admin level setting if you can befriend people in your network
        else if ($eligible == 1) {

            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            $networkMembershipName = $networkMembershipTable->info('name');

            $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
            $select
                    ->from($networkMembershipName, 'user_id')
                    ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                    ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
                    ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
            ;

            $data = $select->query()->fetch();

            if (empty($data)) {
                return '';
            }
        }

        $response = array();
        if (!$direction) {
            // one-way mode
            if (null === $row) {
                if (_ANDROID_VERSION >= '1.8.6') {
                    $name = 'member_follow';
                } else {
                    $name = 'add_friend';
                }
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Follow'),
                    'name' => $name,
                    'url' => 'user/add',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            } else if ($row->resource_approved == 0) {
                if (_ANDROID_VERSION >= '1.8.6') {
                    $name = 'cancel_follow';
                } else {
                    $name = 'cancel_request';
                }
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cancel Follow'),
                    'name' => $name,
                    'url' => 'user/cancel',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            } else {
                if (_ANDROID_VERSION >= '1.8.6') {
                    $name = 'member_unfollow';
                } else {
                    $name = 'remove_friend';
                }
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unfollow'),
                    'name' => $name,
                    'url' => 'user/remove',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            }
        } else {
            // two-way mode
            if (null === $row) {
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Friend'),
                    'name' => 'add_friend',
                    'url' => 'user/add',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            } else if ($row->user_approved == 0) {
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cancel Request'),
                    'name' => 'cancel_request',
                    'url' => 'user/cancel',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            } else if ($row->resource_approved == 0) {
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Accept Request'),
                    'name' => 'accept_request',
                    'url' => 'user/confirm',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            } else if ($row->active) {
                return array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Remove Friend'),
                    'name' => 'remove_friend',
                    'url' => 'user/remove',
                    'urlParams' => array(
                        "user_id" => $user->user_id
                    )
                );
            }
        }

        return '';
    }

    /**
     * Gets the current viewer instance using the authentication storage
     *
     * @return User_Model_User
     */
    public function getViewer() {
        if (null === $this->_viewer) {
            //$authUser = $_SESSION['authUser']['user_id'];
            $identity = $this->getIdentity();
            $this->_viewer = $this->_getUser($identity);
        }

        return $this->_viewer;
    }

    /**
     * Gets the current viewer id using the authenticated token.
     *
     * @return User_Model_User
     */
    public function getIdentity() {
        $user_id = Engine_Api::_()->getApi('oauth', 'siteapi')->validateOauthToken();
        if (@is_numeric($user_id))
            return $user_id;
    }

    /**
     * User Settings: Authenticate user valid or not to login.
     * 
     * @param int $identity user id
     * @param array $identity user login credential
     * @return array
     */
    public function authenticate($identity, $credential) {
        $userTable = Engine_Api::_()->getItemTable('user');
        $userIdentity = $userTable->select()
                ->from($userTable, 'user_id')
                ->where('`email` = ?', $identity)
                ->limit(1)
                ->query()
                ->fetchColumn(0)
        ;

        $authAdapter = $this->getAuthAdapter()
                ->setIdentity($userIdentity)
                ->setCredential($credential);
        // Set up the authentication adapter
        $results = $authAdapter->authenticate();

        return $results;
    }

    /**
     * Set the authentication token
     *
     * @return token
     */
    public function setAuthToken() {
        if (null === $this->_auth) {
            $this->_auth = Zend_Auth::getInstance();
            if (_ENGINE_NO_AUTH && !$this->_auth->getIdentity()) {
                $this->_auth->getStorage()->write(1);
            }
        }
        return $this->_auth;
    }

    /**
     * Creat the authentication token
     *
     * @return token
     */
    public function createAuthToken($user) {
        $token = @md5($user->username . time() . rand(0, 999));
        return $token;
    }

    /**
     * Set the user photo in signup process
     *
     * @return object
     */
    public function setPhoto($photo, $subject, $needToUplode = false) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Group_Model_Exception('invalid argument passed to setPhoto');
        }
        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->getIdentity(),
            'name' => $name,
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->photo_id = $iMain->file_id;
        $subject->save();

        return $subject;
    }

    /**
     * Get the Friendship Status, respect of logged-in user.
     * 
     * @param object $user user object
     * @param object $viewer viewer object
     * @return string
     */
    public function getFriendshipType($user, $viewer = null) {
        if (null === $viewer) {
            $viewer = Engine_Api::_()->user()->getViewer();
        }

        if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer))
            return;

        $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

        // Get data
        if (!$direction)
            $row = $user->membership()->getRow($viewer);
        else
            $row = $viewer->membership()->getRow($user);

        // Check if friendship is allowed in the network
        $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
        if ($eligible == 0)
            return;

        $response = array();
        if (!$direction) {
            // one-way mode
            if (_ANDROID_VERSION >= '1.8.6') {
                if (null === $row) {
                    $status = 'member_follow';
                } else if ($row->resource_approved == 0) {
                    $status = 'cancel_follow';
                } else {
                    $status = 'member_unfollow';
                }
            } else {
                if (null === $row) {
                    $status = 'add_friend';
                } else if ($row->resource_approved == 0) {
                    $status = 'cancel_request';
                } else {
                    $status = 'remove_friend';
                }
            }
        } else {
            // two-way mode
            if (null === $row) {
                $status = 'add_friend';
            } else if ($row->user_approved == 0) {
                $status = 'cancel_request';
            } else if ($row->resource_approved == 0) {
                $status = 'accept_request';
            } else if ($row->active) {
                $status = 'remove_friend';
            }
        }

        return $status;
    }

    private function _getSignupPhotoForm($accountForm = array()) {
        $userSignupPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.photo', 1);
        $accountForm[] = array(
            'type' => 'File',
            'name' => 'photo',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Your Photo'),
            'hasValidator' => false
        );

        return $accountForm;
    }

    private function _getSignupAccountForm($accountForm = array()) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Element: email
        $accountForm[] = array(
            'type' => 'Text',
            'name' => 'email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Address'),
            'hasValidator' => true
        );

        // Element: code
        if ($settings->getSetting('user.signup.inviteonly') > 0) {
            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'code',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite Code'),
                'hasValidator' => true
            );
        }

        if ($settings->getSetting('user.signup.random', 0) == 0 && empty($_REQUEST['facebook_uid']) && empty($_REQUEST['twitter_uid']) && empty($_REQUEST['google_id']) && empty($_REQUEST['apple_id'])) {
            // Element: password
            $accountForm[] = array(
                'type' => 'Password',
                'name' => 'password',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password'),
                'hasValidator' => true
            );

            // Element: passconf
            $accountForm[] = array(
                'type' => 'Password',
                'name' => 'passconf',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Password Again'),
                'hasValidator' => true
            );
        }

        // Element: username
        if ($settings->getSetting('user.signup.username', 1) > 0) {
            $description = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Username must be all lowercase with one number no spaces allowed');

            $accountForm[] = array(
                'type' => 'Text',
                'name' => 'username',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Address'),
                'description' => $description,
                'hasValidator' => true
            );
        }

        // Element: profile_type
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;

            if (COUNT($profileFields) > 1) {
                $accountForm[] = array(
                    'type' => 'Select',
                    'name' => 'profile_type',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Profile Type'),
                    'multiOptions' => $profileFields,
                    'hasValidator' => true
                );
            }
        }

        // Element: timezone
        $timezone = $this->_getTimeZone;
        $accountForm[] = array(
            'type' => 'Select',
            'name' => 'timezone',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Timezone'),
            'multiOptions' => $timezone,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone'),
            'hasValidator' => true
        );

        // Element: language
        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();
        if (COUNT($languageList) > 1) {
            $accountForm[] = array(
                'type' => 'Select',
                'name' => 'language',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Language'),
                'multiOptions' => $this->getLanguages(),
                'hasValidator' => true
            );
        }
        
        // Element: terms
        if ($settings->getSetting('user.signup.terms', 1) == 1) {
            if(_CLIENT_TYPE && (_CLIENT_TYPE == 'ios')){
                $accountForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'terms',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('I have read and agree to the terms of service.'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('I have read and agree to the terms of service.'),
                    'hasValidator' => true
                );
            }


            if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'ios' && _IOS_VERSION > '1.5.5') || (_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.7.1'))) {
                $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseParentUrl = @trim($baseParentUrl, "/");
                $table = (_CLIENT_TYPE === 'ios') ? Engine_Api::_()->getDbtable('menus', 'siteiosapp') : Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
                $select = $table->select()
                        ->where('status = ?', 1)
                        ->where('name = ?', 'terms_of_service')
                        ->limit(1);

                $menu = $table->fetchRow($select);

                if (($menu->name == 'terms_of_service')) {
                    if (empty($menu->url))
                        $url = (!empty($baseParentUrl)) ? $getHost . DIRECTORY_SEPARATOR . $baseParentUrl . DIRECTORY_SEPARATOR . 'help/terms' : $getHost . DIRECTORY_SEPARATOR . 'help/terms';
                    else
                        $url = $menu->url;
                }

                if (isset($url) && !empty($url)) {
                    $accountForm[] = array(
                        'type' => 'Dummy',
                        'name' => 'terms_url',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Click here to read the terms of service.'),
                        'url' => $url
                    );
                }
            }
        }
        return $accountForm;
    }

    private function _getChooseSubscriptionForm() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $showForm = 0;

        $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
        $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');

        if($sitesubscriptionModuleEnable){
            $subscriptionClass = "Sitesubscription_Plugin_Signup_Subscription";
        }
        else{
            $subscriptionClass = "Payment_Plugin_Signup_Subscription";
        }
        $stepSelect = $stepTable->select()->where('class = ?', $subscriptionClass);
        $row = $stepTable->fetchRow($stepSelect);
        $enableSubscription = $row->enable;
        // Get available subscriptions
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $packagesSelect = $packagesTable
                ->select()
                ->from($packagesTable)
                ->where('enabled = ?', true)
                ->where('signup = ?', true);

        $multiOptions = array();
        $packagesObj = $packagesTable->fetchAll($packagesSelect);
        $package_count = count($packagesObj);
       
        if ((count($packagesObj) > 0) && isset($enableSubscription) && !empty($enableSubscription)) {
            $showForm = 1;
        } elseif ((Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0 && Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() > 0)) {
            $showForm = 1;
        }
        
        if($sitesubscriptionModuleEnable)
            $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();
        try {   //@todo getLocal in $package->getPackageDescription()
            foreach ($packagesObj as $package) {
                $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                if ($package_count == 1 && $package->isFree())
                    continue;
                $multiOptions[$package->package_id]['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                $multiOptions[$package->package_id]['description'] = (string) $package->description;

                if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                    $multiOptions[$package->package_id]['price'] = (double) $package->price;
                    $multiOptions[$package->package_id]['description'] = (string) $package->description;
                } else {
                    $multiOptions[$package->package_id]['price'] = (double) $package->price;
                }
                $multiOptions[$package->package_id]['currency'] = (string) $userCurrency;
                // trial fields work start
                $package_type = ($package->isFree()) ? 'free' : 'paid';
                $multiOptions[$package->package_id]['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                if($package_type != 'free'){
                    $multiOptions[$package->package_id]['recurrence'] = $package->recurrence ;
                    $multiOptions[$package->package_id]['recurrence_type'] = $package->recurrence_type ;
                }
                if($sitesubscriptionModuleEnable){
                    $multiOptions[$package->package_id]['trial_duration'] = $package->trial_duration ;
                    $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                    
                    foreach ($featuresRowData as $feature) {
                        $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                        foreach($valueRowData as $key1 => $value1){
                            if($value1['value'] == null)
                                continue;
                            if($value1['package_id'] == $package->package_id)
                            {   
                                $multiOptions[$package->package_id]['trialfields'][] = $value1['value'] ;
                            }
                        }
                    }
                }
                // trial fields work end 
            }
        } catch (Exception $e) {
        }

        // Element: package_id
        if (count($multiOptions) > 0 && isset($showForm) && !empty($showForm)) {
            $packageForm[] = array(
                'type' => 'Radio',
                'name' => 'package_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Plan'),
                'layoutType' => 2,
                'leftDrawable' => 'dollar',
                'multiOptions' => $multiOptions,
                'hasValidator' => 'true'
            );
        }
        return $packageForm;
    }

    /**
     * Get the "User Loggedin" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    private function _getUserProfileFields($fieldsForm = array(), $display = false) {
        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {
            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('user');
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

                    if (isset($display) && !empty($display) && $display) {
                        if (!isset($meta->display) || empty($meta->display))
                            continue;
                    }
                    else {
                        if (!isset($meta->show) || empty($meta->show))
                            continue;

                        if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                            continue;
                    }

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
                                    if (!isset($meta->required) || empty($meta->required)) {
                                        $getMultiOptions[''] = '';
                                    }
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            if ($fieldForm['type'] == 'Multiselect') {
                                $fieldForm['type'] = "MultiCheckbox";
                            }
                            $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getMultiOptions);
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (isset($fieldForm['name']) && !empty($fieldForm['name']) && strstr($fieldForm['name'], 'birthdate'))
                                $fieldForm['format'] = 'date';

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if ($fieldForm['type'] == 'Multi_checkbox') {
                                    $fieldForm['type'] = 'MultiCheckbox';
                                }
                                $fieldsForm[$option_id][Engine_Api::_()->getApi('Core', 'siteapi')->translate($getHeadingName)][] = $fieldForm;
                            } else {
                                if ($fieldForm['type'] == 'Multi_checkbox') {
                                    $fieldForm['type'] = 'MultiCheckbox';
                                }
                                $fieldsForm[Engine_Api::_()->getApi('Core', 'siteapi')->translate($getHeadingName)][] = $fieldForm;
                            }
                        } else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_alias_' . $meta->alias;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                if (!isset($meta->required) || empty($meta->required)) {
                                    $getMultiOptions[''] = '';
                                }
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = $option->label;
                                }
                                $fieldForm['multiOptions'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getMultiOptions);
                            }

                            if (isset($getFormFieldTypeArray['helper']) && !empty($getFormFieldTypeArray['helper']) && $getFormFieldTypeArray['helper'] == 'fieldOptions') {
                                $fieldForm['multiOptions'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getFormFieldTypeArray['multiOptions']);
                            }

                            if (isset($getFormFieldTypeArray['helper']) && !empty($getFormFieldTypeArray['helper']) && $getFormFieldTypeArray['helper'] == 'fieldCountry') {

                                Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
                                // $locale = Zend_Registry::get('Zend_Translate')->getLocale();       
                                $locale = Engine_Api::_()->getApi('Core', 'siteapi')->getLocal();
                                try {
                                    $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                                    asort($countries);
                                    $fieldForm['multiOptions'] = $countries;
                                } catch (Exception $ex) {
                                    
                                }
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (isset($fieldForm['name']) && !empty($fieldForm['name']) && strstr($fieldForm['name'], 'birthdate'))
                                $fieldForm['format'] = 'date';

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if ($fieldForm['type'] == 'Multi_checkbox') {
                                    $fieldForm['type'] = 'MultiCheckbox';
                                }
                                $fieldsForm[$option_id][Engine_Api::_()->getApi('Core', 'siteapi')->translate($getHeadingName)][] = $fieldForm;
                            } else {
                                if ($fieldForm['type'] == 'Multi_checkbox') {
                                    $fieldForm['type'] = 'MultiCheckbox';
                                }
                                $fieldsForm[Engine_Api::_()->getApi('Core', 'siteapi')->translate($getHeadingName)][] = $fieldForm;
                            }
                        }
                    }
                }
            }
        }

        return $fieldsForm;
    }

    public function subscriptionUpgradeForm() {
        $packageForm = array();
        $user = Engine_Api::_()->user()->getViewer();
        // Check if they are an admin or moderator (don't require subscriptions from them)
        $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
        if (in_array($level->type, array('admin', 'moderator'))) {
            return;
        }
        // Get packages
        $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
        $packages = $packagesTable->fetchAll(array('enabled = ?' => 1, 'after_signup = ?' => 1));

        // Get current subscription and package
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        $currentSubscription = $subscriptionsTable->fetchRow(array(
            'user_id = ?' => $user->getIdentity(),
            'active = ?' => true,
        ));

        if ($currentSubscription) {
            $currentPackage = $packagesTable->fetchRow(array(
                'package_id = ?' => $currentSubscription->package_id,
            ));
        }
        $sitesubscriptionModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitesubscription');
        if(!empty($sitesubscriptionModuleEnable))
            $templateInfo = Engine_Api::_()->getApi('core','sitesubscription')->getTemplateData();
        try {   //@todo getLocal in $package->getPackageDescription()
            foreach ($packages as $package) {
                if ($package->package_id != $currentPackage->package_id) {
                    $userCurrency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                    $pacakageDescription = ($package->isFree()) ? "(" . Engine_Api::_()->getApi('Core', 'siteapi')->translate("Free") . ")" : "";

                    $multiOptions[$package->package_id]['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($package->title) . $pacakageDescription;
                    $multiOptions[$package->package_id]['description'] = (string) $package->description;
                    $package_type = ($package->isFree()) ? 'free' : 'paid';
                    $multiOptions[$package->package_id]['type'] = !empty($sitesubscriptionModuleEnable) && !empty($package->trial_duration) ? 'trial' : $package_type;
                    if (isset($package->price) && !empty($package->price) && $package->price > 0) {
                        $multiOptions[$package->package_id]['price'] = (int) $package->price;
                        $multiOptions[$package->package_id]['currency'] = (string) $userCurrency;
                    }
                    
                    if($package_type != 'free'){
                        $multiOptions[$package->package_id]['recurrence'] = $package->recurrence ;
                        $multiOptions[$package->package_id]['recurrence_type'] = $package->recurrence_type ;
                    }

                    if($sitesubscriptionModuleEnable){
                        $multiOptions[$package->package_id]['trial_duration'] = $package->trial_duration ;
                        $featuresRowData = Engine_Api::_()->getDbTable('fields','sitesubscription')->getFields($templateInfo['structureType']);
                        foreach ($featuresRowData as $feature) {
                            $valueRowData = Engine_Api::_()->getDbTable('values','sitesubscription')->getFieldValues($feature['field_id']);
                            foreach($valueRowData as $key1 => $value1){
                                if($value1['value'] == null)
                                    continue;
                                if($value1['package_id'] == $package->package_id)
                                {   
                                    $multiOptions[$package->package_id]['trialfields'][] = $value1['value'] ;
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            
        }
        
        $packageForm[] = array(
            "type" => 'Radio',
            "name" => 'package_id',
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose Plan:'),
            'multiOptions' => $multiOptions
        );
        $packageForm[] = array(
            "type" => 'Submit',
            "name" => 'submit',
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Continue')
        );
        $response = array("subscription" => $packageForm,
            "currentSubscription" => $currentPackage->toArray()
        );
        return $response;
    }

    public function getVerifyInfo($subject) {
    // CHECK THAT USER IS VERIFIED OR NOT
    $verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($subject->getIdentity()); 
    $verify_limit = Engine_Api::_()->authorization()->getPermission($subject->level_id, 'siteverify', 'verify_limit');
    if ($verify_count >= $verify_limit) {
      return 1;
    }

        return 0;
    }

    private function _otpForm() {
        return array(
            "type" => 'Text',
            "name" => 'code',
            "description" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter the verification code received on your Mobile Phone.'),
            "label" => Engine_Api::_()->getApi('Core', 'siteapi')->translate('OTP Code'),
        );
    }

    public function hasEnableOtp() {
        $moduleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteotpverifier');
        //Engine_Api::_()->siteapi()->checkVersion()
        if (!empty($moduleEnable) && ((_IOS_VERSION && version_compare( _IOS_VERSION,'2.2.8')!=-1) || (_ANDROID_VERSION && version_compare(_ANDROID_VERSION,'3.1.7')!=-1 ))) {
            $enabledOTPClient = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();

            $settings = Engine_Api::_()->getApi('settings', 'core');
            $showphoneSignup = $settings->getSetting('siteotpverifier.singupUserPhone', 1);
            return $enabledOTPClient && !empty($showphoneSignup) && !empty($moduleEnable);
        } else
            return 0;
    }

    public function hasEnableLoginOtp() {
        $moduleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteotpverifier');
        if (!empty($moduleEnable) && ((_IOS_VERSION && version_compare( _IOS_VERSION,'2.2.8')!=-1) || (_ANDROID_VERSION && version_compare(_ANDROID_VERSION,'3.1.7')!=-1 ))) {
            $enabledOTPClient = Engine_Api::_()->getApi('core', 'siteotpverifier')->enabledOTPClient();

            return $enabledOTPClient && !empty($moduleEnable);
        } else
            return 0;
    }
    
    //Save Search data into Search Table...........
    public function setSerachVal($user,$data, $profileTypeField)
    {
        // save fields in search table
        if (isset($profileTypeField->field_id) && !empty($profileTypeField->field_id))
        {
            $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
            $searchTableName = $searchTable->info('name');
            $searchFields = $this->setFieldSearchStructure($data);
            if (isset($searchFields) && !empty($searchFields))
            {
                $searchFields['profile_type'] = $profileTypeField->field_id;
                $searchFields['item_id'] = $user->getIdentity();
                $selectQuery = $searchTable->select()
                        ->where('`item_id` = ?', $user->getIdentity());
                $result = $searchTable->fetchRow($selectQuery);
                
                if ($result == null)
                {
                    $userSearchFields = $searchTable->createRow();
                    $userSearchFields->setFromarray($searchFields);
                    $userSearchFields->save();
                }
                else
                {
                    $result->setFromarray($searchFields);
                    $result->save();
                }
            }
        }
    }
    
    public function setFieldSearchStructure($result) {
        foreach ($result as $key => $value) {
            if (strstr($key, '_first_name')) {
                $fieldStructure['first_name'] = $value;
            } else if (strstr($key, '_last_name')) {
                $fieldStructure['last_name'] = $value;
            } else if (strstr($key, '_gender')) {
                $fieldStructure['gender'] = $value;
            } else if (strstr($key, '_birthdate')) {
                $fieldStructure['birthdate'] = date('Y-m-d', strtotime($value));
            }
            else if (strstr($key, 'about_me')) {
                $fieldStructure['about_me'] = $value;
            }
            else if (strstr($key, 'website')) {
                $fieldStructure['website'] = $value;
            }
            else if (strstr($key, 'aim')) {
                $fieldStructure['aim'] = $value;
            }
            else if (strstr($key, 'city')) {
                $fieldStructure['city'] = $value;
            }
            else if (strstr($key, 'country')) {
                $fieldStructure['country'] = $value;
            }
            
            else if (strstr($key, 'zip_code')) {
                $fieldStructure['zip_code'] = $value;
            }
            
            else if (strstr($key, 'location')) {
                $fieldStructure['location'] = $value;
            }
            else if (strstr($key, 'interests')) {
                $fieldStructure['interests'] = $value;
            }
            else
            {
                $parts = explode('_', $key);
                if (count($parts) < 3)
                    continue;
                list($parent_id, $option_id, $field_id) = $parts;
                $fieldStructure['field_'.$field_id] = $value;
            }
        }

        return $fieldStructure;
    }


    /**
    * @getSubjectFieldsInfo method use to fetch profile fields of any type of subject.
    * @param $spec => Subject
    * @param $params => To specify the type fields format like with heading or without heading
    *                   i.e. for specific configuration, customize accordingly
    * @return array
    */
    public function getSubjectFieldsInfo(Core_Model_Item_Abstract $spec, $params = array()) {
        $structure = Engine_Api::_()->fields()->getFieldsStructurePartial($spec);
        $helper = new Siteapi_View_Helper_Fields_FieldValueLoop();
        $fieldsInfo = $helper->fieldValueLoop($spec, $structure, $params);
        return $fieldsInfo;
    }

    // Check if primemessenger is enabled, activated and allowed for the user
    public function getPrimemessengerTab($user = null) {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger')) {
            return 0;
        }

        $showPrimemessenger = Engine_Api::_()->primemessenger()->isPrimeMessengerActive();
        if (empty($showPrimemessenger)) {
            return 0;
        }

        // Check if client has member level settings and are enabled
        // Primemessenger Member level settings were added in version 4.9.4p9
        $pmModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('primemessenger');
        $hasMemberLevelSettings = Engine_Api::_()->siteapi()->checkVersion($pmModule->version, '4.9.4p9');
        if ($hasMemberLevelSettings) {
            if (empty($user)) {
                $user = Engine_Api::_()->user()->getViewer();
            }
            $isAllowed = Engine_Api::_()->authorization()->getPermission($user->level_id, 'primemessenger', 'enable');
            $showPrimemessenger = ($showPrimemessenger && $isAllowed) ? 1 : 0;
        }
        return $showPrimemessenger;
    }
}
