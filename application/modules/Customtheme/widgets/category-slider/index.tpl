
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Hpbblock/externals/styles/styles.css'); ?>

<div class="categories_slider_container">
	<div class="categories_slider">
  	<div class="categories_slider_item item_1">
      <a href="https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211201-WA0003.jpg" alt="" height="400px;"/></div>
          
        </article>
      </a>
    </div>
    <div class="categories_slider_item item_2">
      <a href="https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211201-WA0004.jpg" alt="" height="400px;" /></div>
         
        </article>
      </a>
    </div>
    <div class="categories_slider_item item_3">
      <a href="https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211201-WA0005.jpg" alt="" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
    <div class="categories_slider_item item_4">
      <a href="https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211226-WA0002.jpg" height="400px;" /></div>
          
        </article>
      </a>
    </div>
  
  
  <div class="categories_slider_item item_5">
      <a href="https://us05web.zoom.us/j/5929017972?pwd=WmVPSmtJQ0ppZ1VZR1VPY2hoMGpKQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/1.png" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
      <div class="categories_slider_item item_6">
      <a href="https://us02web.zoom.us/j/81357630419?pwd=cmduWkpRQnJpN29jVGRiNmpWQ09tQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/2.png" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
      <div class="categories_slider_item item_7">
      <a href="https://zoom.us/j/7568151820?pwd=MzZsWjJPSVVtSFRTLzdhNEs2WDNOQT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/3.png" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
      <div class="categories_slider_item item_8">
      <a href="https://us05web.zoom.us/j/84438320310?pwd=a1c4VmY4dUNkeUZOY0c2b25SMzk5QT09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211229-WA0010.jpg" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
      <div class="categories_slider_item item_9">
      <a href="https://us05web.zoom.us/j/82878448465?pwd=bzNSTEZxQjA2S3p2K096c01uaGNuZz09">
        <article>
          <div class=""><img src="application/modules/Hpbblock/externals/images/categories/IMG-20211229-WA0011.jpg" height="400px;" /></div>
          
        </article>
      </a>
    </div>
    
    
  </div>
</div>

<?php 
  $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hpbblock/externals/scripts/jquery.js')
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hpbblock/externals/scripts/owl.carousel.js'); 
?>
<script type="application/javascript">
  sespageJqueryObject('.categories_slider').owlCarousel({
    <?php 
    $orientation = ($this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr');
    if($orientation == 'rtl') { ?>
      rtl:true,
    <?php }?>
    center: false,
    nav :true,
    loop:true,
    items:1,
    dots:true,
    autoplay:true,
    center:true,
    margin:5
  })
  sespageJqueryObject(".owl-prev").html('<i class="fa fa-angle-left"></i>');
  sespageJqueryObject(".owl-next").html('<i class="fa fa-angle-right"></i>');
</script>