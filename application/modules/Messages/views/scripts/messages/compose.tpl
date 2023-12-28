<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: compose.tpl 10224 2014-05-15 18:45:45Z lucas $
 * @author     John
 */
?>
<?php
  $to = Zend_Controller_Front::getInstance()->getRequest()->getParam('to', 0);
  $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl."externals/selectize/css/normalize.css");
  $headScript = new Zend_View_Helper_HeadScript();
  $headScript->appendFile($this->layout()->staticBaseUrl.'externals/selectize/js/selectize.js');
?>
<script type="text/javascript">
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  en4.core.runonce.add(function() {
    scriptJquery('#to').selectize({
      maxItems: maxRecipients,
      valueField: 'id',
      labelField: 'label',
      searchField: 'label',
      //create: true,
      load: function(query, callback) {
          if (!query.length) return callback();
          scriptJquery.ajax({
            url: '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest','message' => true), 'default', true) ?>',
            data: { 
              value: query 
            },
            success: function (transformed) {
              callback(transformed);
            },
            error: function () {
                callback([]);
            }
          });
      }
    });
  });
</script>

<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<script type="text/javascript">
  var composeInstance;
  en4.core.runonce.add(function() {
    var tel = scriptJquery.crtEle('div', {
      'id' : 'compose-tray',
      'styles' : {
        'display' : 'none'
      }
    }).insertAfter(scriptJquery('submit'), 'before');
    
    <?php if(!empty($to)) { ?>
      var mel = scriptJquery.crtEle('div', {
        'id' : 'compose-menu',
				 'class' : 'compose-menu'
      }).insertAfter(scriptJquery('#buttons-wrapper'), 'after');
    <?php } else { ?>
      var mel = scriptJquery.crtEle('div', {
        'id' : 'compose-menu'
      }).insertAfter(scriptJquery('#submit'), 'after');
    <?php } ?>


    // @todo integrate this into the composer
    if ('<?php $id = Engine_Api::_()->user()->getViewer()->level_id;
    echo Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $id, 'editor');
         ?>' == 'plaintext' ) {
      //if( !Browser.Engine.trident && !DetectMobileQuick() && !DetectIpad() ) {
        composeInstance = new Composer('#body', {
          overText : false,
          menuElement : mel,
          trayElement: tel,
          baseHref : '<?php echo $this->baseUrl() ?>',
          hideSubmitOnBlur : false,
          allowEmptyWithAttachment : false,
          submitElement: 'submit',
          type: 'message'
        });
      //}
    }
  });
</script>
<?php foreach( $this->composePartials as $partial ): ?>
  <?php echo $this->partial($partial[0], $partial[1]); ?>
<?php endforeach; ?>
<div class="messages_compose_popup">
  <?php echo $this->form->render($this) ?>
</div>
