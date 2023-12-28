<?php ?>
<?php if(count($this->abouts) > 0) { ?>
  <div class="lp_aboutus_section lp_section_box sesbasic_bxs">
    <section id="lp_about_us" class="lp_about_main_section sesbasic_clearfix">
      <div class="lg_about_us_banner wow animated fadeInUp animated" data-animate="fadeInUp-parent">
        <h2><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtheading', 'About Us')); ?></h2>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtvideourl')) { ?>
          <div class="lp_about_icon"><a class="hvr-pop" href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtvideourl', ''); ?>" data-lity><i class="fa fa-play"></i></a></div>
        <?php } ?>
      </div>
    </section>
    <style>
		.lp_about_main_section {
       background-image: url("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtbgimage1', ''); ?>");
     }
		 .lp_about_us_main {
       background-image: url("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1abtbgimage2', ''); ?>");
     }
		</style>
    <div class="lp_about_us_main">
    <h1 style="color: #fff;text-align: center;padding: 0px 0px 20px 0px;font-weight: 300;">How it works !</h1>
      <div class="about_us_block">
        <div class=" owl-theme lp_about_slider">
          <?php $i= 0; ?>
          <?php foreach($this->abouts as $about) { ?>
            <?php $datadelay = 0.3 * $i; ?>
            <div class="item wow animated fadeInUp" data-wow-delay="<?php echo $datadelay ?>s">
              <div class="lp_about_us_intro">
                <div class="lp_about_us_intro_banner">
                  <?php if($about->file_id) { ?>
                    <?php $item = Engine_Api::_()->getItem('sescompany_about', $about->about_id); ?>
                    <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                  <?php } ?>
                </div>
                <div class="lp_about_us_intro_content">
                  <?php if($about->font_icon) { ?>
                    <div class="about_us_cont_icon _icon_skill">
                      <i class="<?php echo $about->font_icon; ?>"></i>
                    </div>
                  <?php } ?>
                  <h3><?php echo $about->about_name; ?></h3>
                  <?php if($about->description) { ?>
                    <p><?php echo $about->description; ?></p>
                  <?php } ?>
                  <?php if($about->readmore_button_name && $about->readmore_button_link) { ?>
                    <a href="<?php echo $about->readmore_button_link; ?>" target="_blank" class="lp_about_us_intro_readmore"><?php echo $about->readmore_button_name; ?></a>
                  <?php } ?>
                </div>
              </div>
            </div>
          <?php $i++; } ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>