<?php 
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js');
?>

<ul class="services_browse">
  
  <li>
    
    <div class='services_browse_photo'>
       <?php echo $this->itemBackgroundPhoto($this->item, 'thumb.normal'); ?>
    </div>

    <div class='services_browse_info'>
      
      <span class='services_browse_info_title'>
          <h3><?php echo $this->item->getTitle() ?></h3>
      </span>

      <div class="stat_info">
        <p>
  	      <b><span><?php echo $this->translate('Location').": " ?> </span></b> 
  	      <span><?php echo $this->item->location; ?></span>
        </p>
      </div>

      <div class="stat_info">
        <p>
      	  <b><span><?php echo $this->translate('Designation').": " ?> </span></b>
      	  <span><?php echo $this->item->designation; ?></span>
        </p>
      </div>

      <?php if($this->item->no_of_bookings != 0) : ?>
        <div style= "margin-top: 6px;" >
        	<b><span><?php echo $this->translate('Number Of Bookings').": " ?></span></b>
		      <span><?php echo $this->item->no_of_bookings; ?></span>
        </div>
      <?php endif; ?>
      
      <p class='providers_browse_info_blurb stat_info'>
        <b><span><?php echo $this->translate('Description').": " ?></span></b>
        <span><?php echo $this->item->description; ?></span>
  	  </p>

    </div>

  </li>

</ul>  



