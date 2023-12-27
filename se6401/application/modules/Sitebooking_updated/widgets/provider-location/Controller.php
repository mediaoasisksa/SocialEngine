<?php

class Sitebooking_Widget_ProviderLocationController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    $subject = Engine_Api::_()->core()->getSubject();
    
    if( $subject->getType() === 'sitebooking_pro' ) { 

      $pro_id = $subject->pro_id;
      $this->view->item = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);

      $providerLocationTable = Engine_Api::_()->getDbtable('providerlocations', 'sitebooking');


      $select = $providerLocationTable->select();
      $sql = $select->where("engine4_sitebooking_providerlocations.pro_id = $pro_id");

      $data = $providerLocationTable->fetchRow($sql);

      if(empty($data)) {
        //NO Render
        return $this->setNoRender();
      }

      $this->view->latitude = $data->latitude;
      $this->view->longitude = $data->longitude;

    }

    if( $subject->getType() === 'sitebooking_ser' ) { 

      $pro_id = $subject->parent_id;
      $this->view->item = Engine_Api::_()->getItem('sitebooking_pro', $pro_id);

      $providerLocationTable = Engine_Api::_()->getDbtable('providerlocations', 'sitebooking');

      $select = $providerLocationTable->select();
      $sql = $select->where("engine4_sitebooking_providerlocations.pro_id = $pro_id");


      $data = $providerLocationTable->fetchRow($sql);

      if(empty($data)) {
        //NO Render
        return $this->setNoRender();
      }

      $this->view->latitude = $data->latitude;
      $this->view->longitude = $data->longitude;

    }
  }
}


?>