<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InviteController.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Seaocore_InviteController extends Core_Controller_Action_Standard
{
	
  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
  protected $_periods = array(
      Zend_Date::DAY, //dd
      Zend_Date::WEEK, //ww
      Zend_Date::MONTH, //MM
      Zend_Date::YEAR, //y
  );
  protected $_allPeriods = array(
      Zend_Date::SECOND,
      Zend_Date::MINUTE,
      Zend_Date::HOUR,
      Zend_Date::DAY,
      Zend_Date::WEEK,
      Zend_Date::MONTH,
      Zend_Date::YEAR,
  );
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );
  
  public function viewstatisticsAction () {
    
  
    
  }
  
  //RESEND THE INVITATION TO USER.
  
  public function resendinviteAction() {
    $this->view->form = new Seaocore_Form_Invite_Invite();
    
    $this->view->invite_service = $socialType =  $_GET['socialtype'];
    $this->view->social_profileid =  $_GET['social_profileid'];
    $table = Engine_Api::_()->getDbtable('invites', 'invite');
    if($socialType == 'linkedin' || $socialType == 'facebook' || $socialType == 'twitter')
      $row = $table->fetchRow(array('social_profileid = ?' => $_GET['social_profileid']));
    else 
      $row = $table->fetchRow(array('recipient = ?' => $_GET['social_profileid']));
    $this->view->custom_message = $row->message;
  }
  
  
  public function chartDataAction() { 
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }

    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
      die('whoops.');
      return;
    }

    // Validate start
    if ($start && !is_numeric($start)) {
      $start = strtotime($start);
    }
    if (!$start) {
      $start = time();
    }

    // Fixes issues with month view
    Zend_Date::setOptions(array(
        'extend_month' => true,
    ));

    // Make start fit to period?
    $startObject = new Zend_Date($start);
   
    $startObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $endObject->add($periodCount, $period);

    $end_tmstmp_obj = new Zend_Date(time());
    $end_tmstmp_obj->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $end_tmstmp = $end_tmstmp_obj->getTimestamp();
    if ($endObject->getTimestamp() < $end_tmstmp) {
      $end_tmstmp = $endObject->getTimestamp();
    }
    $end_tmstmp_object = new Zend_Date($end_tmstmp);
    $end_tmstmp_object->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    // Get data
    $statsTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $statsName = $statsTable->info('name');

    $statsSelect = $statsTable->select();

    $statsSelect
            ->from($statsName, array('COUNT(new_user_id) as invites', 'timestamp', 'user_id'))
            ->where($statsName . '.timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
            ->where($statsName . '.timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
            ->group("DATE_FORMAT(" . $statsName . " .timestamp, '%Y-%m-%d')")
            ->order($statsName . '.timestamp ASC')
            ->distinct(true);
    if ($type == 'referred')
      $statsSelect->where("(`{$statsName}`.`new_user_id` <> ?)", 0);

    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;

    $data_sentInvites = array();
    $data_referredInvites = array();
   
    $cumulative_sent = 0;
    $cumulative_referred = 0;
   
    $previous_sent = 0;
    $previous_referred = 0;
    
    $oldtimestamp = $currentObject->getTimestamp();
    do {
      $nextObject->add(1, $chunk);
      $currentObjectTimestamp = $currentObject->getTimestamp();
      $data_sentInvites[$currentObjectTimestamp] = $cumulative_sent;
      $data_referredInvites[$currentObjectTimestamp] = $cumulative_referred;
      

      // Get everything that matches
      $currentPeriodCount_sent = 0;
      $currentPeriodCount_referred = 0;
     
      switch ($type) {
        case 'all':
          foreach ($rawData as $key => $rawDatum) {  
            $timestamp = explode(" ", $rawDatum->timestamp);
            $rawDatumDate = strtotime($rawDatum->timestamp);
            if ($rawDatumDate <= $currentObjectTimestamp && $rawDatumDate > $oldtimestamp) {
              $currentPeriodCount_sent += $rawDatum->invites;
              //GET THE TOTAL REFERRED FOR THIS DATE:
              $currentPeriodCount_referred += Engine_Api::_()->getApi('invite', 'seaocore')->referredInvites($oldtimestamp,  strtotime($timestamp[0]. ' 23:59:59'));              
              $oldtimestamp = strtotime($timestamp[0]. ' 23:59:59');
            }
          }
          
          break;
        case 'referred':
          foreach ($rawData as $key => $rawDatum) {  
            $timestamp = explode(" ", $rawDatum->timestamp);
            $rawDatumDate = strtotime($timestamp[0] . '00:00:00'); 
            if ($rawDatumDate <= $currentObjectTimestamp && $rawDatumDate > $oldtimestamp) {
              $currentPeriodCount_referred = $rawDatum->invites;                          
              $oldtimestamp = $rawDatumDate;
            }
          }
         
          break;
        case 'sent':
         foreach ($rawData as $key => $rawDatum) {  
            $timestamp = explode(" ", $rawDatum->timestamp);
            $rawDatumDate = strtotime($timestamp[0] . '00:00:00'); 
            if ($rawDatumDate <= $currentObjectTimestamp && $rawDatumDate > $oldtimestamp) {
              $currentPeriodCount_sent = $rawDatum->invites;                          
              $oldtimestamp = $rawDatumDate;
            }
          }
         
          break;
        default:
        
        
      }     

      // Now do stuff with it
      switch ($mode) {
        default:
        case 'normal':
          $data_sentInvites[$currentObjectTimestamp] = $currentPeriodCount_sent;
          $data_referredInvites[$currentObjectTimestamp] = $currentPeriodCount_referred;
          
          break;
        case 'cumulative':
          $cumulative_sent += $currentPeriodCount_sent;
          $cumulative_referred += $currentPeriodCount_referred;
          $data_sentInvites[$currentObjectTimestamp] = $cumulative_sent;
          $data_referredInvites[$currentObjectTimestamp] = $cumulative_referred; 
          break;
        case 'delta':
          $data_sentInvites[$currentObjectTimestamp] = $currentPeriodCount_sent - $previous_sent;          
          $previous_sent = $currentPeriodCount_sent;
          
          $data_referredInvites[$currentObjectTimestamp] = $currentPeriodCount_referred - $previous_referred;          
          $previous_referred = $currentPeriodCount_referred;
          
          break;
      }
      $currentObject->add(1, $chunk);
    } while ($currentObject->getTimestamp() < $end_tmstmp);

    $data_sentInvites_count = count($data_sentInvites);
    $data_referredInvites_count = count($data_referredInvites);
   
    $data = array();
    switch ($type) {

      case 'all': 
        $merged_data_array = array_merge($data_sentInvites, $data_referredInvites);
        $data_count_max = max($data_sentInvites_count, $data_referredInvites_count);
        $data = $data_sentInvites;       
        break;

      case 'sent':      
        $merged_data_array = $data_sentInvites;
        $data_count_max = $data_sentInvites_count;
        $data = $data_sentInvites;
        break;

      case 'referred':
        $merged_data_array = $data_referredInvites;
        $data_count_max = $data_referredInvites_count;
        $data = $data_referredInvites;
        break;
    }

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ($data as $key => $value) {
      if ($key <= $end_tmstmp) {
        $labelDate->set($key);
        $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
      } else {
        $labelDate->set($end_tmstmp);
        $labelStrings[] = date('n/j/y', $end_tmstmp);
      }
    }


    // Make title
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_SEAO_INVITES_STATISTICS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $title = $titleStr . ': '. $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($end_tmstmp_object);

    $sent_str = $translate->_('Sent');
    $referred_str = $translate->_('Referred');
   
    // Make data
    switch ($type) {

      case 'all':
        $labelStrings = '';
        $labelData1 = array();
        $labelData1['Date'] = $titleStr;
        $labelDate = new Zend_Date();
        foreach ($data_sentInvites as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $labelData1["$labelStrings"] = $value;
        }
        
        $labelStrings = '';
        $labelData2 = array();
        $labelData2['Date'] = $titleStr;
        $labelDate = new Zend_Date();
        foreach ($data_referredInvites as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $labelData2["$labelStrings"] = $value;
        }

        $label[]=array('Date','sent','referred');
        foreach ($data_sentInvites as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $label[]=array($labelStrings,(int)$labelData1[$labelStrings],(int)$labelData2[$labelStrings]);
        }
        $this->view->case="all";
        $this->view->data=$label;
        break;

      case 'sent':
        $labelStrings = '';
        $labelData = array();
        $labelData['Date'] = $titleStr;
        $labelDate = new Zend_Date();
        foreach ($data_sentInvites as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $labelData["$labelStrings"] = $value;
        }
        $this->view->data=$labelData;
        break;

      case 'referred':
        $labelStrings = '';
        $labelData = array();
        $labelData['Date'] = $titleStr;
        $labelDate = new Zend_Date();
        foreach ($data_referredInvites as $key => $value) {
          $labelDate->set($key);
          $labelStrings = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
          $labelData["$labelStrings"] = $value;
        }
       $this->view->data=$labelData;
        break;
    }

    // Send
    $this->view->title=$title;
  
  }


}

?>