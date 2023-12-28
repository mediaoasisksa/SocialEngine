<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10018 2013-03-27 01:36:15Z john $
 * @author     John
 */
?>

<?php

$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer.js');
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer_tag.js');
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<?php if( (!empty($this->feedOnly) || !$this->endOfFeed ) &&
    (empty($this->getUpdate) && empty($this->checkUpdate)) ): ?>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      
      var activity_count = <?php echo sprintf('%d', $this->activityCount) ?>;
      var next_id = <?php echo sprintf('%d', $this->nextid) ?>;
      var subject_guid = '<?php echo $this->subjectGuid ?>';
      var endOfFeed = <?php echo ( $this->endOfFeed ? 'true' : 'false' ) ?>;

      var activityViewMore = window.activityViewMore = function(next_id, subject_guid) {
        if(en4.core.request.isRequestActive()) return;
        
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';         
        scriptJquery('#feed_viewmore')[0].style.display = 'none';
        scriptJquery('#feed_loading')[0].style.display = '';
        
          var request = scriptJquery.ajax({
          url : url,
          dataType : 'html',
          data : {
            format : 'html',
            maxid : next_id,
            feedOnly : true,
            nolayout : true,
            subject : subject_guid,
            search : '<?php echo $this->search ?>',
            isHashtagPage : '<?php echo $this->isHashtagPage ?>',
          },
          evalScripts : true,
          success : function(responseHTML) {
            scriptJquery(responseHTML).appendTo(scriptJquery('#activity-feed'));
            en4.core.runonce.trigger();
            Smoothbox.bind(scriptJquery('#activity-feed'));
          }
        });
      }
      
      if( next_id > 0 && !endOfFeed ) {
        scriptJquery('#feed_viewmore').css('display','');
        scriptJquery('#feed_loading').css('display','none');
        scriptJquery('#feed_viewmore_link').off('click').on('click', function(event){
          event.preventDefault();
          activityViewMore(next_id, subject_guid);
        });
      } else {
        scriptJquery('#feed_viewmore').css('display','none');
        scriptJquery('#feed_loading').css('display','none');
      }
      
    });
  </script>
<?php endif; ?>

<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // Simple feed only for AJAX
  echo $this->activityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'similarActivities' => $this->similarActivities,
    'getUpdate' => $this->getUpdate,
    'viewMaxPhoto' => $this->viewMaxPhoto,
    'hashtag' => $this->hashtag
  ));
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if( !empty($this->checkUpdate) ): // if this is for the live update
  if ($this->activityCount)
  echo "<script type='text/javascript'>
          document.title = '($this->activityCount) ' + activityUpdateHandler.title;
          activityUpdateHandler.options.next_id = ".$this->firstid.";
        </script>

        <div class='tip'>
          <span>
            <a href='javascript:void(0);' onclick='javascript:activityUpdateHandler.getFeedUpdate(".$this->firstid.");$(\"feed-update\").empty();'>
              {$this->translate(array(
                  '%d new update is available - click this to show it.',
                  '%d new updates are available - click this to show them.',
                  $this->activityCount),
                $this->activityCount)}
            </a>
          </span>
        </div>";
  return; // Do no render the rest of the script in this mode
endif; ?>

<?php if ($this->isHashtagPage): ?>
   <script type="text/javascript">
     <?php if ($this->search):?>
      var text = 'Trending Posts: <a href = "<?php echo $this->url(array('controller' => 'hashtag', 'action' => 'index'), "core_hashtags")
        . "?search=" . urlencode('#' . $this->search);?>"><?php echo "#" . $this->search; ?></a>';
    <?php else:?>
      var text = 'Trending Posts';
    <?php endif;?>
    scriptJquery('.layout_activity_feed').find('h3:first').html(text);
   </script>
<?php endif;?>

<?php if( !empty($this->getUpdate) ): // if this is for the get live update ?>
   <script type="text/javascript">
     activityUpdateHandler.options.last_id = <?php echo sprintf('%d', $this->firstid) ?>;
   </script>
<?php endif; ?>

