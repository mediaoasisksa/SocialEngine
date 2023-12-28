<?php

?>
<?php if($this->chooselandingdesign == 1) { ?>
  <?php 
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sescompany/externals/styles/landing_page.css');
    $la1aboutshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1aboutshow', 1);
       $la1clientsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsshow', 1);
    $la1countershow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1countershow', 1);
    $la1featuresshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresshow', 1);
 
    $la1testimonialssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialssshow', 1);
    $la1contentssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1contentssshow', 1);
    $la2teamsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsshow', 1);
  ?>

  <div class="lp_section_box_main">
    <?php echo $this->content()->renderWidget('sescompany.banner-slideshow', array('banner_id' =>1, 'full_width' => 1,'height' => '800')) ?>
    <?php if($la1aboutshow && $this->abouts->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_abouts.tpl'; ?>
    <?php } ?>
    <?php if($la1countershow && $this->counters->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_counters.tpl'; ?>
    <?php } ?>
     <?php if($la1clientsshow && $this->clients->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_clients.tpl'; ?>
    <?php } ?>
    <?php if($la1featuresshow && $this->features->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_features.tpl'; ?>
    <?php } ?>
    <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_howitworks.tpl'; ?>
   <?php if($la2teamsshow && $this->teams->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_teams.tpl'; ?>
    <?php } ?>
        
    <?php if($la1testimonialssshow && $this->testimonials->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_testimonials.tpl'; ?> 
    <?php } ?>
   
    <?php if($la1contentssshow && count($this->contents) > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_manage_contents.tpl'; ?> 
    <?php } ?>
  </div>
  
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.rightsidenavigation', 1)) { ?>
    <div id="lp_nav" class="lp_nav_box_main sesbasic_clearfix">
      <ul>
        <li><a href="#slideshow_container_" class="active"><i class="fas fa-home"></i></a>
          <div class="lp_nav_tooltip"><?php echo $this->translate("Home"); ?><div class="tool-triangle"></div></div>
        </li>
        <?php if($la1aboutshow && $this->abouts->getTotalItemCount() > 0) { ?>
          <li><a href="#lp_about_us"><i class="fas fa-info-circle"></i></a>
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtheading', 'About Us')); ?><div class="tool-triangle"></div></div>
          </li>
           <?php } ?>
        <?php if($la1clientsshow && $this->clients->getTotalItemCount() > 0) { ?>
          <li><a href="#lp_client"><i class="fa fa-briefcase"></i></a> 
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1clientsheading', 'Our Clients')); ?><div class="tool-triangle"></div></div>
          </li>
        <?php } ?>
        <?php if($la1countershow && $this->counters->getTotalItemCount() > 0) { ?>
          <li><a href="#lp_statics"><i class="fas fa-chart-bar"></i></a>
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1countersheading', 'Statistics')); ?><div class="tool-triangle"></div></div>
          </li>
          
        <?php } ?>
        <?php if($la1featuresshow && $this->features->getTotalItemCount() > 0) { ?>
          <li><a href="#lp_reason"><i class="fas fa-gem"></i></a>
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresheading', 'Highlighted Features')); ?><div class="tool-triangle"></div></div>
          </li>
        <?php } ?>
          <?php if($la2teamsshow) { ?>
            <li> <a href="#lp_team"><i class="fa fa-user"></i></a>
             <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsheading', 'Our Teams')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
        <?php if($la1testimonialssshow && $this->testimonials->getTotalItemCount() > 0) { ?>
          <li><a href="#lp_testimonial"><i class="fas fa-comment-dots"></i></a>
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialsheading', 'Testimonials')); ?><div class="tool-triangle"></div></div>
          </li>
       
        <?php } ?>
        <?php if($la1contentssshow && count($this->contents) > 0) { ?>
          <li><a href="#lp_blogs"><i class="fa fa-address-card-o"></i></a>
            <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.contheading', 'Blog')); ?><div class="tool-triangle"></div></div>
          </li>
        <?php } ?>
      </ul>
    </div>
  <?php } ?>
  
  <script src="application/modules/Sescompany/externals/scripts/jquery.3.2.1.min.js" type="text/javascript"></script> 
  <script src="application/modules/Sescompany/externals/scripts/lity.js" type="text/javascript"></script> 
  <script type="text/javascript">
  // Select all links with hashes
  seslpObject321('a[href*="#"]')
    // Remove links that don't actually link to anything
    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function(event) {
      // On-page links
      if (
        location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
        && 
        location.hostname == this.hostname
      ) {
        // Figure out element to scroll to
        var target = seslpObject321(this.hash);
        target = target.length ? target : seslpObject321('[name=' + this.hash.slice(1) + ']');
        // Does a scroll target exist?
        if (target.length) {
          // Only prevent default if animation is actually gonna happen
          event.preventDefault();
          seslpObject321('html, body').animate({
            scrollTop: target.offset().top
          }, 1000, function() {
            // Callback after animation
            // Must change focus!
            var $target = seslpObject321(target);
            $target.focus();
            if ($target.is(":focus")) { // Checking if the target was focused
              return false;
            } else {
              $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
              $target.focus(); // Set focus again
            };
          });
        }
      }
    });
  </script>  
    
  <script src="application/modules/Sescompany/externals/scripts/wow.min.js" type="text/javascript"></script>
  <script src="application/modules/Sescompany/externals/scripts/jquery.1.11.1.min.js" type="text/javascript"></script> 

  <script src="application/modules/Sescompany/externals/scripts/main.js" type="text/javascript"></script>
  <script src="application/modules/Sescompany/externals/scripts/prrple.slider.js" type="text/javascript"></script>
  <script type="text/javascript">
    seslpObject(document).ready(function(){
      seslpObject('#sliderh3 .slider').prrpleSlider({
        csstransforms:			false,
        richSwiping:			false
      });	
    });
  </script>
  <!-- <script src="application/modules/Sescompany/externals/scripts/owl.carousel.js" type="text/javascript"></script> -->

