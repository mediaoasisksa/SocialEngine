<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FormSequenceApi.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_Controller_Action_Helper_FormSequenceApi extends Zend_Controller_Action_Helper_Abstract {

    protected $_plugins = array();
    protected $_order = array();
    protected $_needsSort = false;
    protected $_completeAction = array();
    protected $_currentAction = '';
    protected $_currentActionClass = '';
    protected $_registry;
    protected $_isValid = true;
    protected $isValidss = 1;

    public function direct() {
        // If not posting, reset all
        if (!$this->getActionController()->getRequest()->isPost()) {
            $this->resetAll();
        }
        // $this->resetAll();
        // Call init
        $this->initAll();
        // Process
        $this->doSubmit();
        $this->doView();
        //return false;
        return $this->doProcess();
    }

    public function getRegistry() {
        if (null === $this->_registry) {
            $this->_registry = new stdClass();
        }

        return $this->_registry;
    }

    public function setPlugin(Core_Plugin_FormSequence_Interface $plugin, $order = 100) {
        $class = get_class($plugin);
        $this->_plugins[$class] = $plugin;
        $this->_order[$class] = $order;
        $this->_needsSort = true;
        $plugin->setRegistry($this->getRegistry());
        return $this;
    }

    public function getPlugin($class) {
        return @$this->_plugins[$class];
    }

    public function getPlugins() {
        $this->_sortPlugins();
        return $this->_plugins;
    }

    public function setPluginOrder($class, $order = 100) {
        if (isset($this->_plugins[$class])) {
            $this->_order[$class] = $order;
            $this->_needsSort = true;
        }
        return $this;
    }

    public function clearPlugins() {
        $this->_plugins = array();
        $this->_order = array();
        $this->_needsSort = false;
        return $this;
    }

    protected function _sortPlugins() {
        if ($this->_needsSort) {
            $this->_needsSort = false;
            asort($this->_order);

            // Experimental
            $plugins = array();
            foreach ($this->_order as $class => $order) {
                $plugins[$class] = $this->_plugins[$class];
            }
            $this->_plugins = $plugins;
        }
    }

    public function initAll() {
        foreach ($this->getPlugins() as $plugin) {
            if (method_exists($plugin, 'init')) {
                $plugin->init();
            }
        }

        return $this;
    }

    public function resetAll() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->resetSession();
        }

        return $this;
    }

    // Processing

    public function doSubmit() {
        if ($this->getActionController()->getRequest()->isPost()) {
            foreach ($this->getPlugins() as $plugin) {
                if ($plugin->isActive()) {
                    //$PLUGIN HAS SUBMIT METHOD THEN CALL TO THAT METHOD.
                    $classNameArray = explode("_", get_class($plugin));
                    $pluginApiClassName = 'Siteapi_Api_' . $classNameArray[3];
                    $data = $this->getActionController()->getRequest()->getPost();
                    if (class_exists($pluginApiClassName)) {
                        $class = new $pluginApiClassName();
                        if (method_exists($class, 'onSubmit')) {
                            $return = $class->onSubmit($this->getActionController()->getRequest(), $plugin);
                            $this->_isValid = $return;
                            return $return;
                        }
                    }

                    $this->onSubmit($this->getActionController()->getRequest(), $plugin);
                    return $plugin;
                }
            }
        } else {
            
        }

        return false;
    }

    //plugin onsubmit//
    public function onSubmit(Zend_Controller_Request_Abstract $request, Core_Plugin_FormSequence_Abstract $plugin) {
        $post = $request->getPost();

        //NOW ADD THE VALIDATORS FOR THIS CURRENT STEP.
        $getFormValidators = 'getSignup' . str_replace("User_Plugin_Signup_", "", get_class($plugin)) . 'Validators';

        $validators = Engine_Api::_()->getApi('Api_FormValidators', 'user')->$getFormValidators();

        $post['validators'] = $validators;
        //calling plugin onsubmit, we will execute the code here itself.
        //FIRST CHECK THE VALIDITY OF FORM VALUES.

        $valid = Engine_Api::_()->getApi('Validators', 'siteapi')->checkFormValidator($post);
        if ($valid) {
            $post = $request->getPost();
            if (isset($post['validators']))
                unset($post['validators']);
            $plugin->getSession()->data = $post;
            $plugin->setActive(false);
            $plugin->onSubmitIsValid();
            $this->_currentActionClass = get_class($plugin);
            $this->_isValid = true;
            return true;
        }

        // Form was not valid
        else {
            $plugin->getSession()->active = true;
            $plugin->onSubmitNotIsValid();
            $this->_isValid = false;
            return false;
        }
    }

    public function doView() {

        foreach ($this->getPlugins() as $plugin) {
            if ($plugin->isActive()) {
                $fieldElements = array();
                $errorMessages = array();
                if (!$this->getActionController()->getRequest()->isPost() || $this->_isValid == true) {
                    $fieldElements = Engine_Api::_()->getApi('signup', 'Siteapi')->getFormElements(get_class($plugin));
                    $success = true;
                } else {
                    if ($this->_isValid == false) {
                        $success = false;
                        $errorMessages = Engine_Api::_()->getApi('Validators', 'siteapi')->getMessages();
                    }
                }
                $this->getActionController()->view->success = $success;
                $this->getActionController()->view->fieldElements = $fieldElements;
                $this->getActionController()->view->errorMessages = $errorMessages;
                break;
            }
        }

        return false;
    }

    public function doProcess() {
        // Check if we are all done
        $done = true;
        foreach ($this->getPlugins() as $plugin) {
            if ($plugin->isActive()) {
                $done = false;
            }
        }

        // Process
        if ($done) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                foreach ($this->getPlugins() as $plugin) {
                    $plugin->onProcess();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // Remove session data
            foreach ($this->getPlugins() as $plugin) {
                $plugin->getSession()->unsetAll();
            }
        }

        return $done;
    }

    protected function _forward($action, $controller = null, $module = null, array $params = null) {
        $request = $this->getActionController->getRequest();

        if (null !== $params) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (null !== $module) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
                ->setDispatched(false);
    }

}
