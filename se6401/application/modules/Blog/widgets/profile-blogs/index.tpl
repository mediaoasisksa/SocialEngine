<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<div class='container no-padding'>
  <div class='row'>
   <?php foreach( $this->paginator as $item ): ?>
    <div class='col-lg-4 col-md-6 blogs_browse'>
      <div class='blogs_browse_inner'>
        <div class='blogs_browse_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
        </div>
        <div class='blogs_browse_info'>
          <p class='blogs_browse_info_title'>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?><?php if(!empty($item->draft)) { ?><span class="drafts_label"><?php echo $this->translate("Draft")?></span><?php } ?>
          </p>
          <p class='blogs_browse_info_date'>
            <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->creation_date) ?>
            <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'blog')); ?>
          </p>
          <p class='blogs_browse_info_blurb'>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body),110) ?>
          </p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
 </div>
</div>

<?php
  // show view all link even if all are listed
  if( $this->paginator->count() > 0 ):
?>
  <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'blog_view'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
<?php endif;?>
