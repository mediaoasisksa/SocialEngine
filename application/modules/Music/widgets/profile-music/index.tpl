<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    <?php if( !$this->renderOne ): ?>
    var anchor = scriptJquery('#profile_music').parent();
    scriptJquery('#profile_music_previous').css("display",'<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>');
    scriptJquery('#profile_music_next').css("display", '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>');

    scriptJquery('#profile_music_previous').off('click').on('click', function(){
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

    scriptJquery('#profile_music_next').off('click').on('click', function(){
      console.log("asfasdf ");
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

<ul id="profile_music" class="music_browse">
  <?php foreach( $this->paginator as $playlist ): ?>
  <li>
    <div class='music_browse_info'>
      <div class="music_browse_info_title">
        <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
      </div>
      <div class='music_browse_info_date'>
        Posted <?php echo $this->timestamp($playlist->creation_date) ?>
        <?php echo $this->partial('_rating.tpl', 'core', array('item' => $playlist, 'param' => 'show', 'module' => 'music')); ?>
      </div>
      <div class='music_browse_info_desc'>
        <?php echo $playlist->description ?>
      </div>
    </div>
    <?php echo $this->partial('application/modules/Music/views/scripts/_Player.tpl', array(
      'playlist' => $playlist,
      'short_player' => $this->short_player,
      'hideLinks' => true,
    )) ?>
  </li>
  <?php endforeach; ?>
</ul>

<div class="profile_paginator">
  <div id="profile_music_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_music_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
