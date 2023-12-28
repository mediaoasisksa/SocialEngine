<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        // Enable links
        scriptJquery('.classifieds_browse_info_blurb').enableLinks();
    });
</script>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

<div class='container no-padding'>
  <div class='row'>
    <?php foreach( $this->paginator as $item ): ?>
      <div class='col-lg-4 col-md-6 classifieds_browse'>
        <div class='classifieds_browse_inner'>
          <div class='classifieds_browse_photo'> <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?> </div>
          <div class='classifieds_browse_info'>
            <div class='classifieds_browse_info_title'>
              <h3> <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                <?php if( $item->closed ): ?>
                  <i class="fa fa-times"></i>
                <?php endif;?>
              </h3>
            </div>
            <div class='classifieds_browse_info_date'> <?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?> </div>
            <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'classified')); ?>
            <div class='classifieds_browse_info_des'> <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 120) ?> </div>
            <div class='classifieds_browse_info_blurb'>
              <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php echo $this->fieldValueLoop($item, $fieldStructure) ?> </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php else:?>
<div class="tip"> <span> <?php echo $this->translate('No one has posted a classified listing yet.');?> </span> </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true,'query' => $this->formValues)); ?> 
