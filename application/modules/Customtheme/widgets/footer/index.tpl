<?php ?>
    
    <footer>
      <div class="container">
        <div class="foot-All">
          <div class="logo-foot">
            <a href="/">
              <img src="./images/logo-consulto-update.png" alt="" />
            </a>
          </div>
          <div class="Contact-Us-Foot">
            <h4><?php echo $this->translate("CONTACT US");?></h4>
            <p>
              <i class="fas fa-map-marker-alt"></i>12534 Sahra, St. Arka Riyadh
            </p>
            <a href="tel:0581805441"
              ><p>
                <i class="fas fa-phone"></i> Phone: 0581805441
              </p></a
            >
            <a href="tel:0580125943"
              ><p>
                <i class="fas fa-phone"></i> Phone: 0580125943
              </p></a
            >
            <a href="mailto:info@consul2.com">
              <p><i class="fas fa-envelope"></i> Email: info@consul2.com</p>
            </a>
          </div>
          <div class="About-Foot">
            <h4><?php echo $this->translate("ABOUT");?></h4>
            <a href="/help/privacy" class="menu_core_footer core_footer_privacy" order="1" encodeurl="1"><?php echo $this->translate("Privacy");?></a>          
            <a href="/help/terms" class="menu_core_footer core_footer_terms" order="2" encodeurl="1"><?php echo $this->translate("Terms of Service");?></a>          
            <a href="/help/contact" class="menu_core_footer core_footer_contact" order="3" encodeurl="1"><?php echo $this->translate("Contact");?></a> 
          </div>
          <div class="Follow-Us-Foot">
            <h4><?php echo $this->translate("FOLLOW US");?></h4>
            <div class="LOGO-FOOT">
              <a href="http://www.instagram.com">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="http://www.facebook.com">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="http://www.youtube.com">
                <i class="fab fa-youtube"></i>
              </a>
              <a href="http://www.linkedin.com">
                <i class="fab fa-linkedin-in"></i>
              </a>
            </div>
          </div>
          <div class="English">
             <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showlanguage', 1) && 1 !== count($this->languageNameList) ): ?>
      <div class="sescompany_footer_lang sesbasic_clearfix">
        <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block" id="footer_language_<?php echo $this->identity; ?>">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => "setLanguage()"), $this->languageNameList) ?>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
      </div>
    <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="khat-foot"></div>
      <div class="txt-under-foot">
        <p>copyrights <i class="far fa-copyright"></i> 2022 MediaOasis</p>
      </div>
    
    </footer>
    <!-- End Footer -->
    <!-- new script import -->
 <!-- <script src="application/modules/Customtheme/externals/scripts/owl.carousel.min.js"></script> -->
        <?php // $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/scripts/jquery-3.6.0.min.js'); ?>
    <?php // $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/scripts/popper.min.js'); ?>


    <script type="text/javascript" src="application/modules/Customtheme/externals/scripts/owl.carousel.min.js"></script>
    <script type="text/javascript" src="application/modules/Customtheme/externals/scripts/slider.js"></script>
    <!--
        <?php // $this->footerScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/scripts/owl.carousel.min.js'); ?>
    <?php // $this->footerScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/scripts/slider.js'); ?>
    
    -->
    
    <!-- <?php // $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Customtheme/externals/scripts/bootstrap.min.js'); ?> -->

     <!-- <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
      integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
      crossorigin="anonymous"
    ></script> -->
    
    
    <script>
  function setLanguage() {
    scriptJquery('#footer_language_<?php echo $this->identity; ?>').submit();
  }
</script>
    

<style>
    .layout_page_footer {
        background-color: #cfd2d8;
    }
    
    .sescompany_footer_main h3 {
        color: #1e3869;
    }
    
    .sescompany_footer_links a {
        color: #fff;
    }
</style>
