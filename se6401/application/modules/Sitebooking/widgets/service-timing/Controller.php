<?php class Sitebooking_Widget_ServiceTimingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitebooking_ser')) {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $service = Engine_Api::_()->core()->getSubject('sitebooking_ser');
    $service_id = $service->getIdentity();
    $table = Engine_Api::_()->getItemTable('sitebooking_schedule');
    $scheduleRow = $table->fetchRow($table->select()->where('ser_id = ?',$service_id));

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

      foreach ($data as $key => $value) {
        $date1 = date_create(null, timezone_open('UTC'));
        date_time_set($date1, (int) explode(":",$value)[0], (int) explode(":",$value)[1]);
        $d1 =  date_format($date1, 'Y-m-d');
        $date2 = date_timezone_set($date1, timezone_open($viewer->timezone));
        $d2 = date_format($date2, 'Y-m-d');

        $utcTimeSlot = date_format($date2, 'H:i');

        $s1 = date_create($d1);
        $s2 = date_create($d2);
        $diff=date_diff($s1,$s2);
        $dayDiff =  $diff->format("%R%a days");
        $x = explode("_",$key);
        
        if($x[0] === 'mon' && $value != 'mon'){
          $day = strtolower(substr(date('l', strtotime('monday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'tue' && $value != 'tue'){
          $day = strtolower(substr(date('l', strtotime('tuesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'wed' && $value != 'wed'){
          $day = strtolower(substr(date('l', strtotime('wednesday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'thu' && $value != 'thu'){
          $day = strtolower(substr(date('l', strtotime('thursday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'fri' && $value != 'fri'){
          $day = strtolower(substr(date('l', strtotime('friday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sat' && $value != 'sat'){ 
          $day = strtolower(substr(date('l', strtotime('saturday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }
        if($x[0] === 'sun' && $value != 'sun'){
          $day = strtolower(substr(date('l', strtotime('sunday '.$dayDiff)),0,3));
          $popAvail[$day.'_'.$value] = $utcTimeSlot;
        }

      }

      $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = $sunday = array();
      $c1 = $c2 = $c3 = $c4 = $c5 = $c6 = $c7 = -1;
      foreach ($popAvail as $key => $value){
        $x = explode("_",$key);
        if($x[0] === 'mon'){
          $c1++;
          $monday['mon_'.$c1] = $value; 
        }
        if($x[0] === 'tue'){
          $c2++;
          $tuesday['tue_'.$c2] = $value; 
        }
        if($x[0] === 'wed'){
          $c3++;
          $wednesday['wed_'.$c3] = $value; 
        }
        if($x[0] === 'thu'){
          $c4++;
          $thursday['thu_'.$c4] = $value; 
        }
        if($x[0] === 'fri'){
          $c5++;
          $friday['fri_'.$c5] = $value; 
        }
        if($x[0] === 'sat'){
          $c6++;
          $saturday['sat_'.$c6] = $value; 
        }
        if($x[0] === 'sun'){
          $c7++;
          $sunday['sun_'.$c7] = $value; 
        }
      }

      sort($monday);
      sort($tuesday);
      sort($wednesday);
      sort($thursday);
      sort($friday);
      sort($saturday);
      sort($sunday);

      $this->view->monday = $monday;
      $this->view->tuesday = $tuesday;
      $this->view->wednesday = $wednesday;
      $this->view->thursday = $thursday;
      $this->view->friday = $friday;
      $this->view->saturday = $saturday;
      $this->view->sunday = $sunday;

      if(!empty($monday)){
        $mon = $monday;
        $tempMon[0] = 0;
        for ($i=1; $i < sizeof($mon); $i++) { 
          $startTime = new DateTime($mon[$i-1]);
          $endTime = new DateTime($mon[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempMon[] = $i-1;
            $tempMon[] = $i;  
          }
        }
        $tempMon[] = sizeof($mon)-1;
        $this->view->mon = $tempMon;
      }

      if(!empty($tuesday)){
        $tue = $tuesday;
        $tempTue[0] = 0;
        for ($i=1; $i < sizeof($tue); $i++) { 
          $startTime = new DateTime($tue[$i-1]);
          $endTime = new DateTime($tue[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempTue[] = $i-1;
            $tempTue[] = $i;  
          }
        }
        $tempTue[] = sizeof($tue)-1;
        $this->view->tue = $tempTue;
      }

      if(!empty($wednesday)){
        $wed = $wednesday;
        $tempWed[0] = 0;
        for ($i=1; $i < sizeof($wed); $i++) { 
          $startTime = new DateTime($wed[$i-1]);
          $endTime = new DateTime($wed[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempWed[] = $i-1;
            $tempWed[] = $i;  
          }
        }
        $tempWed[] = sizeof($wed)-1;
        $this->view->wed = $tempWed;
      }

      if(!empty($thursday)){
        $thu = $thursday;
        $tempThu[0] = 0;
        for ($i=1; $i < sizeof($thu); $i++) { 
          $startTime = new DateTime($thu[$i-1]);
          $endTime = new DateTime($thu[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempThu[] = $i-1;
            $tempThu[] = $i;  
          }
        }
        $tempThu[] = sizeof($thu)-1;
        $this->view->thu = $tempThu;
      }

      if(!empty($friday)){
        $fri = $friday;
        $tempFri[0] = 0;
        for ($i=1; $i < sizeof($fri); $i++) { 
          $startTime = new DateTime($fri[$i-1]);
          $endTime = new DateTime($fri[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempFri[] = $i-1;
            $tempFri[] = $i;  
          }
        }
        $tempFri[] = sizeof($fri)-1;
        $this->view->fri = $tempFri;
      }

      if(!empty($saturday)){
        $sat = $saturday;
        $tempSat[0] = 0;
        for ($i=1; $i < sizeof($sat); $i++) { 
          $startTime = new DateTime($sat[$i-1]);
          $endTime = new DateTime($sat[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempSat[] = $i-1;
            $tempSat[] = $i;  
          }
        }
        $tempSat[] = sizeof($sat)-1;
        $this->view->sat = $tempSat;
      }

      if(!empty($sunday)){
        $sun = $sunday;
        $tempSun[0] = 0;
        for ($i=1; $i < sizeof($sun); $i++) { 
          $startTime = new DateTime($sun[$i-1]);
          $endTime = new DateTime($sun[$i]);
          $duration = $startTime->diff($endTime); //$duration is a DateInterval object
          $timeDiff = $duration->format("%H:%I:%S"); 

          if( $timeDiff != '00:30:00' ){
            $tempSun[] = $i-1;
            $tempSun[] = $i;  
          }
        }
        $tempSun[] = sizeof($sun)-1;
        $this->view->sun = $tempSun;
      }
    }

  }
}
?>