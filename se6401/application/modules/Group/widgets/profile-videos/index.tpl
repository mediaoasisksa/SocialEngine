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
      var anchor = scriptJquery('#profile_groups_videos').parent();
    scriptJquery('#profile_groups_videos_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_videos_next').css("display",'<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_videos_previous').off('click').on('click', function(){
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
    scriptJquery('#profile_groups_videos_next').off('click').on('click', function(){
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
'route' => 'video_general',
'controller' => 'video',
'action' => 'create',
'parent_type'=> 'group',
'subject_id' => $this->subject()->getIdentity(),
), $this->translate('Add Videos'), array(
'class' => 'buttonlink icon_group_photo_new'
)) ?>
  <?php endif; ?>
</div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="container no-padding">
  <div class="row">
    <?php foreach( $this->paginator as $video ): ?>
    <div class="col-lg-4 col-md-6 videos_browse">
      <div>
        <div class="video_thumb_wrapper">
          <?php if( $video->duration ): ?>
          <span class="video_length">
            <?php
if( $video->duration >= 3600 ) {
$duration = gmdate("H:i:s", $video->duration);
} else {
$duration = gmdate("i:s", $video->duration);
}
echo $duration;
?>
          </span>
          <?php endif; ?>
          <?php echo $this->htmlLink($video->getHref(), $this->itemBackgroundPhoto($video, 'thumb.profile')) ?>
          <div class="info_stat_grid">
            <span>
              <?php if( $video->like_count > 0 ) :?>
              <i class="fa fa-thumbs-up">
              </i>
              <?php echo $this->locale()->toNumber($video->like_count) ?>
              <?php endif; ?>
            </span>
            <span>
              <?php if( $video->comment_count > 0 ) :?>
              <i class="fa fa-comment">
              </i>
              <?php echo $this->locale()->toNumber($video->comment_count) ?>
              <?php endif; ?>
            </span>
          </div>
        </div>
        <div class="video_grid_info">
          <?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'video_title')) ?>
          <div class="video_author">
            <?php echo $this->translate('By') ?>
            <?php echo $this->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle()) ?>
          </div>
          <div class="video_stats">
            <span class="views_video">
              <i class="fa fa-eye" aria-hidden="true">
              </i>
              <?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
            </span>
            <span class="star_rating_wrapper">
              <?php for( $x=1; $x<=$video->rating; $x++ ): ?>
              <span class="rating_star_generic rating_star">
              </span>
              <?php endfor; ?>
              <?php if( (round($video->rating) - $video->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half">
              </span>
              <?php endif; ?>
              <?php for( $x=5; $x>round($video->rating); $x-- ): ?>
              <span class="rating_star_generic rating_star_empty">
              </span>
              <?php endfor; ?>
            </span>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>
</div>
<div class="profile_paginator">
  <div id="profile_groups_videos_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
'onclick' => '',
'class' => 'buttonlink icon_previous'
)); ?>
  </div>
  <div id="profile_groups_videos_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
'onclick' => '',
'class' => 'buttonlink_right icon_next'
)); ?>
  </div>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate('No videos have been added to this group yet.');?>
  </span>
</div>
<?php endif; ?>
