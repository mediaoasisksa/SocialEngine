<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FieldAbstract.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Api_Helper_Field_FieldAbstract extends Zend_View_Helper_Abstract {

    public function encloseInLink($subject, $field, $value, $label, $isRange = false) {
        if ($field->display != 2 || $field->search < 1) {
            return $label;
        }

        // Get base url
        $url = $this->view->url(array(), 'user_general', true);
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

        return $this->view->htmlLink($url, $label);
    }

}
