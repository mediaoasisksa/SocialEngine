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
      var anchor = scriptJquery('#profile_groups_events').parent();
    scriptJquery('#profile_groups_events_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_events_next').css("display",'<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_events_previous').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        method : 'post',
        dataType : 'html',
        data : {
        format : 'html',
        subject : en4.core.subject.guid,
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
      }
                                              }
                                             ), {
        'element' : anchor
      }
                           )
    }
                                                                   );
    scriptJquery('#profile_groups_events_next').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        method : 'post',
        dataType : 'html',
        data : {
        format : 'html',
        subject : en4.core.subject.guid,
        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
      }
                                              }
                                             ), {
        'element' : anchor
      }
                           )
    }
                                                               );
    <?php endif;
    ?>
  }
    );
</script>
<div class="group_profile_options">
  <?php if( $this->canAdd ): ?>
  <?php echo $this->htmlLink(array(
'route' => 'event_general',
'controller' => 'event',
'action' => 'create',
'parent_type'=> 'group',
'subject_id' => $this->subject()->getIdentity(),
), $this->translate('Add Events'), array(
'class' => 'buttonlink icon_group_photo_new'
)) ?>
  <?php endif; ?>
</div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="container no-padding">
  <div class='row events_browse'>
    <?php foreach( $this->paginator as $event ): ?>
    <div class="col-lg-4 col-md-6 event_grid_outer">
      <div class="events_browse_inner grid_wrapper">
        <div class='events_photo'>
          <?php echo $this->htmlLink($event->getHref(), $this->itemBackgroundPhoto($event, 'thumb.profile')) ?>
          <div class="events_held_info">
            <i class="fa fa-calendar">
            </i>
            <span class="event_start_date"> 
              <?php echo $this->locale()->toDateTime($event->starttime) ?>
            </span>
          </div>
        </div>
        <div class='events_info'>
          <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $this->string()->chunk($event->getTitle(), 10)) ?></h3>
          </div>
          <span class="events_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </span>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>
</div>

<div class="profile_paginator">
  <div id="profile_groups_events_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
'onclick' => '',
'class' => 'buttonlink icon_previous'
)); ?>
  </div>
  <div id="profile_groups_events_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
'onclick' => '',
'class' => 'buttonlink_right icon_next'
)); ?>
  </div>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate('No events have been added to this group yet.');?>
  </span>
</div>
<?php endif; ?>
