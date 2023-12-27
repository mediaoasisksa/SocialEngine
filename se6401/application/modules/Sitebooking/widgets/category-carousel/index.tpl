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
      var jq = jQuery.noConflict();
      j_q('#owl-carousel-<?php echo $widgetIdentity ?>').owlCarousel({
        loop: false,
        margin: 10,
        responsiveClass: true,
        nav: true,
        navContainer: j_q('#owl-carousel-nav-<?php echo $widgetIdentity ?>'),
        responsive: {
          0: {
            items: 2,
          },
          600: {
            items: 3,
          },
          1000: {
            items: 5,
            loop: false,
            dots: true
          }
        }
      })
    })
  });
</script>

<div class="owl-carousel owl-theme" id="owl-carousel-<?php echo $widgetIdentity ?>">

  <?php foreach( $this->paginator as $item ): ?>

    <div class='item category'>

      <div class="_thumb">

        <?php if(!empty($item->photo_id)) :?>

          <?php $url = Engine_Api::_()->storage()->get($item->photo_id)->getPhotoUrl();?>
          <?php echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','category' => $item->category_id,'reset' => true),"<img src = $url alt = 'Image Not Loaded'>")
          ?>

        <?php else: ?>

          <?php if($item->category_id <= 13):?>
            <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/categoryCarousel/cat"."$item->category_id".".png" ?>
          <?php else: ?>
            <?php $src = $this->layout()->staticBaseUrl . "application/modules/Sitebooking/externals/images/categoryCarousel/cat_default.png" ?>
          <?php endif; ?>    

          <?php echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','category' => $item->category_id,'reset' => true),"<img src = $src alt = 'Image Not Loaded'>")
                  ?>

        <?php endif; ?>

      </div>    

      <?php if($this->compare == "serviceHome"): ?>
        <div class="_info">
            <h3 class="_title"><?php echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_service_browse','category' => $item->category_id,'reset' => true),  $this->translate($item->category_name)) ?>
            </h3>
        </div>
      <?php endif;?>

      <?php if($this->compare == "providerHome"): ?>
        <div class="_info">
            <h3 class="_title"><?php echo $this->htmlLink(array('action' => 'index','route' => 'sitebooking_provider_general','category' => $item->category_id,'reset' => true),  $this->translate($item->category_name)) ?>
            </h3>
        </div>
      <?php endif;?>

    </div>  
  <?php endforeach; ?>
</div>

<div id="owl-carousel-nav-<?php echo $widgetIdentity ?>"></div> 







