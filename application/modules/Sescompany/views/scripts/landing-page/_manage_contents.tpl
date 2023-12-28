<?php ?>
<?php if(count($this->contents) > 0) { ?>
  <?php $bgImage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.mngcontentsbgimage', ''); ?>
  <div class="lp_blogs_section lp_section_box sesbasic_bxs">
    <section id="lp_blogs" class="lp_blogs_box_main sesbasic_clearfix" style="background-image: url('<?php echo $bgImage; ?>');">
      <h2 class="wow animated fadeInUp"><?php echo $this->translate($this->contheading); ?></h2>
      <div class="lp_blogs_row sesbasic_clearfix">
        <?php foreach($this->contents as $content) { ?>
          <div class="lp_blogs_item wow animated fadeInUp">
          <a href="<?php echo $content->getHref(); ?>">
            <div class="lp_blogs_item_box">
            <div class="lp_blogs_item_content">
              <h3><?php echo $content->getTitle(); ?></h3>
              <?php $date = $content->creation_date;
                $date = date('F j, Y', strtotime($date)); ?>
              <p class="blog_date"><?php echo $date; ?></p>
              <p class="blog_author"><?php echo $this->translate("Author: <b>%s</b>", $content->getOwner()->getTitle()); ?></p>
            </div>
            <div class="lp_blogs_item_img">
              <img src="<?php echo $content->getPhotoUrl(); ?>" />
              <p class="blog_dis">
              <?php if(isset($content->description)) { ?>
                <?php echo strip_tags($content->description); ?>
              <?php } else if(isset($content->body)) { ?>
                <?php echo strip_tags($content->body); ?>
              <?php } ?>
              </p>
            </div>
            </a>
            </div>
          </div>
        <?php } ?>
      </div>
    </section>
  </div>
<?php } ?>