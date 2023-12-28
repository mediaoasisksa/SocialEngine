<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
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
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item,'thumb.profile') , array('class' => 'thumb_icon')) ?>
          </div>
          <div class="info">
            <div class="title icon">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              <?php if( $item->closed ): ?>
                <i class="bizlist_close_icon"></i>
              <?php endif ?>
            </div>
            <div class="stats">
              <?php echo $this->timestamp(strtotime($item->{$this->recentCol})) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="bizlists_list_widget_more">
      <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
        <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
          'url' => $this->url(array('action' => 'index'), 'bizlist_general', true),
          'param' => array('orderby' => 'creation_date')
          )); ?>
      <?php endif; ?>
    </div>
</div>