<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/owl-carousel/jquery.js');
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/owl-carousel/owl.carousel.js');
?>

  <script type="text/javascript">

  sesowlJqueryObject(".lp_about_slider").owlCarousel({
    nav:true,
    dots:false,
    items:1,
    margin:10,
    responsiveClass:true,
    autoplay:false,
    responsive:{
      0:{
          items:1,
          autowidth:true,
      },
      600:{
          items:2,
      },
      1000:{
          items:3,
      },
    }
  });

    // seslpObject(document).ready(function() {
    //   var owl = seslpObject('.owl-carousel');
    //   owl.owlCarousel({
    //     items: 3,
    //     loop: false,
    //     margin: 10,
    //     autoplay: true,
    //     autoplayTimeout: false,
    //     autoplayHoverPause: true,
    //     responsiveClass: true,
    //     responsive: {
    //       0: {
    //         items: 1,
    //         nav: false
    //       },
    //       768: {
    //         items: 2,
    //         nav: false	
    //       },
    //       1000: {
    //         items: 3,
    //         nav: false,
    //         loop: false,
    //         margin: 20
    //       }
    //     }
    //   });
    //   var owl = seslpObject('.testimonial-slider');
    //   owl.owlCarousel({
    //     items: 1,
    //     loop: true,
    //     margin: 10,
    //     autoplay: true,
    //     autoplayTimeout: false,
    //     autoplayHoverPause: true
    //   });
    // })
  </script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js" type="text/javascript"></script> 
  <script src="application/modules/Sescompany/externals/scripts/jquery.counterup.min.js" type="text/javascript"></script> 
  <script type="text/javascript">
    seslpObject(document).ready(function( seslpObject ) {
      
      seslpObject('.counter').counterUp({
          delay: 10,
          time: 1000
      });
          
      var sections = seslpObject('.lp_section_box')
      , nav = seslpObject('#lp_nav');

      var nav_height = nav.outerHeight();
      seslpObject(window).on('scroll', function () {
        var cur_pos = seslpObject(this).scrollTop();
        sections.each(function() {
          var top = seslpObject(this).offset().top - nav_height,
              bottom = top + seslpObject(this).outerHeight();

          if (cur_pos >= top && cur_pos <= bottom) {
            nav.find('a').removeClass('active');
            sections.removeClass('active');
            seslpObject(this).addClass('active');
            nav.find('a[href="#'+seslpObject(this).find('section').attr('id')+'"]').addClass('active');
          }
        });
      });

      nav.find('a').on('click', function () {
        var $el = seslpObject(this)
          , id = $el.attr('href');

        seslpObject('html, body').animate({
          scrollTop: seslpObject(id).offset().top

        }, 500);

        return false;
      });
    });

    seslpObject(document).ready(function() {
        seslpObject(".slider_arrow_down").click(function(event){
           seslpObject('html, body').animate({scrollTop: '+=600px'}, 800);
        });
    });
  </script>
