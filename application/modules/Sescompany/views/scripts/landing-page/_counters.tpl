<?php ?>
<?php if($this->counters->getTotalItemCount() > 0) { ?>
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1cntbgimage', ''); ?>
  <div class="lp_statics_box lp_section_box sesbasic_bxs">
    <section id="lp_statics" class="lp_statics_box_main sesbasic_clearfix">
      <div class="lp_statics_box_bg" style="background-image: url(<?php echo $bgImage; ?>);">
        <div class="lp_statics_box_row sesbasic_clearfix">
          <?php $i= 0; ?>
          <?php foreach($this->counters as $counter) { ?>
            <?php $datadelay = 0.3 * $i; ?>
            <div class="lp_statics_box_item wow animated fadeInUp" data-wow-delay="<?php echo $datadelay ?>s">
              <i>
                <?php if($counter->file_id) { ?>
                  <?php $item = Engine_Api::_()->getItem('sescompany_counter', $counter->counter_id); ?>
                  <img src="<?php echo $item->getFilePath('file_id'); ?>" />
                <?php } ?>
              </i>
              <?php if($counter->counter_value) { ?>
                <h3 class="counter"><?php echo $counter->counter_value; ?></h3>
              <?php } ?>
              <?php if($counter->counter_name) { ?>
                <p><?php echo $counter->counter_name; ?></p>
              <?php } ?>
            </div>
          <?php $i++; } ?>
        </div>
        <div class="lp_statics_title wow animated fadeInUp">
          <h2></h2>
          <p></p>
        </div>
      </div>
    </section>
  </div>
<?php } ?>