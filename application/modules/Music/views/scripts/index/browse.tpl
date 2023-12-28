<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>

<?php if( 0 == engine_count($this->paginator) ): ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('There is no music uploaded yet.') ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->htmlLink(array(
          'route' => 'music_general',
          'action' => 'create'
        ), $this->translate('Why don\'t you add some?')) ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>

  <ul class="music_browse music_grid">
    <?php foreach ($this->paginator as $playlist): ?>
      <li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
        <div class="music_browse_author_photo">
          <?php echo $this->htmlLink($playlist->getOwner(),
                     $this->itemBackgroundPhoto($playlist->getOwner(), 'thumb.profile') ) ?>
        </div>
        <div class="music_browse_info">
          <div class="music_browse_info_title">
            <h3>
              <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
            </h3>
          </div>
          <div class="music_info_top">
          <div class="music_browse_info_date">
            <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
            <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
          </div>
          <div class="music_playlist_stats">
              <?php if( $playlist->comment_count > 0 ) :?>
                <span><?php echo $this->translate(array('%s Comment', '%s Comments', $playlist->comment_count), $this->locale()->toNumber($playlist->comment_count)) ?></span>
              <?php endif; ?>

              <?php if( $playlist->like_count > 0 ) :?>
                <span>,</span>
                <span><?php echo $this->translate(array('%s Like', '%s Likes', $playlist->like_count), $this->locale()->toNumber($playlist->like_count)) ?></span>
              <?php endif; ?>

              <?php if( $playlist->play_count > 0 ) :?>
                <span>,</span>
                <span><?php echo $this->translate(array('%s Play', '%s Plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?></span>
              <?php endif; ?>

              <?php if( $playlist->view_count > 0 ) :?>
                <span class="music_views"><?php echo $this->translate(array('%s View', '%s Views', $playlist->view_count), $this->locale()->toNumber($playlist->view_count)) ?></span>
              <?php endif; ?>
              <?php echo $this->partial('_rating.tpl', 'core', array('item' => $playlist, 'param' => 'show', 'module' => 'music')); ?>
            </div>
          </div>
          <div class="music_browse_info_desc">
            <p><?php echo $this->string()->truncate($this->string()->stripTags($playlist->description), 300) ?></p>
          </div>
          <?php echo $this->partial('_Player.tpl', array('playlist' => $playlist, 'hideStats' => true)) ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
    //'params' => $this->formValues,
  )); ?>

<?php endif; ?>
