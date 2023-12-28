<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Video
* @copyright  Copyright 2006-2020 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: browse.tpl 9785 2012-09-25 08:34:18Z pamela $
* @author     John Boehr <john@socialengine.com>
*/
?>
<?php if( $this->tag ): ?>
<h3>
  <?php echo $this->translate('Videos using the tag') ?>
  #
  <?php echo $this->tag ?>
  <a href="<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>">(x)
  </a>
</h3>
<?php endif; ?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="container no-padding">
  <div class="row">
    <?php foreach( $this->paginator as $item ): ?>
      <div class="col-lg-4 col-md-6 videos_browse">
        <div>
          <div class="video_thumb_wrapper">
            <?php if( $item->duration ): ?>
            <span class="video_length">
              <?php
                if( $item->duration >= 3600 ) {
                $duration = gmdate("H:i:s", $item->duration);
                } else {
                $duration = gmdate("i:s", $item->duration);
                }
                //$duration = ltrim($duration, '0:');
                //              if( $duration[0] == '0' ) {
                //                $duration= substr($duration, 1);
                //              }
                echo $duration;
                ?>
            </span>
            <?php endif ?>
            <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
            <div class="info_stat_grid">
              <span>
                <?php if( $item->like_count > 0 ) :?>
                <i class="fa fa-thumbs-up">
                </i>
                <?php echo $this->locale()->toNumber($item->like_count) ?>
                <?php endif; ?>
              </span>
              <span>
                <?php if( $item->comment_count > 0 ) :?>
                <i class="fa fa-comment">
                </i>
                <?php echo $this->locale()->toNumber($item->comment_count) ?>
                <?php endif; ?>
              </span>
            </div>
          </div>
          <div class="video_grid_info">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'video_title')) ?>
            <div class="video_author">
              <?php echo $this->translate('By') ?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </div>
            <div class="video_stats">
              <span class="views_video">
                <i class="fa fa-eye" aria-hidden="true">
                </i>
                <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </span>
              
              <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'video')); ?>
            </div>
          </div>
        </div>
        <p class="half_border_bottom"></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php elseif( $this->category || $this->tag || $this->text ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has posted a video with that criteria.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No one has created a video yet.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
'query' => $this->formValues,
'pageAsQuery' => true,
)); ?>