<?php if( $this->enableComposer && !$this->isHashtagPage): ?>
  <div class="activity-post-container nolinks">

    <form method="post" action="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="activity-form">
      <textarea id="activity_body" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Post Something...')) ?>"></textarea>
      <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
      <?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
        <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
      <?php endif; ?>
      <?php if( $this->formToken ): ?>
        <input type="hidden" name="token" id="token" value="<?php echo $this->formToken ?>" />
      <?php endif ?>
        <div id="compose-menu" class="compose-menu">
         <div class="compose-right-content">
          <?php if (!empty($this->privacyList) || !empty($this->networkList)): ?>
            <script type="text/javascript">
              var togglePrivacyPulldownEnable = true;
              scriptJquery(document).ready(function () {
                scriptJquery(document.body).on('click', function() {
                  if (togglePrivacyPulldownEnable && scriptJquery('#privacy_pulldown').hasClass('privacy_pulldown_active')) {
                    scriptJquery('#privacy_pulldown').addClass('privacy_pulldown').removeClass('privacy_pulldown_active');
                  }
                  togglePrivacyPulldownEnable = true;
                });
              });
              var togglePrivacyPulldown = function ( element) {
                scriptJquery('.privacy_list').each(function (evnet) {
                  var otherElement = scriptJquery(this);
                  var pulldownElement = otherElement.find('#privacy_pulldown_active');
                  if (pulldownElement) {
                    pulldownElement.addClass('privacy_pulldown').removeClass('privacy_pulldown_active');
                  }
                });
                if (scriptJquery(element).hasClass('privacy_pulldown')) {
                  scriptJquery(element).removeClass('privacy_pulldown').addClass('privacy_pulldown_active');
                } else {
                  //element.addClass('privacy_pulldown').removeClass('privacy_pulldown_active');
                }
                if (scriptJquery(element).hasClass('privacy_pulldown_active')) {
                  setTimeout(function() {
                     composeInstance.getMenu().css('display', '');
                   },300);
                }
                togglePrivacyPulldownEnable = false;
              }

              var setPrivacyValue = function (value, label, classicon) {
                scriptJquery('li.activity_tab_active').each(function (el) {
                  scriptJquery(this).removeClass('activity_tab_active').addClass('activity_tab_unactive');
                });
                scriptJquery('#privacy_list_' + value).addClass('activity_tab_active').removeClass('activity_tab_unactive');
                scriptJquery('#auth_view').val(value);
                scriptJquery('#privacy_pulldown_button').html('<i class="privacy_pulldown_icon ' + classicon + ' "></i><span>' + label + ' </span><i class="fa fa-caret-down"></i>');
                scriptJquery("#privacy_lable_tip").html(en4.core.language.translate("<?php echo $this->string()->escapeJavascript($this->translate('Share with %s')) ?>", label));
              }

              var showMultiNetworks = function () {
                Smoothbox.open('<?php echo $this->url(array(
                  'module' => 'activity',
                  'controller' => 'index',
                  'action' => 'add-multiple-networks'), 'default', true) ?>');
                var element = scriptJquery('#privacy_pulldown');
                if(scriptJquery(element).hasClass('privacy_pulldown') ) {
                  element.removeClass('privacy_pulldown').addClass('privacy_pulldown_active');
                } else {
                  element.addClass('privacy_pulldown').removeClass('privacy_pulldown_active');
                }
              }
            </script>
            <div class='privacy_list' id='privacy_list'>
              <span class="privacy_pulldown" id="privacy_pulldown" onmousedown="togglePrivacyPulldown(this);">
                <p class="privacy_list_tip">
                  <span id="privacy_lable_tip">
                    <?php echo $this->translate("Share with %s", $this->defaultPrivacyLabel) ?>
                  </span>
                  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Activity/externals/images/tooltip-arrow-down.png" alt="" />
                </p>
                <a href="javascript:void(0);" id="privacy_pulldown_button" class="privacy_pulldown_button">
                  <i class="privacy_pulldown_icon <?php echo $this->defaultPrivacyClass ?>"></i>
                  <span>
                    <?php echo !empty($this->defaultPrivacyLabel) ? $this->translate($this->defaultPrivacyLabel) : '' ?>
                  </span>
                  <i class="fa fa-caret-down"></i>
                </a>
                <div class="privacy_pulldown_contents_wrapper">
                  <div class="privacy_pulldown_contents">
                    <ul id="privacylist">
                      <?php foreach( $this->privacyList as $key => $value ): ?>
                        <li class="<?php echo ( $key == $this->defaultPrivacy ? 'activity_tab_active' : 'activity_tab_unactive' ) ?>" id="privacy_list_<?php echo $key ?>" onclick="setPrivacyValue('<?php echo $key ?>', '<?php echo $this->string()->escapeJavascript($this->translate($value)); ?>', 'activity_icon_feed_<?php echo $key ?>')" title="<?php echo $this->translate("Share with %s", $value); ?>" >
                          <i class="privacy_pulldown_icon activity_icon_feed_<?php echo $key ?>"></i>
                          <div><?php echo $this->translate($value); ?></div>
                          </li>
                      <?php endforeach; ?>
                      <?php if(!empty($this->privacyList) && !empty($this->networkList)): ?>
                        <li class="sep"></li>
                      <?php endif;?>
                      <?php foreach( $this->networkList as $key => $value ): ?>
                        <li class="<?php echo ( $key == $this->defaultPrivacy ? 'activity_tab_active' : 'activity_tab_unactive' ) ?>" id="privacy_list_<?php echo $key ?>" onclick="setPrivacyValue('<?php echo $key ?>', '<?php echo $this->string()->escapeJavascript($this->translate($value)); ?>', 'activity_icon_feed_network')" title="<?php echo $this->translate("Share with %s", $value); ?>" >
                          <i class="privacy_pulldown_icon activity_icon_feed_network"></i>
                          <div><?php echo $this->translate($value); ?></div>
                        </li>
                      <?php endforeach; ?>
                      <?php if(is_array($this->networkList) && engine_count($this->networkList) > 1): ?>
                        <li class="sep"></li>
                        <li onclick="showMultiNetworks();">
                          <i class="privacy_pulldown_icon activity_icon_feed_network"></i>
                          <div><?php echo $this->translate("Multiple Networks"); ?></div>
                        </li>
                      <?php endif;?>
                    </ul>
                  </div>
                </div>
              </span>
            </div>
          <?php endif;?>
          <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $this->defaultPrivacy; ?>" />
          <button id="compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
          </div>
        </div>
    </form>
    <script type="text/javascript">
      var composeInstance;
      en4.core.runonce.add(function() {
        // @todo integrate this into the composer
        if( true ) {
          try {
            composeInstance = new Composer('#activity_body', {
              menuElement : '#compose-menu',
              hashtagEnabled : '<?php echo $this->hashtagEnabled ?>',
              baseHref : '<?php echo $this->baseUrl() ?>',
              postLength : <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_postLength',1000); ?>,
              lang : {
                'Post Something...' : '<?php echo $this->string()->escapeJavascript($this->translate('Post Something...')) ?>'
              },
              submitCallBack : en4.activity.post
            });
          }catch(err){ console.log(err); }
        }
      });
    </script>
    <?php foreach( $this->composePartials as $partial ): ?>
      <?php if (false !== strpos($partial[0], '_composeTag') && is_array($this->composerOptions) && !engine_in_array('userTags', $this->composerOptions)) {
        continue;
      }?>
      <?php echo $this->partial($partial[0], $partial[1], array(
        'composerType' => 'activity'
      )) ?>
    <?php endforeach; ?>
  </div>
  <?php if (is_array($this->composerOptions) && engine_in_array("emoticons", $this->composerOptions)): ?>
    <?php $emoticonsTag = Engine_Api::_()->activity()->getEmoticons();
        if (!empty($emoticonsTag)): ?>
        <script type="text/javascript">
          var hideEmoticonsBox = false;
          function htmlunescape(content) {
            var doc = new DOMParser().parseFromString(content, "text/html");
            return doc.documentElement.textContent;
          }

          function setEmoticonsBoard() {
            if (composeInstance) {
              composeInstance.focus();
            }

            hideEmoticonsBox = true;
            scriptJquery('#emoticons-activator').toggleClass('emoticons_active');
            scriptJquery('#emoticons-activator').toggleClass('');
            scriptJquery('#emoticons-board').toggleClass('emoticons_box_opened');
            scriptJquery('#emoticons-board').toggleClass('emoticons_box_closed');
            if (scriptJquery('#emoticons-activator').hasClass('emoticons_active')) {
              setTimeout(function() {
                composeInstance.getMenu().css('display', '');
              },300);
            }
          }

          function addEmoticonIcon(iconCode) {
            var content = htmlunescape(composeInstance.getContent());
            content = content.replace(/&nbsp;/g, ' ');
            if( !('useContentEditable' in composeInstance.options && composeInstance.options.useContentEditable )) {
              composeInstance.setContent(content + ' ' + iconCode);
              return;
            }
            var textBeforeCaret = content.substr(0, composeInstance.lastCaretPos);
            var textAfterCaret = content.substr(composeInstance.lastCaretPos);
            iconCode = ' ' + iconCode + ' ';
            composeInstance.setContent(textBeforeCaret + iconCode + textAfterCaret);
            composeInstance.setCaretPos(textBeforeCaret.length + iconCode.length);
            if ( composeInstance.getContent() !== '' ) {
              setTimeout(function() {
                composeInstance.getMenu().css('display', '');
              },300);
            }
          }

          scriptJquery(document.body).on('click',hideEmoticonsBoxEvent.bind());

          function hideEmoticonsBoxEvent() {
            if (!hideEmoticonsBox && scriptJquery('#emoticons-board').length) {
              scriptJquery('#emoticons-board').removeClass('emoticons_box_opened').addClass('emoticons_box_closed');
            }
            hideEmoticonsBox = false;
          }

          en4.core.runonce.add(function() {
            scriptJquery('#emoticons-activator').appendTo(scriptJquery('#compose-container'));
          });
        </script>
        <span id="emoticons-activator"  class="emoticons-activator"  onmousedown="setEmoticonsBoard()">
          <span id="emoticons-board"  class="emoticons_box emoticons_box_closed" >
          <span class="emoticons_box_arrow"></span>
          <?php foreach ($emoticonsTag as $symbol => $icon): ?>
            <span class="emoticons_box_icon" onmousedown='addEmoticonIcon("<?php echo $this->string()->escapeJavascript($symbol)?>")'>
              <?php echo "<img src=\"" . $this->layout()->staticBaseUrl .
                    "application/modules/Activity/externals/emoticons/images/$icon\" border=\"0\"/>" ?>
            </span>
          <?php endforeach; ?>
          </span>
        </span>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->updateSettings && !$this->action_id): // wrap this code around a php if statement to check if there is live feed update turned on ?>
  <script type="text/javascript">
    var activityUpdateHandler;
    en4.core.runonce.add(function() {
      activity_type = 1;
      try {
          activityUpdateHandler = new ActivityUpdateHandler({
            'baseUrl' : en4.core.baseUrl,
            'basePath' : en4.core.basePath,
            'identity' : 4,
            'delay' : <?php echo $this->updateSettings;?>,
            'last_id': <?php echo sprintf('%d', $this->firstid) ?>,
            'subject_guid' : '<?php echo $this->subjectGuid ?>'
          });
          setTimeout("activityUpdateHandler.start()",1250);
          //activityUpdateHandler.start();
          window._activityUpdateHandler = activityUpdateHandler;
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
    });
  </script>
<?php endif;?>

  <div class="tip" id="fail_msg" style="display: none;">
    <span>
      <?php $url = $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'privacy'), 'default', true) ?>
      <?php echo $this->translate('The post was not added to the feed. Please check your %1$sprivacy settings%2$s.', '<a href="'.$url.'">', '</a>') ?>
    </span>
  </div>
