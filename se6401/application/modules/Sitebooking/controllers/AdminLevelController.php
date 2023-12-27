<!-- Sitebooking_Form_Admin_Settings_Level -->
<?php

class Sitebooking_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitebooking_admin_main', array(), 'sitebooking_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Sitebooking_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');


    //Populating process
    $populateFormValue = $form->getValues();
    $servicePopulateValues = $providerPopulateValues = $populateValues = array();

    foreach ($populateFormValue as $key => $value) {
      $str_arr = explode ("_", $key);
      $type = $str_arr[0].'_'.$str_arr[1];
      if($type === 'sitebooking_pro') {
        if(!empty($str_arr[3])){
          $providerPopulateValues[$str_arr[2].'_'.$str_arr[3]] = $value;}
        else{
          $providerPopulateValues[$str_arr[2]] = $value; }
      }elseif($type === 'sitebooking_ser') {
        if(!empty($str_arr[3]))
          $servicePopulateValues[$str_arr[2].'_'.$str_arr[3]] = $value;
        else
          $servicePopulateValues[$str_arr[2]] = $value;
      }
      
    }
    $providerPopulateValues = $permissionsTable->getAllowed('sitebooking_pro', $id, array_keys($providerPopulateValues));
    foreach ($providerPopulateValues as $key => $value) {
      $populateValues['sitebooking_pro_'.$key] = $value;
    }
    $servicePopulateValues = $permissionsTable->getAllowed('sitebooking_ser', $id, array_keys($servicePopulateValues));
    foreach ($servicePopulateValues as $key => $value) {
      $populateValues['sitebooking_ser_'.$key] = $value;
    }

    $form->populate($populateValues);

    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $formValues = $form->getValues();
    $serviceValues = $providerValues= array();
    foreach ($formValues as $key => $value) {

      $str_arr = explode ("_", $key);
      $type = $str_arr[0].'_'.$str_arr[1];
      if($type === 'sitebooking_pro') {
        if(!empty($str_arr[3]))
          $providerValues[$str_arr[2].'_'.$str_arr[3]] = $value;
        else
          $providerValues[$str_arr[2]] = $value;
      }elseif($type === 'sitebooking_ser') {
        if(!empty($str_arr[3]))
          $serviceValues[$str_arr[2].'_'.$str_arr[3]] = $value;
        else
          $serviceValues[$str_arr[2]] = $value; 
      }
    
    }
    
    $nonBooleanSettings = $form->nonBooleanFields();


    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try
    {
      // Set permissions
      $permissionsTable->setAllowed('sitebooking_pro', $id, $providerValues, '', $nonBooleanSettings);
      $permissionsTable->setAllowed('sitebooking_ser', $id, $serviceValues, '', $nonBooleanSettings);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }
}

?>