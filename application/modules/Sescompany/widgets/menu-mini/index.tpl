<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sescompany
 * @package    Sescompany
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-06-17 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $showPopup = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popup.enable', 1);?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php if($this->viewer->getIdentity() == 0) : ?>
  <script src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/jquery.min.js"></script>
  <script src="<?php echo $baseUrl; ?>application/modules/Sescompany/externals/scripts/jquery.magnific-popup.js"></script>
  <link href="<?php echo $baseUrl; ?>application/modules/Sescompany/externals/styles/magnific-popup.css" rel="stylesheet" />
<?php endif;?>
  
<?php $request = Zend_Controller_Front::getInstance()->getRequest();?>
<?php $controllerName = $request->getControllerName();?>
<?php $actionName = $request->getActionName();?>
  
<?php $showSeparator = 0;?> 
<?php $settings = Engine_Api::_()->getApi('settings', 'core');?>
<?php $facebook = Engine_Api::_()->getDbtable('facebook', 'user')->getApi();?>
<?php if ('none' != $settings->getSetting('core_facebook_enable', 'none') && $settings->core_facebook_secret && $facebook):?>
  <?php $showSeparator = 1;?>
<?php elseif ('none' != $settings->getSetting('core_twitter_enable', 'none') && $settings->core_twitter_secret):?>
  <?php $showSeparator = 1;?>
<?php endif;?>
  
<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title','1');?>
<?php if($this->viewer->getIdentity() == 0):?>
  <div id="small-dialog" class="zoom-anim-dialog mfp-hide company_quick_popup company_quick_login_popup sesbasic_bxs">
    <div class="company_popup_header clearfix">
    	<div class="company_popup_header_title"><?php echo $this->translate("Sign In");?></div>  
    </div>
    
    
    <?php if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('sessociallogin')): ?>
      <?php $numberOfLogin = Engine_Api::_()->sessociallogin()->iconStyle();?>
      <div class="company_social_login_btns <?php if($numberOfLogin < 3):?>social_login_btns_label<?php endif;?>">
        <?php  echo $this->partial('_socialLoginIcons.tpl','sessociallogin',array()); ?>
      </div>
    <?php endif; ?>
    <div class="company_popup_content clearfix">
      <?php if(!empty($showSeparator) && !Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('sessociallogin')):?>
        <div class="company_quick_popup_social">
          <?php if ('none' != $settings->getSetting('core_facebook_enable', 'none') && $settings->core_facebook_secret):?>
            <?php if (!$facebook):?>
            <?php return; ?>
            <?php endif;?>
          <?php $href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth','action' => 'facebook'), 'default', true);?>
            <a href="<?php echo $href;?>" id="fbLogin" class="company_facebook_btn company_social_btn"><span><i class="fa fa-facebook"></i>Sign in with Facebook</span></a>
          <?php endif;?>
          <?php if ('none' != $settings->getSetting('core_twitter_enable', 'none') && $settings->core_twitter_secret):?>
            <?php $href = Zend_Controller_Front::getInstance()->getRouter() ->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'twitter'), 'default', true);?>
            <a href="<?php echo $href;?>" id="googleLogin" class="company_twitter_btn company_social_btn"><span><i class="fa fa-twitter"></i>Sign in with Twitter</span></a>
          <?php endif;?>
        </div>
      <?php endif;?>
      <div class="company_quick_popup_form">
        <?php echo $this->content()->renderWidget("sescompany.login-or-signup")?>
      </div>

    </div>
    <div class="company_popup_footer clearfix">
			<?php if($controllerName != 'signup'){ ?>
      	<span>
            <?php echo $this->translate("No account yet?");?> <a class="popup-with-move-anim tab-link" href="#user_signup_form"><?php echo $this->translate("Sign Up Now!");?></a>
        </span>
      <?php } ?>
    </div>
  </div>

  <?php if($controllerName != 'signup'){ ?>
    <div id="user_signup_form" class="zoom-anim-dialog mfp-hide company_quick_popup sesbasic_bxs company_quick_signup_popup">
      <div class="company_popup_header clearfix">
      	<div class="company_popup_header_title"><?php echo $this->translate("Sign Up");?></div>  
      </div>
      <?php if(Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('sessociallogin')):?>
        <?php $numberOfLogin = Engine_Api::_()->sessociallogin()->iconStyle();?>
        <div class="company_social_login_btns <?php if($numberOfLogin < 3):?>social_login_btns_label<?php endif;?>">
          <?php  echo $this->partial('_socialLoginIcons.tpl','sessociallogin',array()); ?>
        </div>
      <?php endif; ?>
      <div class="company_popup_content clearfix">
         <?php if(!empty($showSeparator) && !Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled('sessociallogin')):?>
          <div class="company_quick_popup_social">
            <?php if ('none' != $settings->getSetting('core_facebook_enable', 'none') && $settings->core_facebook_secret):?>
              <?php if (!$facebook):?>
                <?php return; ?>
              <?php endif;?>
              <?php $href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'auth','action' => 'facebook'), 'default', true);?>
              <a href="<?php echo $href;?>" id="fbLogin" class="company_facebook_btn company_social_btn"><span><i class="fa fa-facebook"></i>Sign Up with Facebook</span></a>
            <?php endif;?>
  
            <?php if ('none' != $settings->getSetting('core_twitter_enable', 'none') && $settings->core_twitter_secret):?>
              <?php $href = Zend_Controller_Front::getInstance()->getRouter() ->assemble(array('module' => 'user', 'controller' => 'auth', 'action' => 'twitter'), 'default', true);?>
              <a href="<?php echo $href;?>" id="googleLogin" class="company_twitter_btn company_social_btn"><span><i class="fa fa-facebook"></i>Sign Up with Twitter</span></a>
            <?php endif;?>
          </div>
        <?php endif;?>
        <div class="company_quick_popup_form">
          <?php if($this->poupup && $controllerName != 'auth' && $actionName != 'login'){ ?>
            <?php echo $this->action("index", "signup", "sescompany", array()) ?>
          <?php } ?>
          <div class="company_popup_footer clearfix">
        <span>Already a member? <a class="popup-with-move-anim tab-link" href="#small-dialog"><?php echo $this->translate("Sign In");?></a></span>
      </div>
        </div>
      </div>
    </div>
  <?php } ?>
