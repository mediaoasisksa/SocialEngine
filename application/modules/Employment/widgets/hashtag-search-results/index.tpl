<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        // Enable links
        scriptJquery('.employments_browse_info_blurb').enableLinks();
    });
</script>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="employments_browse">
    <?php foreach( $this->paginator as $item ): ?>
      <li class="employments_browse_item">
        <div class='employments_browse_info'>
          <div class='employments_browse_info_title'>
            <h3>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              <?php if( $item->closed ): ?>
                <i class="employments_close_icon"></i>
              <?php endif;?>
            </h3>
          </div>
          <div class='employments_browse_info_des'>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 120) ?>
          </div>
          <div class='employments_browse_info_blurb'>
            <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
            <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
          </div>
        </div>
        <div class="employments_browse_footer">
          <div class="employments_browse_footer_info">
            <span><i class="far fa-user"></i><?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></span>
            <span><i class="far fa-clock"></i><?php echo $this->timestamp(strtotime($item->creation_date)) ?></span>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else:?>
  <div class="tip">
    <span><?php echo $this->translate('Nobody has posted a employment listing yet.');?></span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true,'query' => $this->formValues)); ?>
