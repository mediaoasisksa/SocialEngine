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
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div class='container no-padding'>
    <div class='row'>
      <?php foreach( $this->paginator as $item ): ?>
        <div class='col-lg-4 col-md-6 blogs_browse'>
          <div class='blogs_browse_inner'>
            <div class='blogs_browse_photo'>
              <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
            </div>
            <div class='blogs_browse_info'>
              <span class='blogs_browse_info_title'>
                <h3>
                  <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                </h3>
              </span>
              <p class='blogs_browse_info_date'>
                <?php echo $this->translate('Posted');?>
                <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                <?php echo $this->translate('by');?>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
                <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'blog')); ?>
              </p>
              <p class='blogs_browse_info_blurb'>
                <?php $readMore = ' ' . $this->translate('Read More') . '...';?>
                <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 110, $this->htmlLink($item->getHref(), $readMore) ) ?>
              </p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php elseif( $this->category || $this->show == 2 || $this->search ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has written a blog entry with that criteria.');?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has written a blog entry yet.'); ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues,
//'params' => $this->formValues,
)); ?>
