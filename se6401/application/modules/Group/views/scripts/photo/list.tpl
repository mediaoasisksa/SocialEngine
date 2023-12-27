<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: list.tpl 9987 2013-03-20 00:58:10Z john $
 * @author	   John
 */
?>
<div class="layout_middle">
  <div class="generic_layout_container layout_core_content">
    <h2>
      <?php echo $this->group->__toString() ?>
      <?php echo $this->translate('&#187; Photos');?>
    </h2>

    <?php if( $this->canUpload ): ?>
      <div class="group_photos_list_options">
        <?php echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $this->subject()->getGuid(),
          ), $this->translate('Upload Photos'), array(
            'class' => 'buttonlink icon_group_photo_new'
        )) ?>
      </div>
    <?php endif; ?>

    <div class="container no-padding">
      <div class="row group_photos_list">
        <?php foreach( $this->paginator as $photo ): ?>
          <div class="col-lg-3 col-6 grid_outer">
            <div class="grid_wrapper albums_block">
              <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
                <?php echo $this->itemBackgroundPhoto($photo, 'thumb.main')?>
              </a>
            </div>
          </div>
        <?php endforeach;?>
      </div>
    </div>
    <?php if( $this->paginator->count() > 0 ): ?>
      <?php echo $this->paginationControl($this->paginator); ?>
    <?php endif; ?>
  </div>
</div>

<script type="text/javascript">
  scriptJquery('.core_main_group').parent().addClass('active');
</script>
