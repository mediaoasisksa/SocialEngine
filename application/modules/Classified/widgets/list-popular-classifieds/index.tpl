<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="generic_list_wrapper">
    <ul class="generic_list_widget generic_list_widget_large_photo">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class="photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile'), array('class' => 'thumb')) ?>
          </div>
          <div class="info">
            <div class="title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              <?php if( $item->closed ): ?>
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Classified/externals/images/close.png' />
              <?php endif ?>
            </div>
            <div class="stats">
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('Posted by %1$s',
                  $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())) ?>
            </div>
            <div class="description">
               <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
               <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
               <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 45) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if( $this->paginator->getPages()->pageCount > 1 ): ?>
      <?php echo $this->partial('_widgetLinks.tpl', 'core', array(
        'url' => $this->url(array('action' => 'index'), 'classified_general', true),
        'param' => array('orderby' => 'view_count')
        )); ?>
    <?php endif; ?>
</div>
