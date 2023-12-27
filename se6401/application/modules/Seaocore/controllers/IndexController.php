<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_IndexController extends Core_Controller_Action_Standard {

  public function indexAction()
  {
    
  }

    public function uploadcamimageAction() {
        $session = new Zend_Session_Namespace();

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/webcam';

        $profile_photo = $this->_getParam('profile_photo', 0);
        if (@is_dir($path)) {
            // Delete all before inserted files.
            $this->destroy($path);
        }

        // Create directory if not exist.
        if (!@is_dir($path) && !@mkdir($path, 0777, true)) {
            @mkdir(dirname($path));
            @chmod(dirname($path), 0777);
            @touch($path);
            @chmod($path, 0777);
        }

        $filename = date('YmdHis') . '.png';
        $result = file_put_contents('public/webcam/' . $filename, file_get_contents('php://input'));
        if (!$result) {
            print "ERROR: Failed to write data to $filename, check permissions\n";
            exit();
        }

        if (!$profile_photo) {
            $session->tem_file_name = $filename;
        } else {
            $session->tem_file_main_photo_name = $filename;
        }
    }

    private function destroy($dir) {
        $handle = opendir($dir);

        while (($file = readdir($handle)) !== false) {
            @chmod($dir . '/' . $file, 0777);
            @unlink($dir . '/' . $file);
        }

        closedir($handle);
        return;
    }

    public function upgradeSeaoPluginsAction() {
        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('is_seaocore_install', 1);
            $this->view->isPost = TRUE;
        }
    }

    //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
    public function viewMapAction() {

        $this->view->resouce_type = $resouce_type = $this->_getParam('resouce_type');
        $this->view->is_mobile = $is_mobile = $this->_getParam('is_mobile');

        if ($resouce_type == 'classified') {
            $table_option = Engine_Api::_()->fields()->getTable('classified', 'search');
            $table_option_name = $table_option->info('name');
            $select_options = $table_option->select()
                    ->from($table_option_name)
                    ->where($table_option_name . '.item_id =?', $this->_getParam('id'));
            $searchItem = $table_option->fetchRow($select_options);
            if (!empty($searchItem)) {
                $seLocationsTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
                $select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
                $results = $seLocationsTable->fetchRow($select);
                if (empty($results->location_id)) {
                    //Accrodeing to event  location entry in the seaocore location table.
                    if (!empty($searchItem->location)) {
                        $seaoLocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocationId($searchItem->location);
                        $select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
                        $item = $seLocationsTable->fetchRow($select);
                    }
                } else {
                    $select = $seLocationsTable->select()->where('location = ?', $searchItem->location);
                    $item = $seLocationsTable->fetchRow($select);
                }
            }
        }

        if (empty($is_mobile)) {
            $this->_helper->layout->setLayout('default-simple');
        }

        if (!$this->_getParam('id'))
            return $this->_forward('notfound', 'error', 'core');

        $userGeoSettings = '';
        switch ($resouce_type) {
            case 'sitepage_page' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitepage');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
                $id = 'page_id';
                break;
            case 'sitebusiness_business' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitebusiness');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.proximity.search.kilometer', 0);
                $id = 'business_id';
                break;
            case 'list_listing' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'list');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.proximity.search.kilometer', 0);
                $id = 'listing_id';
                break;
            case 'sitereview_listing' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitereview');
                $id = 'listing_id';
                $userGeoSettings = Engine_Api::_()->seaocore()->geoUserSettings('sitereview');
                break;
            case 'sitegroup_group' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
                $id = 'group_id';
                break;
            case 'sitestore_store' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitestore');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
                $id = 'store_id';
                break;
            case 'sitestoreproduct_product' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
                $id = 'product_id';
                break;
            case 'siteevent_event' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'siteevent');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
                $id = 'event_id';
                break;
            case 'recipe' :
                $dbtable = Engine_Api::_()->getDbtable('locations', 'recipe');
                $userGeoSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('recipe.proximity.search.kilometer', 0);
                $id = 'recipe_id';
                break;
            case 'seaocore' :
                $dbtable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
                $id = 'locationitem_id';
                $userGeoSettings = Engine_Api::_()->seaocore()->geoUserSettings('sitetagcheckin');
                break;
            case 'page_event':
                $dbtable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
                $id = 'locationitem_id';
                break;
            case 'event':
                $dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
                $id = 'location_id';
            case 'video':
                $dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
                $id = 'location_id';
            case 'group':
                $dbtable = Engine_Api::_()->getDbtable('locations', 'seaocore');
                $id = 'location_id';
                break;
            default:
                exit();
                break;
        }

        $this->view->userSettings = $userGeoSettings;
        $location_id = $this->_getParam('location_id');
        $flag = $this->_getParam('flag');

        if ($id) {
            if ($resouce_type != 'classified') {
                //if ($resouce_type == 'seaocore' || $resouce_type == 'event' || $resouce_type == 'page_event' || $resouce_type == 'recipe') {

                if (empty($location_id) && empty($flag)) {
                    $select = $dbtable->select()->where($dbtable->info('name') . ".$id =?", $this->_getParam('id'));
                } else {
                    $select = $dbtable->select()
                            ->where($dbtable->info('name') . ".$id =?", $this->_getParam('id'))
                            ->where($dbtable->info('name') . ".location_id =?", $location_id);
                }
                $item = $dbtable->fetchRow($select);
                // 			} else {
                // 				$item = $dbtable->getLocation($this->_getParam('id'));
                // 			}
            }
        }

        if (empty($item)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $params = (array) $item->toArray();
        if (is_array($params)) {
            $this->view->checkin = $params;
        } else {
            return $this->_forward('notfound', 'error', 'core');
        }
    }

    public function tagSuggestAction() {
        $tags = Engine_Api::_()->seaocore()->getTagsByText($this->_getParam('text'), $this->_getParam('limit', 40), $this->_getParam('resourceType'));
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($tags as $tag) {
                $data[] = $tag->text;
            }
        } else {
            foreach ($tags as $tag) {
                $data[] = array(
                    'id' => $tag->tag_id,
                    'label' => $tag->text
                );
            }
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    private function _doWeHaveOldDocument() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'document' => '4.8.12'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

//				$isModSupport = strcasecmp($getModVersion->version, $value);
                $running_version = $getModVersion->version;
                $product_version = $value;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }

                if (!empty($shouldUpgrade)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialApps.tech Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

}

?>
