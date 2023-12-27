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
      var anchor = scriptJquery('profile_groups_blogs').parent();
    scriptJquery('#profile_groups_blogs_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_blogs_next').css("display",'<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');
    scriptJquery('#profile_groups_blogs_previous').off('click').on('click', function(){
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
    scriptJquery('profile_groups_blogs_next').off('click').on('click', function(){
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
'route' => 'blog_general',
'controller' => 'blog',
'action' => 'create',
'parent_type'=> 'group',
'subject_id' => $this->subject()->getIdentity(),
), $this->translate('Add Blogs'), array(
'class' => 'buttonlink icon_group_photo_new'
)) ?>
  <?php endif; ?>
</div>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class='container no-padding'>
  <div class='row'>
    <?php foreach( $this->paginator as $blog ): ?>
    <div class='col-lg-4 col-md-6 blogs_browse'>
      <div class='blogs_browse_inner'>
        <div class='blogs_browse_photo'>
          <?php echo $this->htmlLink($blog->getHref(), $this->itemBackgroundPhoto($blog, 'thumb.profile')) ?>
        </div>
        <div class='blogs_browse_info'>
          <span class='blogs_browse_info_title'>
            <h3><?php echo $this->htmlLink($blog->getHref(), $blog->getTitle()) ?></h3>
          </span>
          <p class='blogs_browse_info_date'>
            <?php echo $this->translate('Posted');?> 
            <?php echo $this->timestamp($blog->creation_date) ?>
          </p>
          <p class='blogs_browse_info_blurb'>
            <?php echo $this->string()->truncate($this->string()->stripTags($blog->body),110) ?>
          </p>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>
</div>

<div class="profile_paginator">
  <div id="profile_groups_blogs_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
'onclick' => '',
'class' => 'buttonlink icon_previous'
)); ?>
  </div>
  <div id="profile_groups_blogs_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
'onclick' => '',
'class' => 'buttonlink_right icon_next'
)); ?>
  </div>
</div>
<?php else: ?>
<div class="tip">
  <span>
    <?php echo $this->translate('No Blog have been added to this group yet.');?>
  </span>
</div>
<?php endif; ?>
