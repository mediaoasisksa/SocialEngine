<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9859 2013-02-12 02:06:55Z john $
 * @author     Jung
 */
?>
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
                echo $duration;
              ?>
                </span>
                <?php endif ?>
                <?php echo $this->htmlLink($item->getHref(), $this->itemBackgroundPhoto($item, 'thumb.normal')) ?>
                <div class="info_stat_grid"> <span>
                    <?php if( $item->like_count > 0 ) :?>
                    <i class="fa fa-thumbs-up"></i> <?php echo $this->locale()->toNumber($item->like_count) ?>
                    <?php endif; ?>
                    </span> <span>
                    <?php if( $item->comment_count > 0 ) :?>
                    <i class="fa fa-comment"></i> <?php echo $this->locale()->toNumber($item->comment_count) ?>
                    <?php endif; ?>
                    </span> </div>
              </div>
              <div class="video_grid_info"> <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'video_title')) ?>
                <div class="video_author"> <?php echo $this->translate('By') ?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?> </div>
                <div class="video_stats"> <span class="views_video"> <i class="fa fa-eye" aria-hidden="true"></i> <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?> </span>
                <?php echo $this->partial('_rating.tpl', 'core', array('item' => $item, 'param' => 'show', 'module' => 'video')); ?>
                </div>
              </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php else:?>
  <div class="tip">
      <span>
        <?php echo $this->translate('No one has created a video yet.');?>
      </span>
  </div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>
