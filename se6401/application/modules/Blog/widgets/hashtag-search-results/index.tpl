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
            <div class='blogs_browse_photo'> <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?> </div>
            <div class='blogs_browse_info'> <span class='blogs_browse_info_title'>
               <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
               </span>
               <p class='blogs_browse_info_date'> <?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($item->creation_date)) ?> <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?> </p>
               <div class="stat_info">
                  <?php if( $item->comment_count > 0 || $item->like_count > 0 ) :?>
                  <i class="fa fa-chart-bar"></i>
                  <?php endif; ?>
                  <?php if( $item->comment_count > 0 ) :?>
                  <span> <?php echo $this->translate(array('%s Comment', '%s Comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?> </span>
                  <?php endif; ?>
                  <?php if( $item->like_count > 0 ) :?>
                  <span> <?php echo $this->translate(array('%s Like', '%s Likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?> </span>
                  <?php endif; ?>
                  <?php if( $item->view_count > 0 ) :?>
                  <span class="views_blog"> <?php echo $this->translate(array('%s View', '%s Views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> </span>
                  <?php endif; ?>
               </div>
               <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'blog')); ?>
               <p class='blogs_browse_info_blurb'>
                  <?php $readMore = ' ' . $this->translate('Read More') . '...';?>
                  <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 110, $this->htmlLink($item->getHref(), $readMore) ) ?> </p>
            </div>
         </div>
      </div>
      <?php endforeach; ?>
   </div>
</div>
<?php else:?>
<div class="tip"> <span> <?php echo $this->translate('No one has written a blog entry yet.'); ?> </span> </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?> 
