<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Bizlist
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    // Enable links
    scriptJquery('.bizlists_browse_info_blurb').enableLinks();
  });
</script>

<?php if( $this->tag ): ?>
  <h3>
    <?php echo $this->translate('Showing business listings using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array('module' => 'bizlist', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
  </h3>
<?php endif; ?>

<?php if( $this->start_date ): ?>
  <?php foreach ($this->archive_list as $archive): ?>
    <h3>
      <?php echo $this->translate('Showing business listings created on');?> <?php if ($this->start_date==$archive['date_start']) echo $archive['label']?> <a href="<?php echo $this->url(array('module' => 'bizlist', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
    </h3>
  <?php endforeach; ?>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
 <div class='bizlists_browse container no-padding'>
   <div class='row'>
    <?php foreach( $this->paginator as $item ): ?>
      <div class='col-lg-6 col-md-6 bizlists_browse_item'>
        <div class='bizlists_browse_item_inner'>
        <div class='bizlists_browse_photo'>
          <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.profile')) ?>
        </div>
        <div class='bizlists_browse_info'>
          <div class='bizlists_browse_info_title'>
            <h3>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            <?php if( $item->closed ): ?>
              <i class="bizlist_close_icon"></i>
            <?php endif;?>
            </h3>
          </div>
          <div class='bizlists_browse_info_date'>
            Posted <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
          </div>
          <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'bizlist')); ?>
          <div class='bizlists_browse_info_des'>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 120) ?>
          </div>
        </div>
      </div>
	 </div>
    <?php endforeach; ?>
  </div>
</div>

<?php elseif( $this->category || $this->show == 2 || $this->tag || $this->search ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has posted a business listing with that criteria.');?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'bizlist_general', true).'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has posted a business listing yet.');?>
      <?php if ($this->can_create): ?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'bizlist_general', true).'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  //'params' => $this->formValues,
)); ?>
