<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminApiCacheController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteapi_AdminApiCacheController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteapi_admin_main', array(), 'siteapi_admin_api_caching');
        
        $settingFile = APPLICATION_PATH . '/application/settings/restapi_cache.php';
        $defaultFilePath = APPLICATION_PATH . '/temporary/restapicache';

        if (file_exists($settingFile)) {
            $currentCache = include $settingFile;
        } else {
            $currentCache = array(
                'default_backend' => 'File',
                'frontend' => array(
                        'automatic_serialization' => true,
                        'cache_id_prefix' => 'Engine4_restapi',
                        'lifetime' => '300',
                        'caching' => true,
                        'status'=>true,
                        'gzip' => true,
                ),
                'backend' => array(
                    'File' => array(
                        'cache_dir' => APPLICATION_PATH . '/temporary/restapicache',
                    ),
                ),
            );
        }
        $currentCache['default_file_path'] = $defaultFilePath;
        
        $this->view->form = $form = new Siteapi_Form_Admin_ApiCaching_Create();
         // pre-fill form with proper cache type
        $form->populate($currentCache);
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            try {
                //include_once APPLICATION_PATH . '/application/modules/Siteapi/controllers/license/license2.php';
                 if (is_writable($settingFile) || (is_writable(dirname($settingFile)) && !file_exists($settingFile))) {
            // do nothing
        } else {
            $phrase = Zend_Registry::get('Zend_Translate')->_('Changes made to this form will not be saved.  Please adjust the permissions (CHMOD) of file %s to 777 and try again.');
            $form->addError(sprintf($phrase, '/application/settings/restapi_cache.php'));
            return;
        }  
                $this->view->form = $form = new Siteapi_Form_Admin_ApiCaching_Create();
                $form->addNotice("Your changes have been saved.");
            } catch (Exception $e) {
                throw $e;
            }
            
            if ($form->isValid($this->getRequest()->getPost())) {
            $code = "<?php\ndefined('_ENGINE') or die('Access Denied');\nreturn ";
            $options=array();
            $options['file_locking']=true;
            $options['cache_dir']=APPLICATION_PATH . '/temporary/restapicache';
            $currentCache['backend'] = array('File' => $options);
            $currentCache['frontend']['lifetime'] = $this->_getParam('siteapi_caching_lifetime');
            $currentCache['frontend']['caching'] = (bool) $this->_getParam('siteapi_caching_status');
            $currentCache['frontend']['gzip'] = true;
            $currentCache['frontend']['status'] = (bool) $this->_getParam('siteapi_lifetime_status');

            $code .= var_export($currentCache, true);
            $code .= '; ?>';
            
            // test write+read before saving to file
            $backend = null;
            if (!$currentCache['frontend']['caching']) {
                $this->view->success = true;
            } else {
                $backend = Zend_Cache::_makeBackend('File', $options, false);
                if ($currentCache['frontend']['caching'] && @$backend->save('test_value', 'test_id') && @$backend->test('test_id')) {
                    $this->view->success = true;
                } else {
                    $this->view->success = false;
                    $form->getElement('type')->setErrors(array('Unable to use this backend.  Please check your settings or try another one.'));
                }
            }
            
            if ($this->view->success && file_put_contents($settingFile, $code)) {
                $form->addNotice('Your changes have been saved.');
            } elseif ($this->view->success) {
                $form->addError('Your settings were unable to be saved to the
          cache file.  Please log in through FTP and either CHMOD 777 the file
          <em>/application/settings/cache.php</em>, or edit that file and
          replace the existing code with the following:<br/>
          <code>' . htmlspecialchars($code) . '</code>');
            }
            
            if ($backend instanceof Zend_Cache_Backend && $form->getElement('flush')->getValue()) {
                $backend->clean();
                $form->getElement('flush')->setValue(0);
                $form->addNotice('Cache has been flushed.');
            }
            
            }
        }
    }

}
 