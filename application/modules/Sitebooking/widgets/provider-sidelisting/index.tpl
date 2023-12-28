<ul class="providers_browse _wrapper">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      
      <div class='providers_browse_photo'>
        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
      </div>
      
      <div class='providers_browse_info'>
        
        <span class='providers_browse_info_title'>
            <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
        </span>

        <?php if(!empty($item->location)): ?>
          <div class="_location"><i class="fa fa-map-marker"></i><?php echo $item->location; ?></div>
        <?php else: ?>
          <div class="_location"><i class="fa fa-map-marker"></i><?php echo "No Location Mentioned"; ?></div>
        <?php endif;?>
        
        <!-- RATING -->
        <div class=' '>
          <div id="sitebooking_rating" class="rating">
            <span id="provider_sidelisting_rate_<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
            <span id="provider_sidelisting_rate_<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
            <span id="provider_sidelisting_rate_<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
            <span id="provider_sidelisting_rate_<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
            <span id="provider_sidelisting_rate_<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
          </div>
        </div>

        <script type="text/javascript">
          en4.core.runonce.add( function() {
            var rating = 0;
            rating = "<?php echo $item->rating;?>";
          
            for(var x=1; x<=parseInt(rating); x++) {
                
              var id = <?php echo $item->getIdentity() ?>;
              id = "provider_sidelisting_rate_"+id+"_"+x;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
            }

            var remainder = Math.round(rating)-rating;

            for(var x=parseInt(rating)+1; x<=5; x++) {
                
              var id = <?php echo $item->getIdentity() ?>;
              id = "provider_sidelisting_rate_"+id+"_"+x;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
            }

            if (remainder <= 0.5 && remainder !=0){

              var id = <?php echo $item->getIdentity() ?>;
              var last = parseInt(rating)+1;
              id = "provider_sidelisting_rate_"+id+"_"+last;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
            }
          });
        </script>

      </div>
    </li>
  <?php endforeach; ?>
</ul>




