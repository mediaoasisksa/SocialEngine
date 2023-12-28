<?php 

class Sitebooking_Plugin_Core
{
  public function onSitebookingSerDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof Sitebooking_Model_Ser ) {

      $service_id = $payload->ser_id;

      $overview = Engine_Api::_()->getDbtable('serviceoverviews','sitebooking');
      $overview = $overview->fetchRow('ser_id = '.$service_id);

      $schedule = Engine_Api::_()->getDbtable('schedules','sitebooking');
      $schedule = $schedule->fetchRow('ser_id = '.$service_id);

      $serviceRating = Engine_Api::_()->getDbtable('serviceratings','sitebooking');
      $serviceRating = $serviceRating->fetchAll('ser_id = '.$service_id);

      $review = Engine_Api::_()->getDbtable('reviews','sitebooking');
      $reviewTableName = $review->info('name');
      $reviewSql = $review->select()
             ->where($reviewTableName . '.resource_id = ?', $service_id)
             ->where($reviewTableName . '.resource_type = ?', "sitebooking_ser");
      $review = $review->fetchAll($reviewSql);

      $serviceBooking = Engine_Api::_()->getDbtable('servicebookings','sitebooking');
      $serviceBooking = $serviceBooking->fetchAll('ser_id = '.$service_id);

      $field_values = Engine_Api::_()->fields()->getTable('sitebooking_ser', 'values');

      $field_search = Engine_Api::_()->fields()->getTable('sitebooking_ser', 'search');

      $values = $field_values->fetchAll($field_values->select()->where('item_id = ?', $service_id));

      $searchs = $field_search->fetchAll($field_search->select()->where('item_id = ?', $service_id));

      if(!empty($overview)) {
        $overview->delete();
      }
      
      if(!empty($serviceRating)) {
        foreach ($serviceRating as $item) { 
          $item->delete();
        }
      }

      if(!empty($review)) {
        foreach ($review as $item) { 
          $item->delete();
        }
      }

      if(!empty($serviceBooking)) {
        foreach ($serviceBooking as $item) { 
          $item->delete();
        }
      }

      if(!empty($schedule)) {
        $schedule->delete();
      }

      if(!empty($values)) {        
        foreach ($values as $value) {
          if(!empty($value))
            $value->delete();
        }
      }
    
      if(!empty($searchs)) {        
        foreach ($searchs as $search) {
          if(!empty($search))
            $search->delete();
        }
      }

    }
  }

  public function onSitebookingProDeleteBefore($event)
  {
    $payload = $event->getPayload();
    $provider_id = $payload->pro_id;

    if( $payload instanceof Sitebooking_Model_Pro ) {
      
      $location = Engine_Api::_()->getDbtable('providerlocations','sitebooking');
      $location = $location->fetchRow('pro_id = '.$provider_id);
      
      $overview = Engine_Api::_()->getDbtable('providersoverviews','sitebooking');
      $overview = $overview->fetchRow('pro_id = '.$provider_id);

      $providerRating = Engine_Api::_()->getDbtable('providerratings','sitebooking');
      $providerRating = $providerRating->fetchAll('pro_id = '.$provider_id);

      $review = Engine_Api::_()->getDbtable('reviews','sitebooking');
      $reviewTableName = $review->info('name');
      $reviewSql = $review->select()
             ->where($reviewTableName . '.resource_id = ?', $provider_id)
             ->where($reviewTableName . '.resource_type = ?', "sitebooking_pro");
      $review = $review->fetchAll($reviewSql);

      if(!empty($location)){
        $location->delete();
      }
      
      if(!empty($overview)){
        $overview->delete();
      }

      if(!empty($providerRating)) {
        foreach ($providerRating as $item) { 
          $item->delete();
        }
      }

      if(!empty($review)) {
        foreach ($review as $item) { 
          $item->delete();
        }
      }

      // Delete all provider services with its all service dependencies
      $service = Engine_Api::_()->getDbtable('sers','sitebooking');
      $services = $service->fetchAll('parent_id = '.$provider_id);

      foreach ($services as $value) {

        $value->delete();
       
      }  

    }
  }

}