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
              <?php echo $this->timestamp(strtotime($item->{$this->recentCol})) ?>
              - <?php echo $this->translate('led by %1$s',
                  $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
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
        'param' => array('order' => 'creation_date+DESC')
        )); ?>
    <?php endif; ?>
</div>