<div class="tip" id="flood_msg" style="display: none;">
    <span id="flood_msg_cnt">

    </span>
</div>
<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity ): ?>
  <?php if( $this->action_id ): ?>
    <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
    <p>
      <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
    </p>
  <?php return; else: ?>
    <div class="tip" id="no-feed-tip">
      <span>
        <?php
          if(!$this->isHashtagPage) {
            echo $this->translate("Nothing has been posted here yet - be the first!");
          } else {
            echo $this->translate("No results found!");
          } ?>
      </span>
    </div>
  <?php return; endif; ?>
<?php endif; ?>

<div id="feed-update"></div>

<?php echo $this->activityLoop($this->activity, array(
  'action_id' => $this->action_id,
  'viewAllComments' => $this->viewAllComments,
  'viewAllLikes' => $this->viewAllLikes,
  'similarActivities' => $this->similarActivities,
  'getUpdate' => $this->getUpdate,
  'viewMaxPhoto' => $this->viewMaxPhoto,
  'hashtag' => $this->hashtag
)) ?>

<div class="feed_viewmore" id="feed_viewmore" style="display: none;">
  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
    'id' => 'feed_viewmore_link',
    'class' => 'buttonlink icon_viewmore'
  )) ?>
</div>

<div class="feed_viewmore" id="feed_loading" style="display: none;">
  <i class="fa-spinner fa-spin fa"></i>
  <?php echo $this->translate("Loading ...") ?>
</div>

<script type="text/javascript">
  var showEditMultiNetworks = function (action_id) {
    var action_id = action_id;
    var activityEditLink = '<?php echo $this->url(array(
      'module' => 'activity',
      'controller' => 'index',
      'action' => 'edit-multiple-networks',
      'action_id'=>''), 'default', true) ?>';
    Smoothbox.open(`${activityEditLink}${action_id}`);
    var element = scriptJquery('#privacy_pulldown');
    if(element.hasClass('privacy_pulldown') ) {
      element.removeClass('privacy_pulldown').addClass('privacy_pulldown_active');
    } else {
      element.addClass('privacy_pulldown').removeClass('privacy_pulldown_active');
    }
  }
  function setEditPrivacyValue(privacy,action_id) {
    if(privacy=="multi_networks"){
      showEditMultiNetworks(action_id);
    }
  }
  
  		
		en4.core.runonce.add(function() {
			scriptJquery(".feed_item_body_content > .feed_item_bodytext").each(function(){
        var element = scriptJquery(this);
        element.clone().insertAfter(scriptJquery(this).closest(".feed_item_body_content"));
        element.remove();
      });
		});
    
</script>
