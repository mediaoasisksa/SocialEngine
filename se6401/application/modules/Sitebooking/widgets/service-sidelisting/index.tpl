<ul class="services_browse _wrapper">
  <?php foreach( $this->paginator as $item ): ?>
    <li>

      <div class='services_browse_photo'>
        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
      </div>

      <div class='services_browse_info'>
        <span class='services_browse_info_title'>
          <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
        </span>

        <p class='services_browse_info_date'>

          <span class=''>

            <?php if(!empty($item->provider_photo_id)) :?>

              <?php $url = Engine_Api::_()->storage()->get($item->provider_photo_id)->getPhotoUrl();?>
              
              <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $url style='width: 20px; height: 20px; border-radius: 50%;'>")
              ?>

            <?php else: ?>

              <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_provider_profile.png" ?> 

              <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $src style='width: 20px; height: 20px; border-radius: 50%;'>")
              ?>

            <?php endif; ?>

          </span>

          <span><?php echo $this->translate('By');?></span>
          <span class="sb_pro_name"><?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug), $item->provider_title) ?></span>
        </p>
        
        <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></div>

        <!-- RATING -->
        <div class='  '>
          <div id="sitebooking_rating" class="rating">
            <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_1" class="rating_star_big_generic"> </span>
            <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_2" class="rating_star_big_generic"> </span>
            <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_3" class="rating_star_big_generic"></span>
            <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_4" class="rating_star_big_generic" ></span>
            <span id="service_browse_rate_<?php echo $item->getIdentity() ?>_5" class="rating_star_big_generic" ></span>
          </div>
        </div>

        <script type="text/javascript">
          en4.core.runonce.add( function() {
            var rating = 0;
            rating = "<?php echo $item->rating;?>";
            
            for(var x=1; x<=parseInt(rating); x++) {
              
              var id = <?php echo $item->getIdentity() ?>;
              id = "service_browse_rate_"+id+"_"+x;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
            }

            var remainder = Math.round(rating)-rating;

            for(var x=parseInt(rating)+1; x<=5; x++) {
              
              var id = <?php echo $item->getIdentity() ?>;
              id = "service_browse_rate_"+id+"_"+x;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
            }

            if (remainder <= 0.5 && remainder !=0){

              var id = <?php echo $item->getIdentity() ?>;
              var last = parseInt(rating)+1;
              id = "service_browse_rate_"+id+"_"+last;
              document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
            }
          });
        </script>
      </div>
    </li>
  <?php endforeach; ?>
</ul>







        
