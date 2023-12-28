<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9987 2013-03-20 00:58:10Z john $
 * @author     Steve
 */

// this is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs    = $playlist->getSongs();
?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.enable.rating', 1)) { ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/rating.js'); ?>
  <script type="text/javascript">
    var modulename = 'music';
    var pre_rate = <?php echo $this->playlist->rating;?>;
    var rated = '<?php echo $this->rated;?>';
    var resource_id = <?php echo $this->playlist->playlist_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';
    var resource_type = 'music';
    var rating_text = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    var ratingIcon = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('music.ratingicon', 'fas fa-star'); ?>';
  </script>
<?php } ?>
<?php if ($this->popout): ?>
<?php $this->headTitle($playlist->getTitle(), Zend_View_Helper_Placeholder_Container_Abstract::SET) ?>
<div class="music_playlist_popout_wrapper">
  <div class="music_playlist_info_title">
    <h3><?php echo $playlist->getTitle() ?></h3>
  </div>
  <div class="music_playlist_info_date">
    <?php echo $this->translate('Created %1$s by %2$s', $this->timestamp($playlist->creation_date), $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle())) ?>
  </div>
  <?php echo $this->partial('_Player.tpl', array('playlist'=>$this->playlist,'popout'=>true)) ?>
</div>
<?php return; endif; ?>

<h2>
  <?php echo $playlist->getTitle() ?>
</h2>

<div class="music_playlist" id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
  <div class="music_browse_author_photo">
    <?php echo $this->htmlLink($playlist->getOwner(), $this->itemBackgroundPhoto($playlist->getOwner(), 'thumb.profile') ) ?>
  </div>

  <div class="music_playlist_options">
    <?php if ($playlist->isEditable())
    echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit')),
    $this->translate('Edit Playlist'),
    array('class'=>'buttonlink icon_music_edit'
    )) ?>
    <?php if ($playlist->isDeletable())
    echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'delete')),
    $this->translate('Delete Playlist'),
    array('class'=>'buttonlink smoothbox icon_music_delete'
    )) ?>
    <?php if ($playlist->getOwner()->isSelf( Engine_Api::_()->user()->getViewer() ))
    echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'set-profile')),
    $this->translate($playlist->profile ? 'Disable Profile Playlist' : 'Play on my Profile'),
    array(
    'class' => 'buttonlink music_set_profile_playlist ' . ( $playlist->profile ? 'icon_music_disableonprofile' : 'icon_music_playonprofile' )
    )
    ) ?>
  </div>

  <div class="music_playlist_info">
    <div class="music_playlist_info_title">
      <p><?php echo Engine_Api::_()->core()->smileyToEmoticons($playlist->description); ?></p>
    </div>
    <div class="music_playlist_info_rating">
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.enable.rating', 1)) { ?>
        <?php echo $this->partial('_rating.tpl', 'core', array('rated' => $this->rated, 'param' => 'create', 'module' => 'music')); ?>
      <?php } ?>
    </div>
    <div class="music_playlist_info_date">
      <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
      <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
    </div>
    <?php echo $this->partial('_Player.tpl', array('playlist'=>$playlist)) ?>
  </div>
</div>


<script type="text/javascript">
    scriptJquery('.core_main_music').parent().addClass('active');
</script>