<?php endif;?>

<div id='core_menu_mini_menu'>
  <?php
    // Reverse the navigation order 
    $count = count($this->navigation);
    foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
  ?>
  <ul class="company_minimenu_links">
       <li class="company_minimenu_link company_minimenu_login">
          <?php echo $this->translate('%1$sJoin Zoom Meeting%2$s', '<a href="'.$this->url(array(), "core_zoommeeting").'" style="background-color: green !important;color: white;">', '</a>'); ?>
      </li>
      
    <?php foreach( $this->navigation as $item ): ?>
     
      <?php if(end(explode(' ', $item->class)) == 'core_mini_signup'):?>
        <li class="company_minimenu_link company_minimenu_signup">
          <?php if($controllerName != 'signup'){ ?>
            <a id="popup-signup" <?php if(0){ ?> <?php if($this->poupup && $controllerName != 'auth' && $actionName != 'login'){ ?> class="popup-with-move-anim" <?php } ?> <?php } ?> href="<?php if($this->poupup && $controllerName != 'auth' && $actionName != 'login'){ ?>/signup<?php }else{ echo 'signup'; } ?>">
            
         
              <?php echo $this->translate($item->getLabel());?>
            </a>
          <?php } ?>
        </li>
      <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() == 0):?>
        <?php if($controllerName != 'auth'){ ?>
        <li class="company_minimenu_link company_minimenu_login">
          <a id="popup-login" <?php if($this->poupup){ ?> class="popup-with-move-anim" <?php } ?> href="<?php if($this->poupup){ ?>#small-dialog <?php }else{ echo 'login'; } ?>">
         
            <?php echo $this->translate($item->getLabel());?>
          </a>
        </li>
        <?php } ?>
        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_auth' && $this->viewer->getIdentity() != 0):?>
          <?php continue;?>
        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_friends'):?>
           <?php $friendRequestIcon = Engine_Api::_()->sescompany()->getMenuIcon('core_mini_friends');?>
           <?php if($this->viewer->getIdentity()):?>
             <li class="company_minimenu_request company_minimenu_icon" title="<?php echo $this->translate('Friends Invitation');?>">
            <?php if($this->requestCount):?>
              <span id="request_count_new" class="company_minimenu_count"><?php echo $this->requestCount ?></span>
            <?php else:?>
              <span id="request_count_new"></span>
            <?php endif;?>
          <span onclick="toggleUpdatesPulldown(event, this, '4', 'friendrequest');" style="display:block;" class="friends_pulldown">
            <div id="friend_request" class="company_pulldown_contents_wrapper">
              <div class="dropdown_caret"><span class="caret_outer"></span><span class="caret_inner"></span></div>
              <div class="company_pulldown_header">
                <?php echo $this->translate('Requests'); ?>
              </div>
              <div id="company_friend_request_content" class="company_pulldown_contents">
                <div class="pulldown_loading" id="friend_request_loading">
                  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="<?php echo $this->translate('Loading'); ?>" />
                </div>
              </div>
            </div>
            <?php if(empty($friendRequestIcon)):?>
              <a href="javascript:void(0);" id="show_request" class="fa fa-user-plus">
                <span><?php echo $this->translate('Friends Invitation');?></span>
              </a>
            <?php else:?>
              <a href="javascript:void(0);" id="show_request" style="background-image:url(<?php echo $this->storage->get($friendRequestIcon, '')->getPhotoUrl(); ?>);">
                <span><?php echo $this->translate('Friends Invitation');?></span>
              </a>
            <?php endif;?>
          </span>
        </li>
        <?php endif;?>
        <?php elseif(end(explode(' ', $item->class)) == 'core_mini_notification'):?>
        <?php $notificationIcon = Engine_Api::_()->sescompany()->getMenuIcon('core_mini_notification');?>
        <?php if($this->viewer->getIdentity()):?>
          <li id='core_menu_mini_menu_update' class="company_minimenu_updates company_minimenu_icon" title="<?php echo $this->translate('Notifications');?>">
              <?php if($this->notificationCount):?>
                <span id="notification_count_new" class="company_minimenu_count">
                  <?php echo $this->notificationCount ?>
                </span>
              <?php else:?>
                <span id="notification_count_new"></span>
              <?php endif;?>
            <span onclick="toggleUpdatesPulldown(event, this, '4', 'notifications');" style="display:block;" class="updates_pulldown">
              <div class="company_pulldown_contents_wrapper">
                <div class="dropdown_caret"><span class="caret_outer"></span><span class="caret_inner"></span></div>
                <div class="company_pulldown_header">
                  <?php echo $this->translate('Notifications'); ?>
                </div>
                <div class="company_pulldown_contents pulldown_content_list">
                  <ul class="notifications_menu" id="notifications_menu">
                    <div class="pulldown_loading" id="notifications_loading">
                      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="<?php echo $this->translate('Loading'); ?>" />
                    </div>
                  </ul>
                </div>
                <div class="pulldown_options">
                  <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                     $this->translate('View All Updates'),
                     array('id' => 'notifications_viewall_link')) ?>
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
                    'id' => 'notifications_markread_link',
                  )) ?>
                </div>
              </div>
              <?php if(empty($notificationIcon)):?>
                <a href="javascript:void(0);" id="updates_toggle" class="<?php if( $this->notificationCount ):?>new_updates<?php endif;?> fa fa-bell">
                	<span><?php echo $this->translate($this->locale()->toNumber($this->notificationCount)) ?></span>
                </a>
              <?php else:?>
                 <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?> style="background-image:url(<?php echo $this->storage->get($notificationIcon, '')->getPhotoUrl(); ?>);">
              <span><?php echo $this->translate($this->locale()->toNumber($this->notificationCount)) ?></span></a>
              <?php endif;?>
            </span>
          </li>
        <?php endif;?>
      <?php elseif(end(explode(' ', $item->class)) == 'core_mini_profile'):?>
        <li class="company_minimenu_profile" title="<?php echo $this->translate('My Profile');?>">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($this->viewer(), 'thumb.icon')) ?>
        </li>
      <?php elseif(end(explode(' ', $item->class)) == 'core_mini_settings'):?>
        <?php $settingIcon = Engine_Api::_()->sescompany()->getMenuIcon('core_mini_settings');?>
        <li class="company_minimenu_setting company_minimenu_icon" title="<?php echo $this->translate('Settings');?>">
          <span onclick="toggleUpdatesPulldown(event, this, '4', 'settings');" style="display:block;" class="settings_pulldown">
            <div id="user_settings" class="company_pulldown_contents_wrapper company-mini-menu-settings-pulldown">
              <div class="dropdown_caret"><span class="caret_outer"></span><span class="caret_inner"></span></div>
              <div class="company_pulldown_header">
                <?php echo $this->translate('Account & Settings');?>
              </div>
              <div id="company_user_settings_content" class="company_pulldown_contents">
                <div class="pulldown_loading">
                  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="<?php echo $this->translate('Loading'); ?>" />
                </div>
              </div>
            </div>
            <?php if(empty($settingIcon)):?>
              <a href="javascript:void(0);" id="show_settings" class="fa fa-gear">
              <span><?php echo $this->translate($item->getLabel());?></span>
              </a>
            <?php else:?>
              <a href="javascript:void(0);" id="show_settings" style="background-image:url(<?php echo $this->storage->get($settingIcon, '')->getPhotoUrl(); ?>);">
              	<span><?php echo $this->translate($item->getLabel());?></span>
              </a>
            <?php endif;?>
          </span>
        </li>
      <?php elseif(end(explode(' ', $item->class)) == 'core_mini_messages'):?>
        <?php $messageIcon = Engine_Api::_()->sescompany()->getMenuIcon('core_mini_messages');?>
        <li class="company_minimenu_message company_minimenu_icon" title="<?php echo $this->translate('Messages');?>">
          <?php if($this->messageCount):?>
            <span id="message_count_new" class="company_minimenu_count"><?php echo $this->messageCount ?></span>
          <?php else:?>
            <span id="message_count_new"></span>
          <?php endif;?>
          <span onclick="toggleUpdatesPulldown(event, this, '4', 'message');" style="display:block;" class="messages_pulldown">
            <div id="company_user_messages" class="company_pulldown_contents_wrapper company-mini-menu-messages-pulldown">
              <div class="dropdown_caret"><span class="caret_outer"></span><span class="caret_inner"></span></div>
              <div class="company_pulldown_header">
                <?php echo $this->translate('Messages'); ?>
                <a class="icon_message_new righticon fa fa-plus" title="<?php echo $this->translate('Compose New Message'); ?>" href="<?php echo $this->url(array('action' => 'compose'), 'messages_general') ?>"></a>
              </div>
              <div id="company_user_messages_content" class="company_pulldown_contents">
                <div class="pulldown_loading">
                  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="<?php echo $this->translate('Loading'); ?>" />
                </div>
              </div>
            </div>
            <?php if(empty($messageIcon)):?>
              <a href="javascript:void(0);" id="show_message" class="fa fa-comments">
              	<span><?php echo $this->translate($item->getLabel());?></span>
              </a>
            <?php else:?>
              <a href="javascript:void(0);" id="show_message" style="background-image:url(<?php echo $this->storage->get($messageIcon, '')->getPhotoUrl(); ?>);">
              	<span><?php echo $this->translate($item->getLabel());?></span>
              </a>
            <?php endif;?>
          </span>
        </li>
      <?php elseif(end(explode(' ', $item->class)) == 'core_mini_admin'):?>
        <?php continue;?>
      <?php else:?>
        <li class="company_minimenu_link">
          <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
          'class' => ( !empty($item->class) ? $item->class : null ),
          'alt' => ( !empty($item->alt) ? $item->alt : null ),
          'target' => ( !empty($item->target) ? $item->target : null ),
          'title' => $this->translate(strip_tags($item->getLabel())),
          ))) ?>
        </li>
      <?php endif;?>
    <?php endforeach; ?>
    <?php if($this->show_search && $this->headerview != 1): ?>
      <li class="minimenu_search_button company_minimenu_icon">
        <a title="<?php echo $this->translate('Click to Search'); ?>" href="javascript:void(0);" id="minimenu_header_searchbox_toggle" class="fa fa-search"></a>
      </li>
    <?php endif; ?>
  </ul>