<?php } else if($this->chooselandingdesign == 2) { ?>
  <?php
    $la1contentssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1contentssshow', 1);
    $la1testimonialssshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialssshow', 1);
    $la2photosshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosshow', 1);
    $la2contactsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsshow', 1);
    $la2teamsshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsshow', 1);
    $la1featuresshow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresshow', 1);
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sescompany/externals/styles/landing_page_two.css'); 
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sescompany/externals/styles/simplelightbox.min.css'); 
  ?>

  <div class="lp_section_box_main">
    <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_slider2.tpl'; ?>

    <?php if($la1featuresshow && $this->features->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_features.tpl'; ?>
    <?php } ?>
     <?php if($la2photosshow && count($this->photos) > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_photos.tpl'; ?>
    <?php } ?>
    <?php if($la2teamsshow && $this->teams->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_teams.tpl'; ?>
    <?php } ?>
    <?php if($la1contentssshow) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_manage_contents_2.tpl'; ?>
    <?php } ?>
    <?php if($la1testimonialssshow && $this->testimonials->getTotalItemCount() > 0) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_testimonials.tpl'; ?>
    <?php } ?>
    
    <?php if($la2contactsshow) { ?>
      <?php include APPLICATION_PATH .  '/application/modules/Sescompany/views/scripts/landing-page/_contactus.tpl'; ?>
    <?php } ?>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.rightsidenavigation', 1)) { ?>
      <div id="lp_nav" class="lp_nav_box_main sesbasic_clearfix">
        <ul>
          <li> <a href="#lp_slider" class="active"><i class="fa fa-home"></i></a>
           <div class="lp_nav_tooltip"><?php echo $this->translate("Home"); ?><div class="tool-triangle"></div></div>
           
          </li>

          <?php if($la1featuresshow && $this->features->getTotalItemCount() > 0) { ?>
            <li><a href="#lp_reason"><i class="fa fa-diamond"></i></a>
              <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresheading', 'Highlighted Features')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
           <?php if($la2photosshow) { ?>
            <li> <a href="#lp_gallery"><i class="fa fa-picture-o"></i></a>
              <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2photosheading', 'Photo Gallery')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
          <?php if($la2teamsshow) { ?>
            <li> <a href="#lp_team"><i class="fa fa-user"></i></a>
             <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2teamsheading', 'Our Teams')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
           <?php if($la1contentssshow) { ?>
            <li> <a href="#lp_featured"><i class="fa fa-address-card-o"></i></a>
              <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresheading', 'Highlighted Features')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
          <?php if($la1testimonialssshow) { ?>
            <li> <a href="#lp_testimonial"><i class="fa fa-commenting"></i></a>
              <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialsheading', 'Testimonials')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
         <?php if($la2contactsshow) { ?>
            <li> <a href="#lp_contact"><i class="fa fa-map-marker"></i></a>
              <div class="lp_nav_tooltip"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la2contactsheading', 'Contact Us')); ?><div class="tool-triangle"></div></div>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    
    <script src="application/modules/Sescompany/externals/scripts/jquery.3.2.1.min.js" type="text/javascript"></script> 
    <script type="text/javascript">
       // Select all links with hashes
       sesJqueryObject('a[href*="#"]')
           // Remove links that don't actually link to anything
           .not('[href="#"]')
           .not('[href="#0"]')
           .click(function(event) {
               // On-page links
               if (
                   location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
                   location.hostname == this.hostname
               ) {
                   // Figure out element to scroll to
                   var target = $(this.hash);
                   target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                   // Does a scroll target exist?
                   if (target.length) {
                       // Only prevent default if animation is actually gonna happen
                       event.preventDefault();
                       sesJqueryObject('html, body').animate({
                           scrollTop: target.offset().top
                       }, 1000, function() {
                           // Callback after animation
                           // Must change focus!
                           var $target = $(target);
                           $target.focus();
                           if ($target.is(":focus")) { // Checking if the target was focused
                               return false;
                           } else {
                               $target.attr('tabindex', '-1'); // Adding tabindex for elements not focusable
                               $target.focus(); // Set focus again
                           };
                       });
                   }
               }
           });
    </script> 
    <script src="application/modules/Sescompany/externals/scripts/jquery.1.11.1.min.js" type="text/javascript"></script>
    <script src="application/modules/Sescompany/externals/scripts/wow.min.js" type="text/javascript"></script> 
    <script src="application/modules/Sescompany/externals/scripts/main.js" type="text/javascript"></script> 
    <script src="application/modules/Sescompany/externals/scripts/prrple.slider.js" type="text/javascript"></script> 
    <script src="application/modules/Sescompany/externals/scripts/simple-lightbox.min.js" type="text/javascript"></script> 
    <script>
       sesJqueryObject(function() {

           var gallery = seslpObject('.gallery a').simpleLightbox({
               navText: ['&lsaquo;', '&rsaquo;']
           });
       });
    </script> 
    <script src="application/modules/Sescompany/externals/scripts/Carousel.js" type="text/javascript"></script>
    <script type="text/javascript">
       seslpObject(document).ready(function() {
           seslpObject('.container').carousel({
               num: 5,
               maxWidth: 750,
               maxHeight: 550,
               distance: 100,
               scale: 0.8,
               animationTime: 1000,
               showTime: 4000
           });
       });
    </script>
    <script>
       function openPics(evt, picsName) {
           var i, x, tabactives;
           x = document.getElementsByClassName("pics");
           for (i = 0; i < x.length; i++) {
               x[i].style.display = "none";
           }
           tabactives = document.getElementsByClassName("tabactive");
           for (i = 0; i < x.length; i++) {
               tabactives[i].className = tabactives[i].className.replace(" tabcurrent", "");
           }
           document.getElementById(picsName).style.display = "block";
           evt.currentTarget.className += " tabcurrent";
       }
    </script> 
    <script type="text/javascript">
       seslpObject(document).ready(function() {
           seslpObject('#sliderh3 .slider').prrpleSlider({
               csstransforms: false,
               richSwiping: false
           });
       });
    </script> 
    <script src="application/modules/Sescompany/externals/scripts/owl.carousel.js" type="text/javascript"></script> 
    <script type="text/javascript">
       seslpObject(document).ready(function() {
           var owl = seslpObject('.owl-carousel');
           owl.owlCarousel({
               items: 3,
               loop: false,
               margin: 10,
               autoplay: true,
               autoplayTimeout: false,
               autoplayHoverPause: true,
               responsiveClass: true,
               responsive: {
                   0: {
                       items: 1,
                       nav: false
                   },
                   768: {
                       items: 2,
                       nav: false
                   },
                   1000: {
                       items: 3,
                       nav: false,
                       loop: false,
                       margin: 20
                   }
               }
           });
           var owl = seslpObject('.testimonial-slider');
           owl.owlCarousel({
               items: 1,
               loop: true,
               margin: 10,
               autoplay: true,
               autoplayTimeout: false,
               autoplayHoverPause: true
           });
           var owl = seslpObject('.featured-slider');
           owl.owlCarousel({
               items: 1,
               loop: true,
               margin: 10,
               autoplay: true,
               autoplayTimeout: false,
               autoplayHoverPause: true
           });
       })
    </script> 
    <script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js" type="text/javascript"></script> 
    <script src="application/modules/Sescompany/externals/scripts/jquery.counterup.min.js" type="text/javascript"></script> 
    <script type="text/javascript">
       seslpObject(document).ready(function(seslpObject) {
           seslpObject('.counter').counterUp({
               delay: 10,
               time: 1000
           });

           var sections = seslpObject('.lp_section_box'),
               nav = seslpObject('#lp_nav');

           var nav_height = nav.outerHeight();
           seslpObject(window).on('scroll', function() {
               var cur_pos = seslpObject(this).scrollTop();
               sections.each(function() {
                   var top = seslpObject(this).offset().top - nav_height,
                       bottom = top + seslpObject(this).outerHeight();

                   if (cur_pos >= top && cur_pos <= bottom) {
                       nav.find('a').removeClass('active');
                       sections.removeClass('active');
                       seslpObject(this).addClass('active');
                       nav.find('a[href="#' + seslpObject(this).find('section').attr('id') + '"]').addClass('active');
                   }
               });
           });

           nav.find('a').on('click', function() {
               var $el = seslpObject(this),
                   id = $el.attr('href');

               seslpObject('html, body').animate({
                   scrollTop: seslpObject(id).offset().top

               }, 500);

               return false;
           });
       });
    </script> 
<?php } ?>