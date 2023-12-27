<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  var pageAction =function(page){
    scriptJquery('#page').val(page);
    scriptJquery('#filter_form').submit();
  }
</script>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="blogs_manage list_wrapper">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='blogs_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
          </div>
          <div class='blogs_options'>
            <?php echo $this->htmlLink(array(
              'action' => 'edit',
              'blog_id' => $item->getIdentity(),
              'route' => 'blog_specific',
              'reset' => true,
            ), $this->translate('Edit Entry'), array(
              'class' => 'buttonlink icon_blog_edit',
            )) ?>
            <?php
            echo $this->htmlLink(array('route' => 'default', 'module' => 'blog', 'controller' => 'index', 'action' => 'delete', 'blog_id' => $item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Entry'), array(
              'class' => 'buttonlink smoothbox icon_blog_delete'
            ));
            ?>
          </div>
          <div class='blogs_info'>
            <span class='blogs_browse_info_title'>
              <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?><?php if(!empty($item->draft)) { ?><span class="drafts_label"><?php echo $this->translate("Draft")?></span><?php } ?></h3>
            </span>
            <p class='blogs_browse_info_date'>
              <?php echo $this->translate('Posted by');?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
              <?php echo $this->translate('about');?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                <?php if( $item->comment_count > 0 || $item->like_count > 0 ) :?>
                  <i class="fa fa-chart-bar"></i>
                <?php endif; ?>
                  <?php if( $item->comment_count > 0 ) :?>
                    <span>
                      <?php echo $this->translate(array('%s Comment', '%s Comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
                    </span>
                  <?php endif; ?>
                  <?php if( $item->like_count > 0 ) :?>
                    <span>
                      <?php echo $this->translate(array('%s Like', '%s Likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
                    </span>
                  <?php endif; ?>
                  <?php if( $item->view_count > 0 ) :?>
                    <span class="views_blog">
                      <?php echo $this->translate(array('%s View', '%s Views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
                    </span>
                  <?php endif; ?>
                  <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'blog')); ?>
            </p>
            <p class='blogs_browse_info_blurb'>
              <?php $readMore = ' ' . $this->translate('Read More') . '...';?>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 180, $this->htmlLink($item->getHref(), $readMore) ) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$swriting%2$s a new entry.', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
  )); ?>


<script type="text/javascript">
  scriptJquery('.core_main_blog').parent().addClass('active');
</script>