</div>
<script type='text/javascript'>
  var notificationUpdater;
  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        $('notification_count_new').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });
  
  var previousMenu;
  var abortRequest;
  var toggleUpdatesPulldown = function(event, element, user_id, menu) {
    
    if (typeof(abortRequest) != 'undefined') {
      abortRequest.cancel();
    }

    if(event.target.className == 'company_pulldown_header')
    return;
   
    var hideNotification = 0;
    var hideMessage = 0;
    var hideSettings = 0;
    var hideFriendRequests = 0;
    if($$(".updates_pulldown_active").length > 0) {
      $$('.updates_pulldown_active').set('class', 'updates_pulldown');
      var hideNotification = 1;
    }

    if($$(".messages_pulldown_active").length > 0) {
      $$('.messages_pulldown_active').set('class', 'messages_pulldown');
      hideMessage = 1;
    }
    
    if($$(".settings_pulldown_active").length > 0) {
      $$('.settings_pulldown_active').set('class', 'settings_pulldown');
      hideSettings = 1;
    }
    
    if($$(".friends_pulldown_active").length > 0) {
      $$('.friends_pulldown_active').set('class', 'friends_pulldown');
      hideFriendRequests = 1;
    }
   
    if(menu == 'notifications' && hideNotification == 0) {
      
      if( element.className=='updates_pulldown') {
        element.className= 'updates_pulldown_active';
        showNotifications();
      } 
      else
        element.className='updates_pulldown';
    }
    else if(menu == 'message' && hideMessage == 0) {
      if( element.className=='messages_pulldown' ) {
        element.className= 'messages_pulldown_active';
        showMessages();
      } 
      else {
        element.className='messages_pulldown';
      }
    }
    else if(menu == 'settings' && hideSettings == 0) {
      if( element.className=='settings_pulldown' ) {
        element.className= 'settings_pulldown_active';
        showSettings();
      } 
      else {
        element.className='settings_pulldown';
      }
    }
    else if(menu == 'friendrequest' && hideFriendRequests == 0) {
      if( element.className=='friends_pulldown' ) {
        element.className= 'friends_pulldown_active';
        showFriendRequests();
      } 
      else {
        element.className='friends_pulldown';
      }
    }
    previousMenu = menu;
  }

  var showNotifications = function() {

    en4.activity.updateNotifications();
    abortRequest = new Request.HTML({
      'url' : en4.core.baseUrl + 'sescompany/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading iconsignup
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
        document.getElementById('notification_count_new').innerHTML = '';
        document.getElementById('notification_count_new').removeClass('company_minimenu_count');
      }
    });  en4.core.request.send(abortRequest, {
    'force': true
  });
    
  };

  function showSettings() {

    abortRequest = new Request.HTML({
      url : en4.core.baseUrl + 'sescompany/index/general-setting',
      data : {
        format : 'html'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
       document.getElementById('company_user_settings_content').innerHTML = responseHTML;
      }
    });
    en4.core.request.send(abortRequest, {
    'force': true
  });
  }

  function showMessages() {

    abortRequest = new Request.HTML({
      url : en4.core.baseUrl + 'sescompany/index/inbox',
      data : {
        format : 'html'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
       document.getElementById('company_user_messages_content').innerHTML = responseHTML;
       document.getElementById('message_count_new').innerHTML = '';
       document.getElementById('message_count_new').removeClass('company_minimenu_count');
      }
    }); 
    en4.core.request.send(abortRequest, {
    'force': true
  });
  }
  var popUp;
  function showFriendRequests() {

    abortRequest = new Request.HTML({
      url : en4.core.baseUrl + 'sescompany/index/friendship-requests',
      data : {
        format : 'html'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
       if(responseHTML) {
         document.getElementById('company_friend_request_content').innerHTML = responseHTML;
         document.getElementById('request_count_new').innerHTML = '';
         document.getElementById('request_count_new').removeClass('company_minimenu_count');
       }
       else {
        $('friend_request_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new friend request."));?>';
       }
      }
    }); 
    en4.core.request.send(abortRequest, {
    'force': true
  });
  }
  
  if('<?php echo $this->viewer->getIdentity();?>' == 0) {
    jQuery(document).ready(function() {
    <?php $forcehide =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popupfixed', false);    ?>
   	popUp=jQuery('.popup-with-move-anim').magnificPopup({
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,
        overflowY: 'auto',
				enableEscapeKey:<?php echo $forcehide ? "false" : "true" ; ?>,
				showCloseBtn:<?php echo $forcehide ? "false" : "true"; ?>,
				closeOnBgClick: <?php echo $forcehide ? "false" : "true"; ?>, // allow opening popup on middle mouse click. Always set it to true if you don\'t provide alternative source.
        closeBtnInside: true,
        preloader: true,
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-slide-bottom'
      });
      if(jQuery(".payment_form_signup"))
      jQuery(".payment_form_signup").attr('action',en4.core.baseUrl + 'signup');
    });  
		
		jQuery.magnificPopup.instance.close = function () {
   var day = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.popup.day', 5);?>';
   if(day != 0)
			setCookie("is_popup",'popo',day);
			jQuery.magnificPopup.proto.close.call(this);	
		}
		
   jQuery(document).ready(function(e){
			jQuery('#signup_account_form input,#signup_account_form input[type=email]').each(
						function(index){
								var input = jQuery(this);
								if(jQuery(this).closest('div').parent().css('display') != 'none' && jQuery(this).closest('div').parent().find('.form-label').find('label').first().length && jQuery(this).prop('type') != 'hidden' && jQuery(this).closest('div').parent().attr('class') != 'form-elements'){	
									if(jQuery(this).prop('type') == 'email' || jQuery(this).prop('type') == 'text' || jQuery(this).prop('type') == 'password'){
										jQuery(this).attr('placeholder',jQuery(this).closest('div').parent().find('.form-label').find('label').html());
									}
								}
						}
					)	
		});

		jQuery(document).ready(function(e){
			jQuery('#sesariana_form_login input,#sesariana_form_login input[type=email]').each(
						function(index){
								var input = jQuery(this);
								if(jQuery(this).closest('div').parent().css('display') != 'none' && jQuery(this).closest('div').parent().find('.form-label').find('label').first().length && jQuery(this).prop('type') != 'hidden' && jQuery(this).closest('div').parent().attr('class') != 'form-elements'){	
									if(jQuery(this).prop('type') == 'email' || jQuery(this).prop('type') == 'text' || jQuery(this).prop('type') == 'password'){
										jQuery(this).attr('placeholder',jQuery(this).closest('div').parent().find('.form-label').find('label').html());
									}
								}
						}
					)	
		});
  }
</script>
<script type="text/javascript">
// cookie get and set function
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toGMTString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
} 
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1);
			if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
	}
	return "";
}
// end cookie get and set function.
var popUpShow=getCookie('is_popup');
if('<?php echo $this->viewer->getIdentity();?>' == 0 && popUpShow == '' && '<?php echo $actionName != 'login' ;?>' && '<?php echo $controllerName != 'signup' ;?>') {
	jQuery(document).ready(function() {
    if('<?php echo $showPopup;?>' == '1' && '<?php echo $this->poupup ?>' == '1')
		document.getElementById("popup-login").click(); 
	});
}
window.addEvent('domready', function() {
	  $(document.body).addEvent('click', function(event){

      if(event.target.id != 'show_message' && event.target.id != 'show_request' && event.target.id != 'updates_toggle' &&  event.target.id != 'show_settings' && event.target.className != 'company_pulldown_header' && event.target.className != 'pulldown_loading' && event.target.id != 'minimenu_search_box') {

        if($$(".updates_pulldown_active").length > 0)
        $$('.updates_pulldown_active').set('class', 'updates_pulldown');

        if($$(".messages_pulldown_active").length > 0)
        $$('.messages_pulldown_active').set('class', 'messages_pulldown');

        if($$(".settings_pulldown_active").length > 0)
        $$('.settings_pulldown_active').set('class', 'settings_pulldown');
      
        if($$(".friends_pulldown_active").length > 0)
        $$('.friends_pulldown_active').set('class', 'friends_pulldown');  
        
//         if($('minimenu_search_box').hasClass('open_search')) {
//           $('minimenu_search_box').removeClass('open_search');
//           $('minimenu_header_searchbox_toggle').removeClass('active');
//         }
      }
    });
  <?php if($this->viewer->getIdentity() != 0) : ?>
    setInterval(function() {
      newUpdates();
    },20000);
  
    window.setInterval(function() {
      newMessages();
    },30000);
  
    window.setInterval(function() {
      newFriendRequests();
    },10000);
  <?php endif; ?>
});

	
  function newFriendRequests() {

    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sescompany/index/new-friend-requests',
      method : 'POST',
      data : {
        format : 'json'
      },
      onSuccess : function(responseJSON) 
      {
        if( responseJSON.requestCount && $("request_count_new") ) {
          $('updates_toggle').addClass('new_updates');
          $("request_count_new").style.display = 'block';
					if(responseJSON.requestCount > 0 && responseJSON.requestCount != '')
         		$("request_count_new").addClass('company_minimenu_count');
          $("request_count_new").innerHTML = responseJSON.requestCount;
					
        }
      }
    }));
  }
