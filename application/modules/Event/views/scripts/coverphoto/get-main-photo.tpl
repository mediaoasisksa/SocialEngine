<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: get-main-photo.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Alex
 */
?>
<div class="profile_main_photo_wrapper">
  <div class="profile_main_photo b_dark">
    <div class="item_photo">
      <?php if (empty($this->uploadDefaultCover)): ?>
        <table class="main_thumb_photo">
          <tr valign="middle">
            <td>
              <?php if($this->event->getPhotoUrl('thumb.profile')) : ?>
                <span style="background-image:url('<?php echo $this->event->getPhotoUrl('thumb.profile');?>'); text-align:left;" id="event_profile_photo"></span>
              <?php else : ?>
                <span class="bg_item_photo bg_thumb_profile bg_item_photo_event bg_item_nophoto" id="event_profile_photo"></span>
              <?php endif;?>
            </td>
          </tr>
        </table>
      <?php else: ?>
        <table class="main_thumb_photo">
          <tr valign="middle">
            <td>
              <span class="bg_item_photo bg_thumb_profile bg_item_photo_event bg_item_nophoto" id="event_profile_photo"></span>
            </td>
          </tr>
        </table>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($this->can_edit) && empty($this->uploadDefaultCover)) : ?>
    <div id="mainphoto_options" class="profile_cover_options
      <?php if (!empty($this->uploadDefaultCover)) : ?> profile_main_photo_options is_hidden
      <?php else: ?> profile_main_photo_options<?php endif; ?>">
      <ul class="edit-button">
        <li>
          <?php if (!empty($this->event->photo_id)) : ?>
            <span class="profile_cover_btn">
              <i class="fa fa-camera" aria-hidden="true"></i>
            </span>
          <?php else: ?>
            <span class="profile_cover_btn">
              <i class="fa fa-camera" aria-hidden="true"></i>
            </span>
          <?php endif; ?>

          <ul class="profile_options_pulldown">
            <li>
              <a href='<?php echo $this->url(array(
                'action' => 'upload-cover-photo',
                'event_id' => $this->event->event_id,
                'photoType' => 'profile'), 'event_coverphoto', true); ?>' class="profile_cover_icon_photo_upload smoothbox">
                <?php echo $this->translate('Upload Photo'); ?>
              </a>
            </li>
            <li>
              <?php echo $this->htmlLink(
                $this->url(array(
                  'action' => 'choose-from-albums',
                  'event_id' => $this->event->event_id,
                  'recent' => 1,
                  'photoType' => 'profile'
                ), 'event_coverphoto', true),
                $this->translate('Choose from Albums'),
                array(' class' => 'profile_cover_icon_photo_view smoothbox')); ?>
            </li>
            <?php if (!empty($this->event->photo_id)) : ?>
              <li>
                <?php echo $this->htmlLink(
                  array('route' => 'event_coverphoto', 'action' => 'remove-cover-photo', 'event_id' => $this->event->event_id, 'photoType' => 'profile'),
                  $this->translate('Remove'),
                  array(' class' => 'smoothbox profile_cover_icon_photo_delete')); ?>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  <?php endif; ?>
</div>
<?php if (empty($this->uploadDefaultCover)): ?>
  <div class="cover_photo_profile_options">
    <div id='profile_status'>
      <?php if($this->subject()) { ?>
        <h2>
          <?php echo $this->subject()->getTitle() ?>
        </h2>
      <?php } ?>
      <span class="coverphoto_navigation">
        <ul>
          <?php foreach( $this->eventNavigation as $link ): ?>
            <li>
             <a class="<?php echo  'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ); ?>" href='<?php echo $link->getHref() ?>'>
               <span><?php echo $this->translate($link->getlabel()) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </span>
    </div>
  </div>
<?php endif; ?>
<div class="clr"></div>
