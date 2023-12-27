<?php
class Sitebooking_Api_Core extends Core_Api_Abstract
{
  public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true, $profileTypeIds = array()) {

    $fieldsApi = Engine_Api::_()->getApi('core', 'fields');

    $type = $fieldsApi->getFieldType($spec);

    $structure = array();
    foreach ($fieldsApi->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
      // Skip maps that don't match parent_option_id (if provided)
      if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
        continue;
      }

      //FETCHING THE FIELDS WHICH BELONGS TO SOME SPECIFIC LISTNIG TYPE
      if ($parent_field_id == 1 && !empty($profileTypeIds) && !in_array($map->option_id, $profileTypeIds)) {
        continue;
      }

      // Get child field
      $field = $fieldsApi->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if (empty($field)) {
        continue;
      }

      // Add to structure
      if ($field->search) {
        $structure[$map->getKey()] = $map;
      }

      // Get children
      if ($field->canHaveDependents()) {
        $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal, $profileTypeIds);
      }
    }

    return $structure;
  }

  public function sendServiceBookingMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) && $owner->email) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_BOOKING_REQUEST', $mailParams);
    }
  }

  public function sendServiceCancelMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_BOOKING_CANCEL', $mailParams);
    }
  }

  public function sendServiceAcceptMail($receiver,$object,$owner,$meeting_urls) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
        'zoom_meeting_time' => $meeting_urls[0]['start_time'],
        'zoom_meeting_url' => $meeting_urls[0]['join_url'],
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_BOOKING_ACCEPT', $mailParams);
    }
  }

  public function sendServiceRejectMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_BOOKING_REJECT', $mailParams);
    }
  }

  public function sendServiceCompleteMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_BOOKING_COMPLETION', $mailParams);
    }
  }
  public function showServiceDuration($seconds) {
    if(!empty($seconds)) {
      if($seconds >= 3600){
        $hours = $seconds/3600;
        if($hours == 1)
          $duration = $hours.' Hour';
        else
          $duration = $hours.' Hours';
      }
      else{
        $minutes = $seconds/60;
        $duration = $minutes.' Minutes';
      }
      return $duration;
    } else {
      return  "NA";
    }
  }

  public function sendProviderApproveMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'provider_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_PROVIDER_APPROVING', $mailParams);
    }
  }

  public function sendProviderAutoapproveMail($receiver,$object) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'object_link' => $object->getHref(),
        'provider_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_PROVIDER_AUTOAPPROVING', $mailParams);
    }
  }


  public function sendProviderDisapproveMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'provider_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_PROVIDER_DISAPPROVING', $mailParams);
    }
  }

  public function sendServiceApproveMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_APPROVING', $mailParams);
    }
  }

  public function sendServiceAutoapproveMail($receiver,$object) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_AUTOAPPROVING', $mailParams);
    }
  }

  public function sendServiceDisapproveMail($receiver,$object,$owner) {
    $email = $receiver->email;
    if( !empty($email) ) {
      $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'date' => time(),
        'sender_email' => $owner->email,
        'sender_title' => $owner->getTitle(),
        'sender_link' => $owner->getHref(),
        'sender_photo' => $owner->getPhotoUrl('thumb.icon'),
        'object_link' => $object->getHref(),
        'service_title' => $object->getTitle(),
      );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'MAIL_SERVICE_DISAPPROVING', $mailParams);
    }
  }


  function timezoneConvertUsingDuration($currentTimezone,$reqiuredTimezone,$time,$duration){
    $timeDuration = array();
    $time = json_decode($time);
    foreach ($time as $key => $value) {
      $timeSlots = explode(',', $value);
      foreach ($timeSlots as $slot) {
        $date1 = date_create($key, timezone_open($currentTimezone));
        $s = explode('-', $slot);
        date_time_set($date1,(int) explode(":",$s[0])[0],(int) explode(":",$s[0])[1]);
        $date2 = date_timezone_set($date1, timezone_open($reqiuredTimezone));

        if(empty($timeDuration[date_format($date2, 'Y-m-d')]) ){
          $timeDuration[date_format($date2, 'Y-m-d')] = date_format($date2, 'H:i').'-'.date_format(date_add($date2, date_interval_create_from_date_string($duration." seconds")),'H:i');
        }else{
          $timeDuration[date_format($date2, 'Y-m-d')] = $timeDuration[date_format($date2, 'Y-m-d')].', '.date_format($date2, 'H:i').'-'.date_format(date_add($date2, date_interval_create_from_date_string($duration." seconds")),'H:i');
        }

      }
    }
    return $timeDuration;
  }


  function timezoneConvert($currentTimezone,$reqiuredTimezone,$time){
    $timeDuration = array();
    $time = json_decode($time);
    foreach ($time as $key => $value) {
      $timeSlots = explode(',', $value);
      foreach ($timeSlots as $slot) {
        $date1 = date_create($key, timezone_open($currentTimezone));
        $s = explode('-', $slot);
        date_time_set($date1, (int)explode(":",$s[0])[0], (int)explode(":",$s[0])[1]);
        $date2 = date_timezone_set($date1, timezone_open($reqiuredTimezone));

        //claculate duration in seconds
        $x = date_create($key, timezone_open('UTC'));
        date_time_set($x, (int) explode(":",$s[0])[0], (int) explode(":",$s[0])[1]);
        $y = date_format($x,'y-m-d H:i');
        date_time_set($x, (int) explode(":",$s[1])[0], (int) explode(":",$s[1])[1]);
        $z = date_format($x,'y-m-d H:i');

        $start = strtotime($y);
        $end = strtotime($z);
        $duration = $end-$start;
        if($duration < 0){
          $duration = 86400+$duration;
        }
        if(empty($timeDuration[date_format($date2, 'Y-m-d')]) ){
          $timeDuration[date_format($date2, 'Y-m-d')] = date_format($date2, 'H:i').'-'.date_format(date_add($date2, date_interval_create_from_date_string($duration." seconds")),'H:i');
        }else{
          $timeDuration[date_format($date2, 'Y-m-d')] = $timeDuration[date_format($date2, 'Y-m-d')].', '.date_format($date2, 'H:i').'-'.date_format(date_add($date2, date_interval_create_from_date_string($duration." seconds")),'H:i');
        }
      }
    }
    return $timeDuration;
  }
  
      public function getGateway($gateway_id)
    {
        return $this->getPlugin($gateway_id)->getGateway();
    }

    public function getPlugin($gateway_id)
    {
        if (null === $this->_plugin) {
            if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) {
                return null;
            }
            Engine_Loader::loadClass($gateway->plugin);
            if (!class_exists($gateway->plugin)) {
                return null;
            }
            $class = str_replace('Payment', 'Sitebooking', $gateway->plugin);

            Engine_Loader::loadClass($class);
            if (!class_exists($class)) {
                return null;
            }

            $plugin = new $class($gateway);
            if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
                throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
            }
            $this->_plugin = $plugin;
        }
        return $this->_plugin;
    }

}
?>