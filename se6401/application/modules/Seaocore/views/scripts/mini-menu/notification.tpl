<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: notification.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js'); ?>

<?php $temp_flag = 0; ?>
<div class='seocore_pulldown_item_list'>
  <?php if( $this->notifications->getTotalItemCount() > 0 ): $temp_flag = 1; ?>
    <div id="seaocore_notification_main_right_content_area">
      <div id="seaocore_notification_scroll_main_right_content" class="seaocore_scroll_content">
        <ul class='notifications' id="notifications_main">
          <?php
          foreach( $this->notifications as $notification ):
            ob_start();
            try {
              ?>
              <li <?php if( !$notification->read ): ?>class="notifications_unread clr"<?php endif; ?> id="notifications_<?php echo $notification->getIdentity();?>" value="<?php echo $notification->getIdentity(); ?>" style="overflow: hidden;">
                <span class="notification_item_general aaf_update_pulldown">
                  <?php $item = $notification->getSubject() ?>      
                  <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', $item->getTitle())) ?>

                  <span class="aaf_update_pulldown_content">
                    <span class="aaf_update_pulldown_content_title fleft"><?php echo $notification->__toString() ?></span>
                    <span class="aaf_update_pulldown_content_stat notification_type_<?php echo $notification->type ?>"> 
                      <?php echo $this->timestamp(strtotime($notification->date)) ?>
                    </span>
                  </span>
                </span>
                
                <?php
                // check core version
              $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
              $checkVersion = Engine_Api::_()->seaocore()->checkVersion($coreVersion, '5.7.0');
              ?>
              <?php if($checkVersion): ?>
                <div class="rmv_notification">
                  <span class="notifications_delete_show"><i class="fa fa-ellipsis-h"></i></span>
                   <div id="block_<?php echo $notification->getIdentity(); ?>" style="display: none;" >   
                  <span id="remove_notification_update" onclick="removenotification(<?php echo $notification->getIdentity(); ?>)"><i class="far fa-times-circle"></i> Remove this Notification</span>
                  </div> 
                </div>
              <?php endif; ?>

              </li>
              <?php
            } catch( Exception $e ) {
              ob_end_clean();
              if( APPLICATION_ENV === 'development' ) {
                echo $e->__toString();
              }
              continue;
            }
            ob_end_flush();
          endforeach;
          ?>
        </ul>
      </div>
    </div>
  <?php else: $temp_flag = 0; ?>
    <div class="seaocore_pulldown_nocontent_msg">
      <?php echo $this->translate("You have no notifications.") ?>
    </div>
  <?php endif; ?>
</div>
<?php if( !empty($temp_flag) ): ?>
  <div class="seocore-pulldown-footer">
    <a href="<?php echo $this->url(array(), 'recent_activity', true) ?>" class="ui-link">
      <?php echo $this->translate('View All Notifications') ?>
    </a>
    <a href="javascript:void(0)" onclick="en4.seaocore.miniMenu.notifications.markAsRead();" class="fright ui-link">
      <?php echo $this->translate('Mark as Read') ?>
    </a>
  </div>
<?php endif; ?>
<script type="text/javascript">
  en4.core.runonce.add(function () {
    SEAOMooVerticalScroll('seaocore_notification_main_right_content_area', 'seaocore_notification_scroll_main_right_content', {});
    scriptJquery('notifications_main').addEventListener('click', function (event) {
      event.stop(); //Prevents the browser from following the link.
      en4.seaocore.miniMenu.notifications.onClick(event);
    });
  });
</script>
<?php if($checkVersion): ?>
<script type="text/javascript">
    function removenotification(notification_id) {
    scriptJquery.ajax({
      url: en4.core.baseUrl + 'activity/notifications/remove-notification',
      data: {
        format : 'json',
        notification_id: notification_id,
      },
      method:'post',
      dataType: 'json',
      success: function (response) {
        var result = response;
        if(result.status == 1) {
          scriptJquery('#notifications_'+notification_id).remove();
        }
      },
    });

    if (!e) var e = window.event;
    e.cancelBubble = true;
    if (e.stopPropagation) e.stopPropagation();
  }

  en4.core.runonce.add(function () {
    scriptJquery('.notifications_delete_show').on('click', function(event) {
      if(scriptJquery(this).hasClass('showdropdown')){
        scriptJquery(this).removeClass('showdropdown');
      }else{
        scriptJquery('.notifications_delete_show').removeClass('showdropdown');
        scriptJquery(this).addClass('showdropdown');
      }
        return false;
    });
  });
</script>
<?php endif; ?>