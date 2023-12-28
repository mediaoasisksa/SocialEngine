<?php
/**
* SocialEngine
*
* @category   Application_Core
* @package    Core
* @copyright  Copyright 2006-2010 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
* @author     Jung
*/
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sescompany/externals/styles/styles.css'); ?>

<div id="slideshow_container"  class="sescompany_banner_container_wrapper sesbasic_bxs<?php if($this->fullwidth) { ?> isfull<?php } ?>" style="height:<?php echo $this->height; ?>px;">
  <div class="sescompany_banner_container" >
		<div class="sescompany_banner_img_container" style="height:<?php echo $this->height; ?>px;">
        <?php if( $this->banner->getPhotoUrl() ): ?>
          <img src="<?php echo $this->banner->getPhotoUrl()?>" />
				<?php endif;?>
		</div>
		<div class="sescompany_banner_content"  style="height:<?php echo $this->height; ?>px;">
    <div class="sescompany_banner_content_inner">
      <h2 class="sescompany_banner_title"><?php echo $this->translate($this->banner->getTitle()) ?></h2>
      <?php if( $this->banner->getDescription() ): ?>
      <p class="sescompany_banner_des"><?php echo $this->translate($this->banner->getDescription()) ?></p>
      <?php endif; ?>
      <?php if( $this->banner->getCTALabel() ): ?>
        <div class="sescompany_banner_btns">
          <a href="<?php echo $this->banner->getCTAHref() ?>" class="sescompany_banner_btn"><?php echo $this->translate($this->banner->getCTALabel()) ?></a>
        </div>
      <?php endif; ?>
		</div>
	</div>
</div>
<?php if($this->fullwidth) { ?>
	<script type="application/javascript">
  sesJqueryObject(document).ready(function(){
    sesJqueryObject('#global_content').css('padding-top',0);
    sesJqueryObject('#global_wrapper').css('padding-top',0);	
  });
  </script>
<?php } ?>