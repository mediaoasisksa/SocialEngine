<?php ?>
<?php if($this->features->getTotalItemCount() > 0) { ?>
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1fetbgimage', ''); ?>
  <div class="lp_reason_section lp_section_box sesbasic_bxs">
    <section id="lp_reason" class="lp_reason_box_main sesbasic_clearfix" style="background-image: url(<?php echo $bgImage ?>);">
      <h2 class="wow animated fadeInUp"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.la1featuresheading', 'Highlighted Features')); ?></h2>
      <div class="lp_reason_row sesbasic_clearfix">
        <?php $i= 0; ?>
        <?php foreach($this->features as $feature) { ?>
          <?php $datadelay = 0.3 * $i; ?>
          <div class="lp_reason_item wow animated fadeInUp" data-wow-delay="<?php echo $datadelay ?>s">
            <span>
              <?php if($feature->file_id) { ?>
                <?php $item = Engine_Api::_()->getItem('sescompany_feature', $feature->feature_id); ?>
                <img src="<?php echo $item->getFilePath('file_id'); ?>" />
              <?php } ?>
            </span>
            <div class="lp_reason_content">
              <?php if($feature->feature_name) { ?>
              <h3><?php echo $feature->feature_name; ?></h3>
              <?php } ?>
              <?php if($feature->description) { ?>
                <p><?php echo $feature->description; ?></p>
              <?php } ?>
            </div>
          </div>
        <?php $i++; } ?>
      </div>
    </section>
  </div>
<?php } ?>