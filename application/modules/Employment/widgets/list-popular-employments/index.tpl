<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="employment_list_widget">
  <ul>
    <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class="employment_list_widget_title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          <?php if( $item->closed ): ?>
            <i class="employments_close_icon"></i>
          <?php endif ?>
        </div>
        <div class="employment_list_widget_date">
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          - <?php echo $this->translate('posted by %1$s',
              $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
        </div>
        <div class="employment_list_widget_description">
          <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
          <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
   <div class="employment_list_widget_more"> 
    <?php echo $this->partial('_widgetLinks.tpl', 'core', array('url' => $this->url(array('action' => 'index'), 'employment_general', true), 'param' => array('orderby' => 'view_count'))); ?>
   </div>
  <?php endif; ?>
</div>
