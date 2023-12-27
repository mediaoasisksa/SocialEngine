<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 10248 2014-05-30 21:48:38Z andres $
 * @author     Jung
 */
?>
<?php if( !$this->video || $this->video->status !=1 ):
echo $this->translate('The video you are looking for does not exist or has not been processed yet.');
return; // Do no render the rest of the script in this mode
endif; ?>

<?php if ( $this->video->type == 'upload' && $this->video_extension == 'mp4' )
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
?>

<?php if( $this->video->type == 'upload' && $this->video_extension == 'flv' ):
    $this->headScript()
         ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer.js');
    $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/flowplayer/skin/skin.css');
  ?>
<?php endif ?>

<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'video';
    var pre_rate = <?php echo $this->video->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->video->video_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'video';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>

<h2>
  <?php echo $this->video->getTitle(); ?>
</h2>

<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>' style='display:none;'>
<input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class="video_view video_view_container">
  <div class="video_desc">
    <?php echo $this->translate('Posted by') ?>
    <?php echo $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle()) ?>
  </div>
  <div class="video_desc">
    <?php echo Engine_Api::_()->core()->smileyToEmoticons($this->video->description);?>
  </div>
  <?php if( $this->video->type == 'upload' ): ?>
    <div id="video_embed" class="video_embed">
      <video id="video" controls preload="auto" width="480" height="300">
        <source type='video/mp4;' src="<?php echo $this->video_location ?>">
      </video>
    </div>
  <?php else: ?>
  <div class="video_embed">
    <?php echo $this->videoEmbedded ?>
  </div>
  <?php endif; ?>
  <div class="video_date">
    <?php echo $this->translate('Posted') ?>
    <?php echo $this->timestamp($this->video->creation_date) ?>
    <?php if( $this->category ): ?>
    - <?php echo $this->translate('Filed in') ?>
    <?php echo $this->htmlLink(array(
    'route' => 'video_general',
    'QUERY' => array('category' => $this->category->category_id)
    ), $this->translate($this->category->category_name)
    ) ?>
    <?php endif; ?>
    <?php if (engine_count($this->videoTags )):?>
    -
    <?php foreach ($this->videoTags as $tag): ?>
    <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
  
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.rating', 1)) { ?>
    <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'video')); ?>
  <?php } ?>
  <br/>

  <div class='video_options'>
    <?php if( $this->can_edit ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'default',
    'module' => 'video',
    'controller' => 'index',
    'action' => 'edit',
    'video_id' => $this->video->video_id
    ), $this->translate('Edit Video'), array(
    //'class' => 'buttonlink icon_video_edit'
    )) ?>
    &nbsp;|&nbsp;
    <?php endif;?>
    <?php if( $this->can_delete && $this->video->status != 2 ): ?>
    <?php echo $this->htmlLink(array(
    'route' => 'default',
    'module' => 'video',
    'controller' => 'index',
    'action' => 'delete',
    'video_id' => $this->video->video_id,
    'format' => 'smoothbox'
    ), $this->translate('Delete Video'), array(
    'class' => 'smoothbox'
    //'class' => 'buttonlink smoothbox icon_video_delete'
    )) ?>
    &nbsp;|&nbsp;
    <?php endif;?>
    <?php if( $this->can_embed ): ?>
    <?php echo $this->htmlLink(array(
    'module'=> 'video',
    'controller' => 'video',
    'action' => 'embed',
    'route' => 'default',
    'id' => $this->video->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Embed"), array(
    'class' => 'smoothbox'
    )); ?>
    &nbsp;|&nbsp;
    <?php endif ?>
    <?php if( Engine_Api::_()->user()->getViewer()->getIdentity() ): ?>
    <?php echo $this->htmlLink(array(
    'module'=> 'activity',
    'controller' => 'index',
    'action' => 'share',
    'route' => 'default',
    'type' => 'video',
    'id' => $this->video->getIdentity(),
    'format' => 'smoothbox'
    ), $this->translate("Share"), array(
    'class' => 'smoothbox'
    //'class' => 'buttonlink smoothbox icon_comments'
    )); ?>
    &nbsp;|&nbsp;
    <?php echo $this->htmlLink(array(
    'module'=> 'core',
    'controller' => 'report',
    'action' => 'create',
    'route' => 'default',
    'subject' => $this->video->getGuid(),
    'format' => 'smoothbox'
    ), $this->translate("Report"), array(
    'class' => 'smoothbox'
    //'class' => 'buttonlink smoothbox icon_report'
    )); ?>
    &nbsp;|&nbsp;
    <?php endif ?>
    <?php echo $this->translate(array('%s view', '%s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
  </div>
</div>


<script type="text/javascript">
    var tagAction = function(tag_id){
      var url = "<?php echo $this->url(array('module' => 'video','action'=>'browse'), 'video_general', true) ?>?tag_id="+tag_id;
      window.location.href = url;
    }
    scriptJquery('.core_main_video').parent().addClass('active');
</script>
