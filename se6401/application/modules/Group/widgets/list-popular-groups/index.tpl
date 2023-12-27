<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="generic_list_wrapper">
    <ul class="generic_list_widget">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile'), array('class' => 'thumb_icon')) ?>
          </div>
          <div class="info">
            <div class="title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <div class="stats">
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('led by %1$s',
                  $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
              <?php if( $this->popularType == 'view_count' ): ?>
                - <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              <?php elseif( $this->popularType == 'member_count' ): ?>
                - <?php echo $this->translate(array('%s member', '%s members', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>
              <?php elseif( $this->popularType == 'like_count' ): ?>
                - <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
              <?php elseif( $this->popularType == 'comment_count' ): ?>
                - <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>
              <?php endif; ?>
            </div>
             <?php
            $desc = trim($this->string()->truncate($this->string()->stripTags($item->description), 45));
            if( !empty($desc) ): ?>
            <div class="description">
              <?php echo $desc ?>
            </div>
          <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    
    <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
      <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'browse'), 'group_general', true),
        'param' => array('order' => 'member_count+DESC')
        )); ?>
    <?php endif; ?>
</div>
