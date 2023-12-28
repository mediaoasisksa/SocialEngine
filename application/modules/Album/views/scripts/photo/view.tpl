<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 10110 2013-10-31 02:04:11Z andres $
 * @author     John Boehr <j@webligo.com>
*/
?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Album/externals/scripts/core.js');
$this->headTranslate(array(
'Save', 'Cancel', 'delete',
));
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('album.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'album';
    var pre_rate = <?php echo $this->photo->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->photo->photo_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'album_photo';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('album.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        var descEls = scriptJquery('.albums_viewmedia_info_caption');
        if( descEls.length > 0 ) {
           descEls.enableLinks();
        }
        var taggerInstance = window.taggerInstance = new Tagger('#media_photo_next',{
        'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
        'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
        'createRequestOptions' : {
            'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
            'data' : {
                'subject' : '<?php echo $this->subject()->getGuid() ?>'
            }
        },
        'deleteRequestOptions' : {
            'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
            'data' : {
                'subject' : '<?php echo $this->subject()->getGuid() ?>'
            }
        },
        'cropOptions' : {
            'container' : scriptJquery('#media_photo_next')
        },
        'tagListElement' : '#media_tags',
        'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
        'suggestProto' : 'request.json',
        'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
        'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
        'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
        'enableDelete' : <?php echo ($this->canUntagGlobal ? 'true' : 'false') ?>
      });   
    });

    var tagAction = window.tagAction = function(tag) {
        scriptJquery('#tag').val(tag);
        scriptJquery('#filter_form').trigger("submit");
    }
</script>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array(
    'module' => 'album',
'controller' => 'index',
'action' => 'browse-photos'
), 'album_general', true) ?>' style='display:none;'>
<input type="hidden" id="tag" name="tag" value=""/>
</form>



<?php if (""!=$this->album->getDescription()): ?>
<p class="photo-description">
  <?php echo $this->album->getDescription() ?>
</p>
<?php endif ?>

<div class="layout_middle">
  <div class='albums_viewmedia'>
    <?php if( !$this->message_view): ?>
    <div class="albums_viewmedia_nav">
      <div>
        <?php echo $this->translate('Photo %1$s of %2$s in %3$s', $this->locale()->toNumber($this->photo->getPhotoIndex() + 1), $this->album->count(), (string) $this->album->getTitle()) ?>
      </div>
      <?php if( $this->album->count() > 1 ): ?>
      <div>
        <?php echo $this->htmlLink(( $this->previousPhoto ? $this->previousPhoto->getHref() : null ), $this->translate('Prev'), array('id' => 'photo_prev')) ?>
        <?php echo $this->htmlLink(( $this->nextPhoto ? $this->nextPhoto->getHref() : null ), $this->translate('Next'), array('id' => 'photo_next')) ?>
      </div>
      <?php endif ?>
    </div>
    <?php endif ?>
    <div class='albums_viewmedia_info'>
      <div class='album_viewmedia_container' id='media_photo_div'>
        <a id='media_photo_next'  href='<?php echo (0 && $this->nextPhoto && !$this->message_view)? $this->escape($this->nextPhoto->getHref()) : 'javascript::void()' ?>'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
        'id' => 'media_photo'
        )); ?>
        </a>
      </div>
      <br />
      <a></a>
      <?php if( $this->photo->getTitle() ): ?>
      <div class="albums_viewmedia_info_title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
      <?php endif; ?>
      <?php if( $this->photo->getDescription() ): ?>
      <div class="albums_viewmedia_info_caption">
        <?php echo Engine_Api::_()->core()->smileyToEmoticons(nl2br($this->photo->getDescription())); ?>
      </div>
      <?php endif; ?>
      <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;">
        <?php echo $this->translate('Tagged:') ?>
      </div>

      <div class="albums_viewmedia_info_footer">
        <div class="albums_viewmedia_info_date">
          <?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
          <?php if (engine_count($this->photoTags )):?>
          -
          <?php foreach ($this->photoTags as $tag): ?>
          <?php if ($tag->getTag()->getType() == 'core_tag'): ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->getIdentity(); ?>);'>#<?php echo $tag->getTag()->getTitle();?></a>&nbsp;
          <?php endif; ?>
          <?php endforeach; ?>
          <?php endif; ?>
          <?php if ($this->viewer()->getIdentity()):?>
          <?php if( $this->canTag ): ?>
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstance.begin();')) ?>
          <?php endif; ?>
          <?php if( $this->canEdit ): ?>
          - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
          <?php endif; ?>
          <?php if( $this->canDelete ): ?>
          - <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
          <?php endif; ?>
          <?php if( !$this->message_view ):?>
          - <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'album_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
          - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
          - <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
          <?php endif;?>
          <?php endif ?>
        </div>
        <?php if( $this->canEdit ): ?>
        <div class="albums_viewmedia_info_actions">
          <a class="buttonlink icon_photos_rotate_ccw" href="javascript:void(0)" onclick="scriptJquery(this).attr('class', 'buttonlink icon_loading');en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 90).then(function(){ this.attr('class', 'buttonlink icon_photos_rotate_ccw') }.bind(scriptJquery(this)));">&nbsp;</a>
          <a class="buttonlink icon_photos_rotate_cw" href="javascript:void(0)" onclick="scriptJquery(this).attr('class', 'buttonlink icon_loading');en4.album.rotate(<?php echo $this->photo->getIdentity() ?>, 270).then(function(){ this.attr('class', 'buttonlink icon_photos_rotate_cw') }.bind(scriptJquery(this)));">&nbsp;</a>
          <a class="buttonlink icon_photos_flip_horizontal" href="javascript:void(0)" onclick="scriptJquery(this).attr('class', 'buttonlink icon_loading');en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'horizontal').then(function(){ this.attr('class', 'buttonlink icon_photos_flip_horizontal') }.bind(scriptJquery(this)));">&nbsp;</a>
          <a class="buttonlink icon_photos_flip_vertical" href="javascript:void(0)" onclick="scriptJquery(this).attr('class', 'buttonlink icon_loading');en4.album.flip(<?php echo $this->photo->getIdentity() ?>, 'vertical').then(function(){ this.attr('class', 'buttonlink icon_photos_flip_vertical') }.bind(scriptJquery(this)));">&nbsp;</a>
        </div>
        <?php endif ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('album.enable.rating', 1)) { ?>
          <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'album')); ?>
        <?php } ?>
        <br/>
      </div>
    </div>

  </div>
</div>


<script type="text/javascript">
    scriptJquery('.core_main_album').parent().addClass('active');
</script>
