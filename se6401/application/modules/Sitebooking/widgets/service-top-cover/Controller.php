<?php

class Sitebooking_Widget_ServiceTopCoverController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
    {
      $this->view->category_name = '';
      $this->view->first_level_category_name = '';
      $this->view->second_level_category_name = '';
      $this->view->flag = '0';
      $this->view->reviewHide = '0';

      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->viewer_id = $values['user_id'] = $viewer_id = $viewer->getIdentity();

      // HAS SUBJECT
      if( !Engine_Api::_()->core()->hasSubject('sitebooking_ser') ) 
        return $this->setNoRender();

      // GET SUBJECT
      $this->view->sitebooking = $sitebooking = Engine_Api::_()->core()->getSubject();

      $review_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.serviceReview');

      if($review_settings == "service_none" || $review_settings == "service_onlyRating") {
        $this->view->reviewHide = '1';
      }

      if($review_settings == "service_Rating&Review")
        $this->view->reviewHide = '0';

      $resource_type = $sitebooking->getType();

      $resource_id = $sitebooking->getIdentity();

      $this->view->item = $item = Engine_Api::_()->getItem($resource_type, $resource_id);


      $serviceTable = Engine_Api::_()->getItemTable('sitebooking_ser');
      $serviceTableName = $serviceTable->info('name');


      $this->view->pro_id =  $values['pro_id'] = $pro_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('pro_id');

      //provider table item
      $this->view->providerTable = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);

      $scheduleTable = Engine_Api::_()->getDbTable('schedules','sitebooking');
      $scheduleRow = $scheduleTable->fetchRow($scheduleTable->select()->where('ser_id = ?',$item->ser_id));
      if($scheduleRow){
        $monday = json_decode($scheduleRow->monday, true);
        $tuesday = json_decode($scheduleRow->tuesday, true);
        $wednesday = json_decode($scheduleRow->wednesday, true);
        $thursday = json_decode($scheduleRow->thursday, true);
        $friday = json_decode($scheduleRow->friday, true);
        $saturday = json_decode($scheduleRow->saturday, true);
        $sunday = json_decode($scheduleRow->sunday, true);

        $data['demo'] = 'demo';
        if(!empty($monday))
          $data = array_merge($data,$monday);
        if(!empty($tuesday))
          $data = array_merge($data,$tuesday);
        if(!empty($wednesday))
          $data = array_merge($data,$wednesday);
        if(!empty($thursday))
          $data = array_merge($data,$thursday);
        if(!empty($friday))
          $data = array_merge($data,$friday);
        if(!empty($saturday))
          $data = array_merge($data,$saturday);
        if(!empty($sunday))
          $data = array_merge($data,$sunday);

        unset($data['demo']);
        $this->view->availability = $data;
      }

      $this->view->providerItem = $providerItem = $serviceTable->fetchRow($serviceTable->getServicesSelect($values));

      $this->view->title = $providerItem['provider_title'];

      $this->view->ser_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('ser_id');

      // SHOW CATEGORY
      if($item->category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->category_id);
        $this->view->category_name = $category["category_name"];
      } 
        
      if($item->first_level_category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->first_level_category_id);
        $this->view->first_level_category_name = $category["category_name"];
      } 
        
      if($item->second_level_category_id != 0) {
        $category = Engine_Api::_()->getItem('sitebooking_category',$item->second_level_category_id);
        $this->view->second_level_category_name = $category["category_name"];
      }

      //VIEW CODE TO SHOW THE PROFILE-MAPPING INFORMATION
      $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

      $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitebooking);

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

      $this->view->otherDetails = $view->fieldValueLoop($sitebooking, $this->view->fieldStructure);
      
      $params = $this->_getAllParams();
      $this->view->params = $params;

      $this->view->showContent = true;   

      // REVIEW BUTTON WORK
      if(!empty($viewer->user_id)){

        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitebooking');

        $sql = $reviewTable->select();
        $sql->where("engine4_sitebooking_reviews.resource_type like 'sitebooking_ser' AND engine4_sitebooking_reviews.resource_id = ".$this->view->ser_id)
        ->where("engine4_sitebooking_reviews.user_id = $viewer->user_id");

        $data = $reviewTable->fetchRow($sql);

        if(!empty($data))
          $this->view->flag = '2';
        else
          $this->view->flag = '1';

      } else{
        $this->view->flag = '0';
      } 
    }
  }

?>