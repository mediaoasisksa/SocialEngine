<?php ?>
<?php if(count($this->contents) > 0) { ?> 
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.mngcontentsbgimage', ''); ?>
  <div class="lp_featured_section lp_section_box sesbasic_bxs">
    <section id="lp_featured" class="lp_featured_box_main sesbasic_clearfix" style="background: url('<?php echo $bgImage; ?>');">
      <h2><?php echo $this->translate($this->contheading); ?></h2>
      <div class="lp_featured_slider_row wow animated fadeInUp">
        <div class="featured-slider owl-theme">
          <?php foreach($this->contents as $content) { ?>
            <div class="item">
              <div class="feaured_item wow zoomIn">
                <div class="featured-img">
                  <img src="<?php echo $content->getPhotoUrl(); ?>">
                </div>
                <div class="featured-details">
                  <h2><?php echo $content->getTitle(); ?></h2>
                  <p>
                    <?php if(isset($content->description)) { ?>
                      <?php echo strip_tags($content->description); ?>
                    <?php } else if(isset($content->body)) { ?>
                      <?php echo strip_tags($content->body); ?>
                    <?php } ?>
                  </p>
                  <a href="<?php echo $content->getHref(); ?>"><?php echo $this->translate("View Details"); ?></a> 
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
  </div>
<?php } ?>