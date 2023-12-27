<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Aollogin.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Engine_Form_Element_DropZoneDrogAndDrop extends Zend_Form_Element_Hidden {

    public function getValue() {
        return explode(" ", trim($this->_value));
    }

    public function render(Zend_View_Interface $view = null) {
        if (null !== $view) {
            $this->setView($view);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }
        return $content;
    }

    /**
    * Load default decorators
    *
    * @return void
    */
    public function loadDefaultDecorators() {
        if( $this->loadDefaultDecoratorsIsDisabled() ) {
            return;
        }

        $decorators = $this->getDecorators();
        if( empty($decorators) ) {
            $this->addDecorator('FormDropZoneDrogAndDrop');
            Engine_Form::addDefaultDecorators($this);
        }
    }
}
