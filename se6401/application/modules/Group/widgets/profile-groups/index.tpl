<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author		 John
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = scriptJquery('#profile_groups').parent();
    scriptJquery('#profile_groups_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_next').css("display",'<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');

    scriptJquery('#profile_groups_previous').off('click').on('click', function(){
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

    scriptJquery('#profile_groups_next').off('click').on('click', function(){
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

<div class="container no-padding">
  <div class="row" id="profile_groups">
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
          <p class="half_border_bottom"></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="profile_paginator">
    <div id="profile_groups_previous" class="paginator_previous">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
        'onclick' => '',
        'class' => 'buttonlink icon_previous'
      )); ?>
    </div>
    <div id="profile_groups_next" class="paginator_next">
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
        'onclick' => '',
        'class' => 'buttonlink_right icon_next'
      )); ?>
    </div>
  </div>
</div>
