<?php

class Siteapi_Api_Profilefields extends Core_Api_Abstract {

    public function setProfileFields($subject, $data) {
// Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);

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
                    'item_id' => $subject->getIdentity()
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
                        $valueRow->item_id = $subject->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $subject->getIdentity();
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
                        'item_id' => $subject->getIdentity()
                    ));
// Delete all
                    $prevPrivacy = null;
                    foreach ($valueRows as $valueRow) {
                        $valueRow->delete();
                    }

// Remove value row if empty
                    if (empty($value)) {
                        if ($valueRow) {
                            $valueRow->delete();
                        }
                        continue;
                    }

                    if ($field_id == 0)
                        continue;
// Lookup
                    $valueRow = $values->getRowMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity(),
                        'index' => 0
                    ));
// Create if missing
                    $isNew = false;
                    if (!$valueRow) {

                        $isNew = true;
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $subject->getIdentity();
                    }
                    $valueRow->value = htmlspecialchars($value);
                    $valueRow->save();
                } catch (Exception $ex) {
                    
                }
            }
        }

        return;
    }

}
?>