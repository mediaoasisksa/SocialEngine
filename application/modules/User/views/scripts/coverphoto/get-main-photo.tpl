<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
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
              <?php if($this->user->getPhotoUrl('thumb.profile')) : ?>
                <span style="background-image:url('<?php echo $this->user->getPhotoUrl('thumb.profile');?>'); text-align:left;" id="user_profile_photo"></span>
              <?php else : ?>
                <span class="bg_item_photo bg_thumb_profile bg_item_photo_user bg_item_nophoto" id="user_profile_photo"></span>
              <?php endif;?>
            </td>
          </tr>
        </table>
      <?php else: ?>
        <table class="main_thumb_photo">
          <tr valign="middle">
            <td>
              <span class="bg_item_photo bg_thumb_profile bg_item_photo_user bg_item_nophoto" id="user_profile_photo"></span>
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
          <?php if (!empty($this->user->photo_id)) : ?>
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
                'user_id' => $this->user->user_id,
                'photoType' => 'profile'), 'user_coverphoto', true); ?>' class="profile_cover_icon_photo_upload smoothbox">
                <?php echo $this->translate('Upload Photo'); ?>
              </a>
            </li>
            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')):?>
              <li>
                <?php echo $this->htmlLink(
                  $this->url(array(
                    'action' => 'choose-from-albums',
                    'user_id' => $this->user->user_id,
                    'photoType' => 'profile'
                  ), 'user_coverphoto', true),
                  $this->translate('Choose from Albums'),
                  array(' class' => 'profile_cover_icon_photo_view smoothbox')); ?>
              </li>
            <?php endif; ?>
            <?php if (!empty($this->user->photo_id)) : ?>
              <li>
                <?php echo $this->htmlLink(
                  array('route' => 'user_coverphoto', 'action' => 'remove-cover-photo', 'user_id' => $this->user->user_id, 'photoType' => 'profile'),
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
          <?php foreach( $this->userNavigation as $link ): ?>
            <li>
              <a class="<?php echo  'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ); ?>" href='<?php echo $link->getHref() ?>'>
               <span><?php echo $this->translate($link->getlabel()) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </span>
      <?php if( $this->auth ): ?>
        <span class="profile_status_text" id="user_profile_status_container">
          <?php $status = Engine_Text_Emoji::decode($this->subject()->status); ?>
          <?php echo $this->viewMore($this->getHelper('getActionContent')->smileyToEmoticons($status)) ?>
          <?php if( !empty($this->subject()->status) && $this->subject()->isSelf($this->viewer())): ?>
            <a class="profile_status_clear" href="javascript:void(0);" onclick="en4.user.clearStatus();">(<?php echo $this->translate('clear') ?>)</a>
          <?php endif; ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
<div class="clr"></div>
<script>
scriptJquery(function () {
  scriptJquery('[data-toggle="tooltip"]').tooltip()
})
</script>
