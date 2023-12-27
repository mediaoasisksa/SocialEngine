<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: share.tpl 9611 2012-01-24 02:13:10Z shaun $
 * @author     John
 */
?>
<?php $this->headLink()->appendStylesheet($this->seaddonsBaseUrl(). '/application/modules/Seaocore/externals/styles/styles.css'); ?>

<?php if (Engine_Api::_()->hasModuleBootstrap('sitehashtag')): ?>
<?php $this->headScript()
           ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advancedactivity/externals/scripts/composer.js');
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        composeInstance = new Composer(scriptJquery('.seaocore_share_popup')[0].getElementById('body'), {
            baseHref : '<?php echo $this->baseUrl() ?>',
            lang : {
              'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Say something about this...')) ?>'
            },
            hideSubmitOnBlur: false,
            allowEmptyWithoutAttachment: true,
          });
    });
</script>
<?php
$composePartials = array(array( '_composerHashtag.tpl', 'sitehashtag'));
?>
<?php foreach( $composePartials as $partial ): ?>
    <?php echo $this->partial($partial[0], $partial[1],  array("isAFFWIDGET"=>1)) ?>
<?php endforeach; ?>
<?php endif; ?>

<div class="seaocore_share_popup" >
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
  <br />
  <div class="sharebox">
    <?php if($this->attachment->getType() === 'activity_action' && Engine_Api::_()->hasModuleBootstrap('advancedactivity')): ?>
        <ul class='feed feed_sections_left_round' id="activity-feed">
          <li class="activity-item">
        <?php echo $this->getRichContent($this->attachment);?>
          </li>
        </ul>
      <?php else: ?>
    <?php if( $this->attachment->getPhotoUrl() ): ?>
      <div class="sharebox_photo">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
      </div>
    <?php endif; ?>
    <div>
      <div class="sharebox_title">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
      </div>
      <div class="sharebox_description">
        <?php echo $this->attachment->getDescription() ?>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<script type="text/javascript">
//<![CDATA[
var toggleFacebookShareCheckbox, toggleTwitterShareCheckbox;
(function(scriptJquery) {
  toggleFacebookShareCheckbox = function(){
      scriptJquery('span.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active');
      scriptJquery('input[name=post_to_facebook]').set('checked', scriptJquery('span.composer_facebook_toggle')[0].hasClass('composer_facebook_toggle_active'));
  }
  toggleTwitterShareCheckbox = function(){
      scriptJquery('span.composer_twitter_toggle').toggleClass('composer_twitter_toggle_active');
      scriptJquery('input[name=post_to_twitter]').set('checked', scriptJquery('span.composer_twitter_toggle')[0].hasClass('composer_twitter_toggle_active'));
  }
})(scriptJquery)
//]]>
</script>