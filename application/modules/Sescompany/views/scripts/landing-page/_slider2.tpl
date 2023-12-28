<?php ?>
<?php if(count($this->slides) > 0) { ?>
  <div class="lp_slider_section lp_section_box sesbasic_bxs">
    <section id="lp_slider" class="lp_slider_main_section sesbasic_clearfix">
      <div class="slider_section_center sesbasic_html_block wow animated fadeInDown">
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderheading')) { ?>
          <h2><?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderheading', 'World\'s Strongest Professional Network'); ?></h2>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderdescription')) { ?>
          <p><?php echo nl2br(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.sliderdescription', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. ')); ?></p>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtntext') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink')) { ?>
          <a href="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtnlink'); ?>" target="_blank" class="View_more_btn"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.slidermorebtntext')); ?></a>
        <?php } ?>
        <div class="container" id="container">
          <ul>
            <?php foreach($this->slides as $slide) { ?>
              <li>
                 <?php if($slide->file_id) { ?>
                    <?php $item = Engine_Api::_()->getItem('sescompany_slide', $slide->slide_id); ?>
                    <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                  <?php } ?>
              </li>
            <?php } ?>
            <img src="application/modules/Sescompany/externals/images/left.png" class="left">
            <img src="application/modules/Sescompany/externals/images/right.png" class="right">
          </ul>
        </div>
      </div>
    </section>
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
  </div>
<?php } ?>