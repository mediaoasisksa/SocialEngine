<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Elpis
 * @copyright  Copyright 2006-2022 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 2022-06-20
 */

?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Elpis/externals/styles/styles.css'); ?>

<div class="elpis_lp_blogs">
  <div class="elpis_lp_blogs_inner">
    <?php foreach( $this->paginator as $item ): ?>
    <div class="blogs_lp_main">
      <div class="blogs_photo"> <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.main')) ?> </div>
      <div class="info">
        <div class="title"> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </div>
        <div class="description"> <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 150) ?> </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
  <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'index'), 'blog_general', true),
        'param' => array('orderby' => 'view_count')
        )); ?>
  <?php endif; ?>
</div>
