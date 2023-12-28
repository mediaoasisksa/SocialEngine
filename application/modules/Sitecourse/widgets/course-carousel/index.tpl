<?php $difficulty_levels = array(0=>'Beginner',1=>'Intermediate',2=>'Expert');
?>
<?php
$content_id = $this->identity;
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
->prependStylesheet($baseUrl . 'application/modules/Sitecourse/externals/styles/owl-carousel.css')
->prependStylesheet($baseUrl . 'application/modules/Sitecourse/externals/styles/owl.carousel.min.css')
->prependStylesheet($baseUrl . 'application/modules/Sitecourse/externals/styles/owl.theme.default.min.css');
$this->headScript()
->appendFile($baseUrl . 'application/modules/Sitecourse/externals/scripts/owl.carousel.js');
?>
<?php $this->isCarousel = true; ?>
<?php $this->carouselClass = 'categorizedCourseCarousel'.$content_id; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitecourse/views/scripts/_pageView.tpl'; ?>
<script>
  window.addEventListener('DOMContentLoaded', function () {
   var owl = scriptJquery('.categorizedCourseCarousel<?php echo $content_id; ?>').owlCarousel({
    responsiveClass: true,
    autoplay: true,
    autoplayTimeout: <?php echo $this->carouselSpeed;?>,
    responsive: {
      0: {
        items: 1,
        nav: true
      },
      479: {
        items: <?php echo $this->tabItem; ?>,
        nav: false
      },
      768: {
        items: <?php echo $this->deskItem; ?>,
        loop: false,
        margin: 20,
      }
    },
    dots: false,
    nav: true,
    tooltip: true,
  });
  scriptJquery(window).bind("load", function() {
    if(scriptJquery('.owl-prev')[0] && scriptJquery('.owl-next')[0]) {
      scriptJquery('.owl-prev')[0].setAttribute('title', 'prev')
      scriptJquery('.owl-next')[0].setAttribute('title', 'next')
    }
  });
})
</script>
