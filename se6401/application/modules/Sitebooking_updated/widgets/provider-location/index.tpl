<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();?>

<div class="provider_location">
  
  <div class="_map" id = "map">  
    <script>
      // Initialize and add the map
      function initMap() {
        // The location of Uluru
        var uluru = {lat: <?php echo $this->latitude;?>, lng: <?php echo $this->longitude;?>};
        // The map, centered at Uluru
        var map = new google.maps.Map(
            document.getElementById('map'), {zoom: 16, center: uluru});
        // The marker, positioned at Uluru
        var marker = new google.maps.Marker({position: uluru, map: map});
      }
      </script>

      <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey;?>&callback=initMap">
      </script>
  </div>

  <div class="_info">
    <?php if(!empty($this->item->telephone_no)): ?>
      <div class="_no">
        <i class="fa fa-phone"></i>
        <div><?php echo $this->item->telephone_no ?></div>
      </div>
    <?php endif;?>

    <?php if(!empty($this->item->location)): ?>
      <div class="_pointer">
        <i class="fa fa-map-marker"></i>
        <div><?php echo $this->item->location;?></div>
      </div>
    <?php endif;?>

    <?php if(!empty($this->item->email)): ?>
      <div class="_mail">
        <i class="fa fa-envelope-o"></i>
        <div><a href="mailto:<?php echo $this->item->email ?>?Subject=write here" target="_top"><?php echo $this->item->email ?></a></div>
      </div>
    <?php endif;?>

    <?php if(!empty($this->item->website)): ?>
      <div class="_web">
        <i class="fa fa-globe"></i>
        <?php if(strstr($this->item->website, 'http://') || strstr($this->item->website, 'https://')) : ?>
            <div><a href='<?php echo $this->item->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->item->website ?></a></div>
            <?php else:?>
            <div><a href='http://<?php echo $this->item->website ?>' target="_blank"><?php echo $this->translate(''); ?> <?php echo $this->item->website ?></a></div>
            <?php endif;?>
      </div>
    <?php endif;?>
  </div>

</div>