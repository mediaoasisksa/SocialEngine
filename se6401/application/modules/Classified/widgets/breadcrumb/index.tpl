<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10248 2014-05-30 21:48:38Z andres $
 * @author     Jung
 */
?>
<div class="classified_breadcrumb">
  <p>
    <?php echo $this->htmlLink(array('route' => 'classified_general'), $this->translate("Classifieds"), array()); ?>
    
    <?php if($this->classified->category_id): ?>
      <?php $category = Engine_Api::_()->getItem('classified_category', $this->classified->category_id); ?>
      <?php if($category) { ?>
				<?php echo $this->translate('&#187;'); ?>
				<a href="<?php echo $this->url(array('action' => 'index'), 'classified_general', true).'?category_id='.urlencode($category->getIdentity()) ; ?>"><?php echo $this->translate($category->category_name); ?></a>
				<?php if($this->classified->subcat_id): ?>
					<?php $subCat = Engine_Api::_()->getItem('classified_category', $this->classified->subcat_id); ?>
					<?php echo $this->translate('&#187;'); ?>
					<a href="<?php echo $this->url(array('action' => 'index'), 'classified_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) ; ?>"><?php echo $this->translate($subCat->category_name); ?></a>   
				<?php endif; ?>
				<?php if($this->classified->subsubcat_id): ?>
					<?php $subSubCat = Engine_Api::_()->getItem('classified_category', $this->classified->subsubcat_id); ?>
					<?php echo $this->translate('&#187;'); ?>
					<a class="catlabel" href="<?php echo $this->url(array('action' => 'index'), 'classified_general', true).'?category_id='.urlencode($category->category_id) . '&subcat_id='.urlencode($subCat->category_id) .'&subsubcat_id='.urlencode($subSubCat->category_id) ; ?>"><?php echo $this->translate($subSubCat->category_name); ?></a>
				<?php endif; ?>
      <?php } ?>
    <?php endif; ?>
    <?php echo $this->translate('&#187;'); ?>
    <?php echo $this->classified->getTitle(); ?>
  </p>
</div>
