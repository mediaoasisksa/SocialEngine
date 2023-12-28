<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10105 2013-10-29 21:32:15Z guido $
 * @author     John
 */
?>

<?php if( $this->canChat ): ?>

  <?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/desktop-notify/desktop-notify'
          . ( APPLICATION_ENV != 'development' ? '-min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Chat/externals/scripts/core.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/jQuery/jquery.idle.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect'
          . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js');
    $this->headTranslate(array(
      'The chat room has been disabled by the site admin.', 'Browse Chatrooms',
      'You are sending messages too quickly - please wait a few seconds and try again.',
      '%1$s has joined the room.', '%1$s has left the room.', 'Settings',
      'Friends Online', 'None of your friends are online.', 'Go Offline', 'Toggle Notifications',
      'Open Chat', 'General Chat', 'Introduce Yourself', '%1$s person',
    ));
    $defaultChatContainer = 'chat_content_layout';
  ?>
  <div id="<?php echo $defaultChatContainer ?>" ></div>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
        if( !$type(window._chatHandler) ) {
          chatHandler = new ChatHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            //'identity' : <?php echo sprintf('%d', $this->viewer()->getIdentity()) ?>,
            'enableIM' : <?php echo $this->canIM ? 'true' : 'false' ?>,
            'enableChat' : <?php echo $this->canChat ? 'true' : 'false' ?>,
            'delay' : <?php echo sprintf('%d', Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000')); ?>,
            'chatOptions' : {
              'operator' : <?php echo sprintf('%d', (int) $this->isOperator) ?>,
              'roomList' : <?php echo Zend_Json::encode($this->rooms) ?>,
              'container' : '#<?php echo $this->chatContainer ?  $this->chatContainer : $defaultChatContainer ?>'
            }
          });
          chatHandler.start();
          window._chatHandler = chatHandler;
          return;
        }
        if( $type(window._chatHandler) ) {
          window._chatHandler.startChat({
            operator : <?php echo sprintf('%d', (int) $this->isOperator) ?>,
            roomList : <?php echo Zend_Json::encode($this->rooms) ?>,
            container : '#<?php echo $this->chatContainer ?  $this->chatContainer : $defaultChatContainer ?>'
          });
        }
    });
  </script>
<?php else: ?>

  <div><?php echo $this->translate('The chat room has been disabled by the site admin.')?></div>
  
<?php endif; ?>

  
