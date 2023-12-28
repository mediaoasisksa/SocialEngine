<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Travel
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    https://socialengine.com/eula
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php if( $this->trAlign == '1'): ?>
<div class="trlistmain">
  <ul class="trlist">
    <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="owner">
          <?php
          $owner = $item->getOwner();
          echo $this->translate('By: %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
          ?>
        </div>
        <?php
           $desc = trim($this->string()->truncate($this->string()->stripTags($item->body), $this->trDesLength));
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
  'url' => $this->url(array('action' => 'index'), 'travel_general', true),
  'param' => array('orderby' => 'view_count')
  )); ?>
  <?php endif; ?>
</div>
<?php elseif( $this->trAlign == '0'): ?>
<div class="trlistmain">
  <ul class="trlisth">
    <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
      </div>
      <div class="info">
        <div class="title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="owner">
          <?php
          $owner = $item->getOwner();
          echo $this->translate('By: %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
          ?>
        </div>
        <?php
           $desc = trim($this->string()->truncate($this->string()->stripTags($item->body), $this->trDesLength));
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
  'url' => $this->url(array('action' => 'index'), 'travel_general', true),
  'param' => array('orderby' => 'view_count')
  )); ?>
  <?php endif; ?>
</div>
<?php endif; ?>
