<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @access	   John
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = scriptJquery('#profile_events').parent();
    scriptJquery('#profile_events_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_events_next').css("display",'<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');

    scriptJquery('#profile_events_previous').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        dataType : 'html',
        method : 'post',
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    scriptJquery('#profile_events_next').off('click').on('click', function(){
      en4.core.request.send(scriptJquery.ajax({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        dataType : 'html',
        method : 'post',
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
<div class="container no-padding">
  <div class='row events_browse' id="profile_events">
    <?php foreach( $this->paginator as $event ): ?>
      <div class="col-lg-4 col-md-6 event_grid_outer">
        <div class="events_browse_inner grid_wrapper">
          <div class="events_photo">
          <?php $startTime = $this->locale()->toDateTime($event->starttime); ?>
          <?php $endTime = $this->locale()->toDateTime($event->endtime); ?>
          <?php if( $this->filter == "past" ): ?>
            <?php $imgTitle = array('title' => "Ends on : $endTime");?>
          <?php else: ?>
            <?php $imgTitle = array('title' => "Starts on : $startTime");?>
          <?php endif; ?>
          <?php echo $this->htmlLink($event->getHref(), $this->itemBackgroundPhoto($event, 'thumb.profile'), $imgTitle) ?>
          <div class="events_held_info">
            <i class="fa fa-calendar"></i>
            <span class="event_start_date"><?php echo $startTime; ?></span>
            <span class="event_end_date"><?php echo $endTime; ?></span>
          </div>
        </div>
        <div class="events_info">
          <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
          </div>
          <div class="events_members">
            <?php echo $this->translate(array('%s guest response', '%s guest responses', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <?php echo $this->partial('_rating.tpl', 'core', array('item' => $event, 'param' => 'show', 'module' => 'event')); ?>
          <?php if(empty($event->is_online) && $event->location ): ?>
            <div class="events_location">
              <i class="fa fa-map-marker"></i>
              <span><?php echo $event->location ?></span>
            </div>
          <?php elseif(!empty($event->is_online && $event->website)): ?>
            <div class="events_location">
              <i class="fa fa-globe"></i>
              <?php $website = (preg_match("#https?://#", $event->website) === 0) ? 'http://'.$event->website : $event->website; ?>
              <span><a href="<?php echo $website ?>" target="_blank"><?php echo $website ?></a></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
     </div>
    <?php endforeach; ?>
  </div>
  <div class="profile_paginator">
    <div id="profile_events_previous" class="paginator_previous">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'onclick' => '',
        'class' => 'buttonlink icon_previous'
      )); ?>
    </div>
    <div id="profile_events_next" class="paginator_next">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => '',
        'class' => 'buttonlink_right icon_next'
      )); ?>
    </div>
  </div>
</div>
