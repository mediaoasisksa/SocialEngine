<?php
/**
* SocialEngine
*
* @category   Application_Core
* @package    Core
* @copyright  Copyright 2006-2021 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: index.tpl 9905 2021-11-09 $
* @author     John
*/
?>
<div class="admin_home_dashboard">
<div class="row">
<div class="col-md-6">
  <div class="admin_home_dashboard_item">
    <h3 class="header_section">
      <?php echo $this->translate("Quick Start") ?> <i class="fas fa-angle-double-right"></i>
    </h3>
    <ul class="admin_home_dashboard_links">
        <li>
          <ul>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'settings', 'action' => 'general'), 'admin_default', true) ?>" class="links_plugins">
                <?php echo $this->translate("Set Site Title") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'mail', 'controller' => 'settings', 'action' => 'settings'), 'admin_default', true) ?>" class="links_abuse">
                <?php echo $this->translate("Set up Mail") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'signup', 'action' => 'index'), 'admin_default', true) ?>" class="links_layout">
                <?php echo $this->translate("Set up Registration") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'settings', 'action' => 'spam'), 'admin_default', true) ?>" class="links_theme">
                <?php echo $this->translate("Enable Anti-Spam") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'authorization', 'controller' => 'level', 'action' => 'index'), 'admin_default', true) ?>" class="links_stats">
                <?php echo $this->translate("Set up Member Levels") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'iframely', 'action' => 'index'), 'admin_default', true) ?>" class="links_members">
                <?php echo $this->translate("Set Iframely for links") ?>
                </a>
              </li>
          </ul>
        </li>
    </ul>
  </div>
</div>
<div class="col-md-6">
  <div class="admin_home_dashboard_item">
    <h3 class="header_section">
      <?php echo $this->translate("Quick Links") ?> <i class="fas fa-angle-double-right"></i>
   </h3>
    <?php if( !empty($this->notifications) || $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="admin_home_dashboard_messages">
        <?php // Hook-based notifications ?>
        <?php if( !empty($this->notifications) ): ?>
        <?php foreach( $this->notifications as $notification ):
          if( is_array($notification) ) {
            $class = ( !empty($notification['class']) ? $notification['class'] : 'notification-notice priority-info' );
            $message = $notification['message'];
          } else {
            $class = 'notification-notice priority-info';
            $message = $notification;
          }
          ?>
        <li class="<?php echo $class ?>">
          <?php echo $message ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php // Database-based notifications ?>
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
        <?php foreach( $this->paginator as $notification ):
          $class = 'notification-' . ( $notification->priority >= 5 ? 'notice' : ( $notification->priority >= 4 ? 'warning' : 'error') )
            . ' priority-' . strtolower($notification->priorityName);
          $message = $notification->message;
          if( !empty($notification->plugin) ) {
            // Load and execute plugin
            try {
              $class = $notification->plugin;
              Engine_Loader::loadClass($class);
              if( !method_exists($class, '__toString') ) continue;
              $instance = new $class($notification);
              $message = $instance->__toString();
              if( method_exists($instance, 'getClass') ) {
                $class .= ' ' . $instance->getClass();
              }
            } catch( Exception $e ) {
              if( APPLICATION_ENV == 'development' ) {
                echo $e->getMessage();
              }
              continue;
            }
          }
          ?>
        <li class="<?php echo $class ?>">
          <?php echo $message ?>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
    <ul class="admin_home_dashboard_links">
        <li>
          <ul>
              <li>
                <a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'manage', 'action' => 'index'), 'admin_default', true) ?>" class="links_members">
                <?php echo $this->translate("View Members") ?>  (<?php echo $this->userCount ?>)
                </a>
               
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'index'), 'admin_default', true) ?>" class="links_abuse">
                <?php echo $this->translate("View Abuse Reports") ?>  <?php if( $this->reportCount > 0 ): ?>
                (<?php echo $this->reportCount ?>) <?php endif; ?>
                </a>
            
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'packages', 'action' => 'index'), 'admin_default', true) ?>" class="links_plugins">
                <?php echo $this->translate("Manage Plugins") ?>          (<?php echo $this->pluginCount ?>)
                </a>
      
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'content', 'action' => 'index'), 'admin_default', true) ?>" class="links_layout">
                <?php echo $this->translate("Edit Site Layout") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true) ?>" class="links_theme">
                <?php echo $this->translate("Edit Site Theme") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'stats', 'action' => 'index'), 'admin_default', true) ?>" class="links_stats">
                <?php echo $this->translate("View Statistics") ?>
                </a>
              </li>
              <li>
                <a href="<?php echo $this->url(array('module' => 'announcement', 'controller' => 'manage', 'action' => 'create'), 'admin_default', true) ?>" class="links_announcements">
                <?php echo $this->translate("Post Announcement") ?>
                </a>
              </li>
          </ul>
        </li>
       
    </ul>
  </div>
</div>
</div>
</div>