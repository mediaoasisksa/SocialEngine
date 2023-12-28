<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10248 2014-05-30 21:48:38Z andres $
 * @author     Jung
 */
?>
<div class="group_breadcrumb">
  <p>
    <?php echo $this->htmlLink(array('route' => 'group_general','action' => 'browse'), "Groups", array()); ?>
    <?php if($this->group->category_id): ?>
      <?php $category = Engine_Api::_()->getItem('group_category', $this->group->category_id); ?>
      <?php echo $this->translate('&#187;'); ?>
      <a href="<?php echo $this->url(array('action' => 'browse'), 'group_general', true).'?category_id='.urlencode($category->getIdentity()) ; ?>"><?php echo $this->translate($category->title); ?></a>
      <?php if($this->group->subcat_id): ?>
        <?php $subCat = Engine_Api::_()->getItem('group_category', $this->group->subcat_id); ?>
        <?php echo $this->translate('&#187;'); ?>
        <a href="<?php echo $this->url(array('action' => 'browse'), 'group_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) ; ?>"><?php echo $this->translate($subCat->title); ?></a>   
      <?php endif; ?>
      <?php if($this->group->subsubcat_id): ?>
        <?php $subSubCat = Engine_Api::_()->getItem('group_category', $this->group->subsubcat_id); ?>
        <?php echo $this->translate('&#187;'); ?>
        <a class="catlabel" href="<?php echo $this->url(array('action' => 'browse'), 'group_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) .'&subsubcat_id='.urlencode($subSubCat->category_id) ; ?>"><?php echo $this->translate($subSubCat->title); ?></a>
      <?php endif; ?>
    <?php endif; ?>
    <?php echo $this->translate('&#187;'); ?>
    <?php echo $this->group->getTitle(); ?>
  </p>
</div>
