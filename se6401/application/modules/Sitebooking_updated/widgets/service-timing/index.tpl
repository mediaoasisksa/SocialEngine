<div class="provider_timing">
    <div class="_info">
      <div class="_days">Mon</div>
		<div class="_timings">
        <?php $mon = $this->mon;
        if(!empty($mon)){
        for ($i=0; $i < sizeof($mon); $i=$i+2) {
          $monday = $this->monday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$monday[$mon[$i+1]])[0], (int) explode(':',$monday[$mon[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $monday[$mon[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
	  </div>
    <div class="_info">
       <div class="_days">Tue</div>
		<div class="_timings">
        <?php $tue = $this->tue;
        if(!empty($tue)){
        for ($i=0; $i < sizeof($tue); $i=$i+2) {
          $tuesday = $this->tuesday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$tuesday[$tue[$i+1]])[0], (int) explode(':',$tuesday[$tue[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $tuesday[$tue[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info">
     <div class="_days">Wed</div>
		<div class="_timings">
        <?php $wed = $this->wed;
        if(!empty($wed)){
        for ($i=0; $i < sizeof($wed); $i=$i+2) {
          $wednesday = $this->wednesday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$wednesday[$wed[$i+1]])[0], (int) explode(':',$wednesday[$wed[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $wednesday[$wed[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info">
      <div class="_days">Thu</div>
		<div class="_timings">
        <?php $thu = $this->thu;
        if(!empty($thu)){
        for ($i=0; $i < sizeof($thu); $i=$i+2) {
          $thursday = $this->thursday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$thursday[$thu[$i+1]])[0], (int) explode(':',$thursday[$thu[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $thursday[$thu[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info">
      <div class="_days">Fri</div>
		<div class="_timings">
        <?php $fri = $this->fri;
        if(!empty($fri)){
        for ($i=0; $i < sizeof($fri); $i=$i+2) {
          $friday = $this->friday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$friday[$fri[$i+1]])[0], (int) explode(':',$friday[$fri[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $friday[$fri[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
			</div>
    </div>
    <div class="_info">
     <div class="_days">Sat</div>
		<div class="_timings">
        <?php $sat = $this->sat;
        if(!empty($sat)){
        for ($i=0; $i < sizeof($sat); $i=$i+2) {
          $saturday = $this->saturday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$saturday[$sat[$i+1]])[0], (int) explode(':',$saturday[$sat[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $saturday[$sat[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
		</div>
    </div>
    <div class="_info">
      <div class="_days">Sun</div>
		<div class="_timings">
        <?php $sun = $this->sun;
        if(!empty($sun)){
        for ($i=0; $i < sizeof($sun); $i=$i+2) {
          $sunday = $this->sunday; 
          $date = date_create('2001-01-01');
          date_time_set($date, (int) explode(':',$sunday[$sun[$i+1]])[0], (int) explode(':',$sunday[$sun[$i+1]])[1]);
          date_add($date, date_interval_create_from_date_string("1800 seconds"));
          $time = date_format($date,"H:i");
        ?>
          <span> <?php echo $sunday[$sun[$i]].' - '.$time;?></span>
        <?php
        }
        }else{ ?>
          <span style="color: #f00;"> <?php echo $this->translate('Closed');?></span>
        <?php } 
        ?>
	   </div>
    </div>
</div>