<?php
class Sitebooking_Model_DbTable_Servicebookings extends Core_Model_Item_DbTable_Abstract 
{
  protected $_rowClass = "Sitebooking_Model_Servicebooking";
  
	public function getBookingsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getBookingsSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if( empty($params['limit']) )
    {
      $page = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebooking.page');
      $paginator->setItemCountPerPage($page);
    }

    return $paginator;
  }

  public function getBookingsSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $bookingTable = Engine_Api::_()->getDbtable('servicebookings', 'sitebooking');
    $bookingTableName = $bookingTable->info('name');

    $userTable = Engine_Api::_()->getItemtable('user');
    $userTableName = $userTable->info('name');

    $select = $bookingTable->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $bookingTableName.'.booking_date DESC' );

    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($bookingTableName.'.user_id = ?', $params['user_id']);
    }

    if( !empty($params['service_title']) || !empty($params['category']) )
    {
      $serviceTable = Engine_Api::_()->getItemtable('sitebooking_ser');
      $serviceTableName = $serviceTable->info('name');
      $title = $params['service_title'];
      $select = $select
              ->setIntegrityCheck(false)
              ->from($bookingTableName)
              ->joinLeft($serviceTableName, "$bookingTableName.ser_id = $serviceTableName.ser_id",array('title','category_id'))
              ->group("$bookingTableName.servicebooking_id");

      if( !empty($params['service_title']) ){
        $title = $params['service_title'];
        $select->where("$serviceTableName.title  LIKE '%$title%'");
      }
      if( !empty($params['category']) ){
        $category = $params['category'];
        $select->where("$serviceTableName.category_id = $category");
      }

    }

    if( !empty($params['status'])  )
    {
      $select->where($bookingTableName.'.status = ?', $params['status']);
    }

    if( !empty($params['pro_id']) )
    {
      $select->where($bookingTableName.'.pro_id = ?', $params['pro_id']);
    }

    if (!empty($params['category']) && $params['category'] != -1) {
      $select->where($serviceTableName . '.category_id = ?', $params['category']);
    }

    if( !empty($params['booking_date']) )
    {
      $bookingdate = date('Y-m-d', strtotime($params['booking_date']) );;
      // $bookingdate = str_replace("/","-",$bookingdate);
      $select->where("$bookingTableName.booking_date LIKE '%$bookingdate%'");
    }

    if( !empty($params['servicing_date']) )
    {
      $date = date('Y-m-d', strtotime($params['servicing_date']) );
      $select->where($bookingTableName.".servicing_date LIKE '%$date%'");

    } 

    if( !empty($owner) ) {
      return $select;
    }
    
    return $select;
  }
}