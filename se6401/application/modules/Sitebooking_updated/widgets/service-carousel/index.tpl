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
<style type="text/css">
  .title {
    white-space: nowrap; 
    width: 150px; 
    overflow: hidden;
    text-overflow: ellipsis; 
  }
</style>

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
            items: 2,
          },
          1000: {
            items: 4,
            loop: false,
            dots: true
          }
        }
      })
    })
  });
</script>

<div class="owl-carousel owl-theme" id="owl-carousel-<?php echo $widgetIdentity ?>">

  <?php foreach ($this->paginator as $item): ?>

    <div class='item services'>

      <div class="_thumb">
        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.main')) ?>
      </div>

      <div class="_info">
		    <div class="_top">
          <span class="_logo">
            
            <?php if(!empty($item->provider_photo_id)) :?>

              <?php $url = Engine_Api::_()->storage()->get($item->provider_photo_id)->getPhotoUrl();?>
                
              <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $url style='max-width:100%'>")
              ?>

            <?php else: ?>

              <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/default_provider_profile.png" ?> 

              <?php echo $this->htmlLink(array('action' => 'view','user_id' => $item->owner_id,'pro_id' => $item->parent_id,'route' => 'sitebooking_provider_view','reset' => true,'slug' => $item->provider_slug),"<img src = $src style='max-width:100%'>")
              ?>

            <?php endif; ?>

          </span>

    		  <span><?php echo $this->translate('Created by'); ?></span>
            <?php echo $this->htmlLink(array('action' => 'view', 'user_id' => $item->owner_id, 'pro_id' => $item->parent_id, 'route' => 'sitebooking_provider_view', 'reset' => true, 'slug' => $item->provider_slug), $item->provider_title) ?>
    		</div>

  		  <div class="_bottom">
          
          <?php 

            // SHOW CATEGORY
            if($item->category_id != 0) {
              $category = Engine_Api::_()->getItem('sitebooking_category',$item->category_id);
              $category_name = $category["category_name"];
            } 
              
            if($item->first_level_category_id != 0) {
              $category = Engine_Api::_()->getItem('sitebooking_category',$item->first_level_category_id);
              $first_level_category_name = $category["category_name"];
            } else {
              $first_level_category_name = 0;
            } 
              
            if($item->second_level_category_id != 0) {
              $category = Engine_Api::_()->getItem('sitebooking_category',$item->second_level_category_id);
              $second_level_category_name = $category["category_name"];
            } else {
              $second_level_category_name = 0;
            } 
            
          ?>
          
          <!-- category_name -->
          <?php if($category_name != '' && $first_level_category_name == '' && $second_level_category_name == ''): ?>
            <div class="_category">
              <?php
                    echo $category_name; 
              ?>
            </div> 
          <?php endif;?>  

         <!-- first_level_category_name -->
          <?php if($first_level_category_name != '' && $second_level_category_name == ''): ?>
            <div class="_category">
              <?php
                    echo $first_level_category_name; 
              ?>
            </div> 
          <?php endif;?> 

          <!-- second_level_category_name -->
          <?php if($second_level_category_name != ''): ?>
            <div class="_category">
              <?php
                    echo $second_level_category_name;
              ?>
            </div>
          <?php endif;?> 

		      <h3 class="_title" ><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>

          <div class="_price"><?php echo $this->locale()->toCurrency($item['price'],Engine_Api::_()->getApi('settings', 'core')->getSetting("sitebooking.unit",'USD')); ?> / <?php echo Engine_Api::_()->getApi('Core', 'sitebooking')->showServiceDuration($item->duration); ?></div>

          <!-- RATING -->
          <div id="sitebooking_rating" class="_rating">
            <span id="service_carousel_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_1" class="rating_star_big_generic"> </span>
            <span id="service_carousel_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_2" class="rating_star_big_generic"> </span>
            <span id="service_carousel_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_3" class="rating_star_big_generic"></span>
            <span id="service_carousel_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_4" class="rating_star_big_generic" ></span>
            <span id="service_carousel_rate<?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>_5" class="rating_star_big_generic" ></span>
          </div>

          <script type="text/javascript">
            en4.core.runonce.add( function() {
              var rating = "<?php echo $item->rating;?>";
            
              for(var x=1; x<=parseInt(rating); x++) {
                  
                var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
                id = "service_carousel_rate"+id+"_"+x;

                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big');
              }

              var remainder = Math.round(rating)-rating;

              for(var x=parseInt(rating)+1; x<=5; x++) {
                  
                var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
                id = "service_carousel_rate"+id+"_"+x;
                document.getElementById(id).set('class', 'rating_star_big_generic rating_star_big_disabled');
              }

              if (remainder <= 0.5 && remainder !=0){

                var id = <?php echo $item->getIdentity() ?><?php echo $this->widgetIdentity ?>;
                var last = parseInt(rating)+1;
                id = "service_carousel_rate"+id+"_"+last;
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