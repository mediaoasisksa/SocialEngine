<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9785 2012-09-25 08:34:18Z pamela $
 * @author	   John
 */
?>

<?php if( engine_count($this->paginator) > 0 ): ?>
<div class="container no-padding">
 <div class="row">
  <?php foreach( $this->paginator as $group ): ?>
  <div class="col-lg-4 col-md-6 groups_browse grid_wrapper">
    <div>
      <div class="groups_photo">
        <?php echo $this->htmlLink($group->getHref(), $this->itemBackgroundPhoto($group, 'thumb.profile')) ?>
      </div>
      <div class="groups_info">
        <div class="groups_members">
          <span><i class="fa fa-user"></i></span>
          <span><?php echo $this->translate(array('%s', '%s', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?></span>
        </div>
        <div class="groups_title">
          <h3><?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?></h3>
        </div>
        <div class="groups_date">
          <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
         </div>
         <?php echo $this->partial('_rating.tpl', 'core', array('item' => $group, 'param' => 'show', 'module' => 'group')); ?>
         <div class="groups_desc">
           <?php echo $this->viewMore($group->getDescription()) ?>
         </div>
        </div>
      </div>
    </div>
   <?php endforeach; ?>
 </div>
</div>

<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] )): ?>
<div class="tip">
    <span>
    <?php echo $this->translate('No one has created a group with that criteria.');?>
    <?php if( $this->canCreate ): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
    <?php endif; ?>
    </span>
</div>

<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('There are no groups yet.') ?>
    <?php if( $this->canCreate): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
    <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'query' => $this->formValues
)); ?>


