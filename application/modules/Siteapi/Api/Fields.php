<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Fields.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Fields extends Core_Api_Abstract {

    protected $_topLevelId;
    protected $_topLevelValue;
    protected $_item;
    protected $_processedValues = array();

    /**
     * @var array An array of table objects
     */
    protected $_tables = array();

    public function __construct() {

        $this->setTopLevelValueId()
                ->setItem(Engine_Api::_()->user()->getUser(null));
    }

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function setTopLevelValueId() {
        // Preload profile type field stuff
        $profileTypeField = $this->getProfileTypeField();
        if ($profileTypeField) {
            $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
            $profileTypeValue = @$accountSession->data['profile_type'];
            if ($profileTypeValue) {

                $this->_topLevelId = $profileTypeField->field_id;
                $this->_topLevelValue = $profileTypeValue;
            } else {
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                    $options = $profileTypeField->getOptions();
                    if (count($options) == 1) {
                        $this->_topLevelId = $profileTypeField->field_id;
                        $this->_topLevelValue = $options[0]->option_id;
                    }
                }
            }
        }

        return $this;
    }

    public function getProfileTypeField() {
        $topStructure = Engine_Api::_()->getApi('core', 'fields')->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            return $topStructure[0]->getChild();
        }
        return null;
    }

    public function getFieldStructure() {
        // Let's allow fallback for no profile type (for now at least)
        if (!$this->_topLevelId || !$this->_topLevelValue) {
            $this->_topLevelId = null;
            $this->_topLevelValue = null;
        }
        return Engine_Api::_()->getApi('core', 'fields')->getFieldsStructureFull($this->getItem(), $this->_topLevelId, $this->_topLevelValue);
    }

    public function generate() {
        $struct = $this->getFieldStructure();

        $orderIndex = 0;
        //now we will create an array of field element to be build.
        $fieldElements = array();
        foreach ($struct as $fskey => $map) {
            $field = $map->getChild();

            // Skip fields hidden on signup
            if (isset($field->show) && !$field->show && $this->_isCreation) {
                continue;
            }

            // Add field and load options if necessary
            $params = $field->getElementParams($this->getItem());

            //$key = 'field_' . $field->field_id;
            $key = $map->getKey();

            // If value set in processed values, set in element
            if (!empty($this->_processedValues[$field->field_id])) {
                $params['options']['value'] = $this->_processedValues[$field->field_id];
            }

            if (!@is_array($params['options']['attribs'])) {
                $params['options']['attribs'] = array();
            }

            // Heading
            if ($params['type'] == 'Heading') {
                $params['options']['value'] = $params['options']['label'];
                unset($params['options']['label']);
            }

            // Order
            // @todo this might cause problems, however it will prevent multiple orders causing elements to not show up
            $params['options']['order'] = $orderIndex++;

            $inflectedType = Engine_Api::_()->fields()->inflectFieldType($params['type']);
            unset($params['options']['alias']);
            unset($params['options']['publish']);

            //
            // $this->addElement($inflectedType, $key, $params['options']);
            //$element = $this->getElement($key);
//      if( method_exists($element, 'setFieldMeta') ) {
//        $element->setFieldMeta($field);
//      }
            // Set attributes for hiding/showing fields using javscript
            $classes = 'field_container field_' . $map->child_id . ' option_' . $map->option_id . ' parent_' . $map->field_id;
            //$element->setAttrib('class', $classes);
            $fieldElements[$key] = array('type' => $inflectedType, 'options' => $params['options'], 'attribs' => array('class' => $classes));
            //
            if ($field->canHaveDependents()) {
                //$element->setAttrib('onchange', 'changeFields(this)');
                $fieldElements[$key]['attribs']['onchange'] = 'changeFields(this)';
            }

            // Set custom error message
//      if( $field->error ) {
//        $element->addErrorMessage($field->error);
//      }
        }

        return $fieldElements;
    }

    public function isValid($data) {
        try {
            if (!is_array($data)) {
                require_once 'Zend/Form/Exception.php';
                throw new Zend_Form_Exception(__CLASS__ . '::' . __METHOD__ . ' expects an array');
            }
            //$translator = $this->getTranslator();
            $valid = true;

//    if ($this->isArray()) {
//      $data = $this->_dissolveArrayValue($data, $this->getElementsBelongTo());
//    }
            // Changing this part
            $structure = $this->getFieldStructure();
            $selected = array();
            if (!empty($this->_topLevelId))
                $selected[$this->_topLevelId] = $this->_topLevelValue;
            $coreApi = Engine_Api::_()->getApi('Validators', 'siteapi');
            $validators = array();
            //CHECK IF A VALIDATOR IS SET FOR THAT PARTICULA ELEMENT.
            if (isset($data['validators']))
                $validators = $data['validators'];
            unset($data['validators']);

            foreach ($data as $key => $value) {

                //$element->setTranslator($translator);
                if (!isset($validators[$key]))
                    continue;
                $parts = explode('_', $key);
                if (count($parts) !== 3) {
                    continue;
                }

                list($parent_id, $option_id, $field_id) = $parts;
                //if( !is_numeric($field_id) ) continue;
                if (!isset($structure[$key])) {
                    return;
                }
                $fieldObject = $structure[$key];
                $field = $fieldObject->getChild();

                // All top level fields are always shown
                if (!empty($parent_id)) {

                    $parent_field_id = $parent_id;
                    $option_id = $option_id;


                    $element = new Zend_Form_Element($key, $validators[$key]);

                    //ADD ERROR MESSAGES IF THERE IS A CUSTOM ERROR MESSAGE.
                    if ($field->error) {
                        $coreApi->setMessage($key, $field->error);
                    }
                    // Field has already been stored, or parent does not have option
                    // specified, <del>or field is a heading</del>
                    if (isset($selected[$field_id]) || empty($selected[$parent_field_id]) /* || !isset($data[$key]) */) {
                        $element->setIgnore(true);
                        continue;
                    }

                    // Parent option doesn't match
                    if (is_scalar($selected[$parent_field_id]) && $selected[$parent_field_id] != $option_id) {
                        $element->setIgnore(true);
                        continue;
                    } else if (is_array($selected[$parent_field_id]) && !in_array($option_id, $selected[$parent_field_id])) {
                        $element->setIgnore(true);
                        continue;
                    }
                }

                // This field is being used
                if (isset($data[$key])) {
                    $selected[$field_id] = $data[$key];
                }

                if ($element instanceof Engine_Form_Element_Heading) {
                    $element->setIgnore(true);
                } else if (!isset($data[$key])) {
                    $valid = $element->isValid(null, $data) && $valid;
                } else {
                    $isValidElement = $element->isValid($data[$key]);
                    if (!$isValidElement) {
                        self::$_messages[$key] = $element->getMessages();
                    }
                    $valid = $isValidElement && $valid;
                }
            }

            $this->_processedValues = $selected;
            // Done changing
//    foreach ($this->getSubForms() as $key => $form) {
//      $form->setTranslator($translator);
//      if (isset($data[$key])) {
//        $valid = $form->isValid($data[$key]) && $valid;
//      } else {
//        $valid = $form->isValid($data) && $valid;
//      }
//    }

            $this->_errorsExist = !$valid;
            return $valid;
        } catch (Exception $e) {
            
        }
    }

    public function onSubmit(Zend_Controller_Request_Abstract $request, Core_Plugin_FormSequence_Abstract $plugin) {
        // Form was valid
        $post = $request->getPost();
        //NOW ADD THE VALIDATORS TO THE FORM ELEMENTS
        //NOW ADD THE VALIDATORS FOR THIS CURRENT STEP.
        $validators = Engine_Api::_()->getApi('Api_FormValidators', 'user')->getSignupFieldsValidators();

        $post['validators'] = $validators;
        if ($this->isValid($post)) {
            $plugin->getSession()->data = $this->getProcessedValues();
            $plugin->getSession()->active = false;
            $plugin->onSubmitIsValid();
            return true;
        }

        // Form was not valid
        else {
            $plugin->getSession()->active = true;
            $plugin->onSubmitNotIsValid();
            return false;
        }
    }

    public function getProcessedValues() {
        return $this->_processedValues;
    }

    /**
     * Simply returns the passed type, or the type of the item if an item
     *
     * @param Core_Model_Item_Abstract|string $type
     * @return string
     * @throws Fields_Model_Exception If the first argument is neither a string
     *   nor an instance of Core_Model_Item_Abstract
     */
    public function getFieldType($dat, $throw = true) {
        if ($dat instanceof Core_Model_Item_Abstract) {
            return $dat->getType();
        } else if ($dat instanceof Fields_Model_Abstract) {
            return $dat->getFieldType();
        } else if (is_string($dat)) {
            return $dat;
        } else {
            if ($throw) {
                throw new Fields_Model_Exception("Unable to get field type");
            } else {
                return null;
            }
        }
    }

    /**
     * Gets a typed table class
     *
     * @param string $type The item type, i.e. user
     * @param string $name The name of the table, i.e. fields, map, options, values
     * @return Engine_Db_Table
     */
    public function getTable($type, $name) {
        $type = $this->getFieldType($type);

        if (!isset($this->_tables[$type][$name])) {
            $this->_tables[$type][$name] = Fields_Model_DbTable_Abstract::factory($type, $name);
        }

        return $this->_tables[$type][$name];
    }

    /**
     * Gets the rowset of field-option mapping for the specified field system class
     *
     * @param Core_Model_Item_Abstract|string $spec The field system class
     * @return Engine_Db_Table_Rowset
     */
    public function getFieldsMaps($type) {
        return $this->getTable($this->getFieldType($type), 'maps')->getMaps();
    }

    /**
     * Gets the rowset of field metadata for the specified field system class
     *
     * @param Core_Model_Item_Abstract|string $spec The field system class
     * @return Engine_Db_Table_Rowset
     */
    public function getFieldsMeta($type) {
        return $this->getTable($this->getFieldType($type), 'meta')->getMeta();
    }

    public function getFirstnameFieldsMeta($type) {
        return $this->getTable($this->getFieldType($type), 'meta')->getMeta();
    }

    /**
     * Generates a flattened array structure of only the fields that apply to the
     * specified item based on it's current values
     *
     * @param Core_Model_Item_Abstract $spec The item to use for generation
     * @param int $parent The field id to start with
     * @return array
     */
    public function getFieldsStructurePartial($spec, $parent_field_id = null) {
        // Spec must be a item for this one
        if (!($spec instanceof Core_Model_Item_Abstract)) {
            throw new Fields_Model_Exception("First argument of getFieldsValues must be an instance of Core_Model_Item_Abstract");
        }

        $type = $this->getFieldType($spec);
        $parentMeta = null;
        $parentValue = null;

        // Get current field values
        if ($parent_field_id) {
            $parentMeta = $this->getFieldsMeta($type)->getRowMatching('field_id', $parent_field_id);
            $parentValueObject = $parentMeta->getValue($spec);
            if (is_array($parentValueObject)) {
                $parentValue = array();
                foreach ($parentValueObject as $parentValueObjectSingle) {
                    $parentValue[] = $parentValueObjectSingle->value;
                }
            } else if (is_object($parentValueObject)) {
                $parentValue = $parentValueObject->value;
            }
        }

        // Build structure
        $structure = array();
        foreach ($this->getFieldsMaps($spec)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
            // Parent value does not match id
            if ($parent_field_id) {
                if (!is_object($parentMeta)) {
                    continue;
                } else if (is_array($parentValue) && !in_array($map->option_id, $parentValue)) {
                    continue;
                } else if (null !== $parentValue && is_scalar($parentValue) && $parentValue != $map->option_id) {
                    continue;
                }
            }
            // Get child field
            $field = $this->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
            if (empty($field)) {
                continue;
            }
            // Add to structure
            $structure[$map->getKey()] = $map;
            // Get dependents
            if ($field->canHaveDependents()) {
                $structure += $this->getFieldsStructurePartial($spec, $field->field_id);
            }
        }

        return $structure;
    }

    public function getProfileTypes($option_id = null, $getFieldId = null) {
        // Set data
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('user');
        $metaData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMeta('user');
        $optionsData = Engine_Api::_()->getApi('core', 'fields')->getFieldsOptions('user');
        // Get top level fields
        $topLevelMaps = $mapData->getRowsMatching(array('field_id' => 0, 'option_id' => 0));
        $topLevelFields = array();
        foreach ($topLevelMaps as $map) {
            $field = $map->getChild();
            $topLevelFields[$field->field_id] = $field;
        }

        // Get top level field
        // Only allow one top level field
        if (count($topLevelFields) > 1) {
            throw new Engine_Exception('Only one top level field is currently allowed');
        }
        $topLevelField = array_shift($topLevelFields);
        // Only allow the "profile_type" field to be a top level field (for now)
        if ($topLevelField->type !== 'profile_type') {
            throw new Engine_Exception('Only profile_type can be a top level field');
        }

        // Get top level options
        $topLevelOptions = array();
        foreach ($optionsData->getRowsMatching('field_id', $topLevelField->field_id) as $option) {
            $topLevelOptions[$option->option_id] = $option->label;
        }

        if (empty($option_id)) {
            return $topLevelOptions;
        }

        if (empty($option_id) || empty($topLevelOptions[$option_id])) {
            $option_id = current(array_keys($topLevelOptions));
        }
        $topLevelOption = $optionsData->getRowMatching('option_id', $option_id);
        if (!$topLevelOption) {
            throw new Engine_Exception('Missing option');
        }

        // Get second level fields
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if (!empty($option_id)) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if (!empty($secondLevelMaps)) {
                foreach ($secondLevelMaps as $map) {
                    $meta = $map->getChild();
                    $key = $map->getKey();
                    if ($meta->type == 'heading') {
                        continue;
                    }
                    $profileFields [$key] = $meta->label;
                }
            }
        }
        return $profileFields;
    }

    public function getMappedLabels($profileTypes) {
        // Get mapped field label
        foreach ($profileTypes as $key => $label) {
            // If profile type aleady mapped
            $value = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteapi_contact_profile_" . $key);
            if (isset($value) && !empty($value)) {
                $secondLevelFields = Engine_Api::_()->getApi('fields', 'siteapi')->getProfileTypes($key);
                if (isset($secondLevelFields[$value]) && !empty($secondLevelFields[$value])) {
                    $mappedFieldLabels[$key] = $secondLevelFields[$value];
                }
            }
        }
        return $mappedFieldLabels;
    }

}
