<?php ?>
<div class="lp_slider_section lp_section_box sesbasic_bxs">
	<section id="lp_slider" class="lp_slider_main_section sesbasic_clearfix">
  		<div class="slider_row sesbasic_clearfix">
        <div class="slider_section_right">
          <div class="slider_container" id="sliderh3">
            <div class="slider">
              <div class="slider_area wow animated fadeInDown" data-wow-delay="0.18s">
                <div class="slides">
                  <?php $i = 1; ?>
                  <?php foreach($this->slides as $slide) { ?>
                    <div class="slide">
                      <div class="slideiamge slide<?php echo $i; ?>"></div>
                    </div>
                  <?php $i++; } ?>
                </div>
              </div>
              <?php $j = 1; ?>
              <?php foreach($this->slides as $slide) { ?>
                <?php $datadelay = 0.3 * $j; ?>
                <div class="slider_bg_img_<?php echo $j ?>">
                  <span class="wow animated fadeInDown" data-wow-delay="<?php echo $datadelay ?>s"></span>
                </div>
              <?php $j++; } ?>
            </div>
          </div>
				</div>
        <div class="slider_section_left sesbasic_html_block wow animated fadeInDown">
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderheading')) { ?>
            <h2><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderheading', 'World\'s Strongest Professional Network'); ?></h2>
          <?php } ?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderdescription')) { ?>
            <p><?php echo nl2br(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderdescription', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ')); ?></p>
          <?php } ?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtntext') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink')) { ?>
            <a href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink'); ?>" target="_blank" class="View_more_btn"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtntext')); ?></a>
          <?php } ?>
        </div>
      </div>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidersharelink', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderfacebooklink', '') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidertwitterlink', '') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidergooglelink', '')) { ?>
        <div class="slider_social_share sesbasic_clearfix">
          <ul>
            <li><?php echo $this->translate("Follow Us"); ?></li>
            <li><a href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderfacebooklink', ''); ?>" target="_blank"><i class="fa fa-facebook"></i> <span><?php echo $this->translate("Facebook"); ?></span></a></li>
            <li><a href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidertwitterlink', ''); ?>" target="_blank"><i class="fa fa-twitter"></i> <span><?php echo $this->translate("Twitter"); ?></span></a></li>
            <li><a href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidergooglelink', ''); ?>" target="_blank"><i class="fa fa-google-plus"></i> <span><?php echo $this->translate("Google+"); ?></span></a></li>
          </ul>
        </div>
      <?php } ?>
      <div class="slider_arrow_down">
      	<a href="#"><i class="fa fa-angle-down"></i></a>
      </div>
  </section>
</div>
<style>
  <?php $g = 1; ?>
  <?php foreach($this->slides as $slide) { ?>
  <?php $item = Engine_Api::_()->getItem('sescompany_slide', $slide->getIdentity()); ?>
  .slide .slideiamge.slide<?php echo $g; ?> {
    background-image: url("<?php echo $item->getFilePath('file_id'); ?>");
  }
  .slider_bg_img_<?php echo $g ?> span {
    background-image: url("<?php echo $item->getFilePath('file_id'); ?>");
  }
  <?php $g++; } ?>
</style>