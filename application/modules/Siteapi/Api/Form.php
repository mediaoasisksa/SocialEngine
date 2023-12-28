<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Siteapi_Api_Form extends Core_Api_Abstract {

    public function getForm($form = null) {
        if (empty($form))
            return array();
        try {

            foreach ($form->getElements() as $element) {
                if (!method_exists($element, 'getType'))
                    continue;
                $type = $element->getType();
                $type = explode("_", $type);
                $label = $element->getLabel() ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($element->getLabel()) : Engine_Api::_()->getApi('Core', 'siteapi')->translate($element->getAttrib('placeholder'));
                if ($type[count($type) - 1] == 'Dummy' && empty($label)) {
                    $decorator = $element->getDecorators();
                    $ViewScript = $decorator['Zend_Form_Decorator_ViewScript'];
                    if (!empty($ViewScript)) {
                        $options = $ViewScript->getOptions();
                        $label = $options['heading'];
                    }
                }

                $AboutForm[] = array(
                    "name" => $element->getName(),
                    "type" => $type[count($type) - 1],
                    "label" => $label,
                    "hasValidator" => $element->isRequired() ? true : false
                );
            }
            return $AboutForm;
        } catch (Exception $ex) {
            return array();
        }
    }

}
?>

