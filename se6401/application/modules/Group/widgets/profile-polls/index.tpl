<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author		 Sami
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = scriptJquery('#profile_groups_polls').parent();
    scriptJquery('#profile_groups_polls_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_polls_next').css("display", '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');

    scriptJquery('#profile_groups_polls_previous').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        method : 'post',
        dataType : 'html',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    scriptJquery('#profile_groups_polls_next').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        method : 'post',
        dataType : 'html',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div class="group_profile_options">
  <?php if( $this->canAdd ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'poll_general',
        'controller' => 'poll',
        'action' => 'create',
        'parent_type'=> 'group',
        'subject_id' => $this->subject()->getIdentity(),
      ), $this->translate('Add Polls'), array(
        'class' => 'buttonlink icon_group_photo_new'
    )) ?>
  <?php endif; ?>
</div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <div class='polls_browse container no-padding'>
  <div class='row' id="profile_polls">
    <?php foreach ($this->paginator as $poll): ?>
      <div class="col-lg-4 col-md-6 polls_browse_item" id="poll-item-<?php echo $poll->poll_id ?>">
        <div class="polls_browse_item_inner" >
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

    <div class="profile_paginator">
      <div id="profile_groups_polls_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="profile_groups_polls_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
 </div>
<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No polls have been added to this group yet.');?>
    </span>
  </div>

<?php endif; ?>