function newUpdates() {
  en4.core.request.send(new Request.JSON({
    url : en4.core.baseUrl + 'sescompany/index/new-updates',
    method : 'POST',
    data : {
      format : 'json'
    },
    onSuccess : function(responseJSON) 
    {
      if( responseJSON.notificationCount && $("notification_count_new") ) {
        $('updates_toggle').addClass('new_updates');
        $("notification_count_new").style.display = 'block';
        $("notification_count_new").innerHTML = responseJSON.notificationCount;
			  if(responseJSON.notificationCount > 0 && responseJSON.notificationCount != '')
        	$("notification_count_new").addClass('company_minimenu_count');
      }
    }
  }));
}  
  function newMessages() {
    en4.core.request.send(new Request.JSON({
      url : en4.core.baseUrl + 'sescompany/index/new-messages',
      method : 'POST',
      data : {
        format : 'json'
      },
      onSuccess : function(responseJSON) 
      {
        if( responseJSON.messageCount && $("message_count_new") ) {
          $('updates_toggle').addClass('new_updates');
          $("message_count_new").style.display = 'block';
					if(responseJSON.messageCount > 0 && responseJSON.messageCount != '')
        	$("message_count_new").addClass('company_minimenu_count');
          $("message_count_new").innerHTML = responseJSON.messageCount;
        }
      }
    }));
  }
</script>