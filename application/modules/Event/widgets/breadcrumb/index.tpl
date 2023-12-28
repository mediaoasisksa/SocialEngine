<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10248 2014-05-30 21:48:38Z andres $
 * @author     Jung
 */
?>
<div class="event_breadcrumb">
  <p>
    <?php if($this->event->getParentItem()): ?>
    <?php echo $this->event->getParentItem()->__toString(); ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('route' => 'event_general','action' => 'browse'), "Events", array()); ?>
    <?php endif; ?>
    <?php if($this->event->category_id): ?>
      <?php $category = Engine_Api::_()->getItem('event_category', $this->event->category_id); ?>
      <?php echo $this->translate('&#187;'); ?>
      <a href="<?php echo $this->url(array('action' => 'browse'), 'event_general', true).'?category_id='.urlencode($category->getIdentity()) ; ?>"><?php echo $this->translate($category->title); ?></a>
      <?php if($this->event->subcat_id): ?>
        <?php $subCat = Engine_Api::_()->getItem('event_category', $this->event->subcat_id); ?>
        <?php echo $this->translate('&#187;'); ?>
        <a href="<?php echo $this->url(array('action' => 'browse'), 'event_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) ; ?>"><?php echo $this->translate($subCat->title); ?></a>   
      <?php endif; ?>
      <?php if($this->event->subsubcat_id): ?>
        <?php $subSubCat = Engine_Api::_()->getItem('event_category', $this->event->subsubcat_id); ?>
        <?php echo $this->translate('&#187;'); ?>
        <a class="catlabel" href="<?php echo $this->url(array('action' => 'browse'), 'event_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) .'&subsubcat_id='.urlencode($subSubCat->category_id) ; ?>"><?php echo $this->translate($subSubCat->title); ?></a>
      <?php endif; ?>
    <?php endif; ?>
    <?php echo $this->translate('&#187;'); ?>
    <?php echo $this->event->getTitle(); ?>
  </p>
</div>
