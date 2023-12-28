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
              <li <?php if( !$notification->read ): ?>class="notifications_unread clr"<?php endif; ?> value="<?php echo $notification->getIdentity(); ?>" style="overflow: hidden;">
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
    new SEAOMooVerticalScroll('seaocore_notification_main_right_content_area', 'seaocore_notification_scroll_main_right_content', {});
    $('notifications_main').addEvent('click', function (event) {
      event.stop(); //Prevents the browser from following the link.
      en4.seaocore.miniMenu.notifications.onClick(event);
    });
  });
</script>