<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: external.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<?php if( $this->error == 1 ): ?>
  <?php echo $this->translate('Embedding of videos has been disabled.') ?>
  <?php return ?>
<?php elseif( $this->error == 2 ): ?>
  <?php echo $this->translate('Embedding of videos has been disabled for this video.') ?>
  <?php return ?>
<?php elseif( !$this->video || $this->video->status != 1 ): ?>
  <?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.') ?>
  <?php return ?>
<?php endif; ?>

<?php if ( $this->video->type == 'upload' && $this->video_extension == 'mp4' )
    $this->headScript()
         ->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');
?>

<?php if( $this->video->type == 'upload' && $this->video_extension == 'flv' ):
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer.min.js');
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'externals/flowplayer/skin/skin.css');
?>

<?php endif ?>

<script type="text/javascript">
  var pre_rate = <?php echo $this->video->rating;?>;
  var video_id = <?php echo $this->video->video_id;?>;
  var total_votes = <?php echo $this->rating_count;?>;
  
  function set_rating() {
    var rating = pre_rate;
    scriptJquery('#rating_text').html("<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>");
    for(var x=1; x<=parseInt(rating); x++) {
      scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big');
    }

    for(var x=parseInt(rating)+1; x<=5; x++) {
      scriptJquery('#rate_'+x).attr('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      scriptJquery('#rate_'+last).attr('class', 'rating_star_big_generic rating_star_big_half');
    }
  }

  en4.core.runonce.add(set_rating);
</script>

<h2>
  <?php echo $this->video->getTitle() ?>
</h2>

<div class='video_view_container' style="max-width: 500px;">

  <div class="video_view video_view_container">
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
      <?php echo $this->translate('Posted by %1$s on %2$s',
          $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle()),
          $this->timestamp($this->video->creation_date)
          ) ?>
      <?php if( $this->category ): ?>
        - <?php echo $this->translate('Filed in') ?>
        <?php echo $this->htmlLink(array(
            'route' => 'video_general',
            'QUERY' => array('category' => $this->category->category_id)
          ), $this->translate($this->category->category_name)
        ) ?>
      <?php endif; ?>
      <?php if (engine_count($this->videoTags) ): ?>
      -
        <?php foreach ($this->videoTags as $tag): ?>
          <a href='javascript:void(0);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.rating', 1)) { ?>
      <div id="video_rating" class="rating">
        <span id="rate_1" class="rating_star_big_generic"></span>
        <span id="rate_2" class="rating_star_big_generic"></span>
        <span id="rate_3" class="rating_star_big_generic"></span>
        <span id="rate_4" class="rating_star_big_generic"></span>
        <span id="rate_5" class="rating_star_big_generic"></span>
        <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate') ?></span>
      </div>
    <?php } ?>
    <div class="video_desc" style="max-height: 55px;">
      <?php echo $this->video->description;?>
    </div>
    <br/>
  </div>
</div>
