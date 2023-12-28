<?php ?>
<?php if($this->testimonials->getTotalItemCount() > 0) { ?>
  <div class="lp_testimonial_section lp_section_box sesbasic_bxs">
    <section id="lp_testimonial" class="lp_testimonial_box_main sesbasic_clearfix">
    <h2><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1testimonialsheading', 'Testimonials')); ?></h2>
      <div class="lp_testimonial_bg"></div>
      <div class="lp_testimonial_slider_row wow animated fadeInUp">
        <div class="testimonial-slider owl-theme">
          <?php foreach($this->testimonials as $testimonial) { ?>
            <div class="item">
              <div class="testimonial_itme">
                <div class="lp_testimonial_avatar">
                  <?php if($testimonial->file_id) { ?>
                    <?php $item = Engine_Api::_()->getItem('sescompany_testimonial', $testimonial->testimonial_id); ?>
                    <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                  <?php } else { ?>
                    <img src="application/modules/User/externals/images/nophoto_user_thumb_icon.png" />
                  <?php } ?>
                </div>
                <div class="lp_testimonial_content">
                  <p class="testimonial_dis"><?php echo $testimonial->description; ?></p>
                  <?php if($testimonial->owner_name) { ?>
                    <p class="testimonial_avatar_name"><?php echo $testimonial->owner_name; ?></p>
                  <?php } ?>
                  <?php if($testimonial->designation) { ?>
                    <p class="testimonial_avatar_tittle">(<?php echo $testimonial->designation; ?>)</p>
                  <?php } ?>
              </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
  </div>
<?php } ?>