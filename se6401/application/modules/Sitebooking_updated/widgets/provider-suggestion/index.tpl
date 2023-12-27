<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_carousel.css') 
->prependStylesheet($baseUrl . 'application/modules/Sitebooking/externals/styles/owl.carousel.min.css')
->prependStylesheet($baseUrl . 'application/modules/Sitebooking/externals/styles/owl.carousel.css')
->prependStylesheet($baseUrl . 'application/modules/Sitebooking/externals/styles/owl.theme.default.css');
$this->headScript()
->appendFile($baseUrl . 'application/modules/Sitebooking/externals/scripts/jquery.min.js')
->appendFile($baseUrl . 'application/modules/Sitebooking/externals/scripts/owl.carousel.js');
?>
<?php $widgetIdentity = $this->identity; ?>

<script type="text/javascript">
  en4.core.runonce.add(function () {
   var j_q = jq.noConflict();
   j_q(document).ready(function () {
    j_q('#owl-carousel-<?php echo $widgetIdentity ?>').owlCarousel({
      loop: false,
      margin: 10,
      responsiveClass: true,
      nav: true,
      navContainer: j_q('#owl-carousel-nav-<?php echo $widgetIdentity ?>'),
      responsive: {
        0: {
          items: 1,
        },
        600: {
          items: 1,
        },
        1000: {
          items: 1,
          loop: false,
          dots: true
        }
      }
    })
  })
 });
</script>

<?php if( count($this->suggestedProviders) > 0 ): ?>
<div class="owl-carousel owl-theme" id="owl-carousel-<?php echo $widgetIdentity ?>">

  <?php foreach ($this->suggestedProviders as $item): ?>
    <div class='_wrapper'>
      <div class="_thumb" style="height: 200px;">
        <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->pro_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->slug), $this->itemBackgroundPhoto($item, 'thumb.normal')); ?>
      </div>

      <div class="_info">
        <div class="_bottom">
          <div class="_details">
            <h3 class="_title" ><?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->pro_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->slug), $item->title); ?></h3>
            <!-- RATING -->
            <div id="sitebooking_rating" class="_rating">
              <span id="provider_carousel_rate<?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>_1" class="rating_star_big_generic"> </span>
              <span id="provider_carousel_rate<?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>_2" class="rating_star_big_generic"> </span>
              <span id="provider_carousel_rate<?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>_3" class="rating_star_big_generic"></span>
              <span id="provider_carousel_rate<?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>_4" class="rating_star_big_generic" ></span>
              <span id="provider_carousel_rate<?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>_5" class="rating_star_big_generic" ></span>
            </div>
          </div>
          <script type="text/javascript">
            en4.core.runonce.add( function() {
              var rating = "<?php echo $item->rating;?>";


              for(var x=1; x<=parseInt(rating); x++) {
                  
                var id = <?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>;
                id = "provider_carousel_rate"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
              }

              var remainder = Math.round(rating)-rating;

              for(var x=parseInt(rating)+1; x<=5; x++) {
                  
              console.log(rating);
                var id = <?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>;
                id = "provider_carousel_rate"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
              }

              if (remainder <= 0.5 && remainder !=0){

                var id = <?php echo $item->pro_id ?><?php echo $this->widgetIdentity ?>;
                var last = parseInt(rating)+1;
                id = "provider_carousel_rate"+id+"_"+last;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_half');
              }
            });
          </script>
        </div>
      </div>
    </div>  

  <?php endforeach; ?>
</div>
<div id="owl-carousel-nav-<?php echo $widgetIdentity ?>"></div>
<?php endif; ?>  
    




