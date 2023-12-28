<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<?php if( 0 == engine_count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no polls yet.') ?>
      <?php if( $this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
          '<a href="'.$this->url(array('action' => 'create'), 'poll_general').'">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>

<?php else: // $this->polls is NOT empty ?>

<div class="polls_browse container no-padding">
  <div class='row'>   
    <?php foreach ($this->paginator as $poll): ?>
      <div class="col-lg-4 col-md-6 polls_browse_item" id="poll-item-<?php echo $poll->poll_id ?>">
        <div class="polls_browse_item_inner">
          <div class='polls_browse_photo'>
              <?php echo $this->htmlLink($poll->getHref(), $this->itemBackgroundPhoto($poll, 'thumb.profile')) ?>
          </div>
          <div class="polls_browse_info">
            <h3 class="polls_browse_info_title">
              <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
              <?php if( $poll->closed ): ?>
                <i class="polls_close_icon" alt="<?php echo $this->translate('Closed') ?>"></i>
              <?php endif ?>
            </h3>
            <p class='polls_browse_info_date'>
              <?php echo $this->translate('Posted');?>
              <?php echo $this->timestamp(strtotime($poll->creation_date)) ?>
              <?php echo $this->translate('by');?>
              <?php echo $this->htmlLink($poll->getOwner()->getHref(), $poll->getOwner()->getTitle()) ?>
              <?php echo $this->partial('_rating.tpl', 'core', array('item' => $poll, 'param' => 'show', 'module' => 'poll')); ?>
            </p>
   
            <div class="polls_browse_bottom">
              <span><i class="far fa-hand-point-up"></i><?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?></span>
              <span><i class="far fa-eye"></i><?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


<?php endif; // $this->polls is NOT empty ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
  //'params' => $this->formValues,
)); ?>
