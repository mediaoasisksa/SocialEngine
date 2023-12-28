<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: view.tpl 9990 2013-03-20 19:59:59Z john $
 * @author     Sami
 */
?>

<div class="layout_page_event_topic_view">
  <div class="generic_layout_container layout_main">
    <div class="generic_layout_container layout_middle">
      <div class="generic_layout_container layout_core_content">
        <div class="event_discussions_topic_head_wrapper">
          <div class="event_discussions_topic_head">
            <h2> <?php echo $this->event->__toString() ?> <?php echo $this->translate('&#187;'); ?> <?php echo $this->htmlLink(array(
          'route' => 'event_extended',
          'controller' => 'topic',
          'action' => 'index',
          'event_id' => $this->event->getIdentity(),
          ), $this->translate('Discussions')) ?> </h2>
          </div>
          <div class="event_discussions_topic_title_options"> <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'topic', 'action' => 'index', 'event_id' => $this->event->getIdentity()), $this->translate('Back to Topics'), array(
          'class' => 'buttonlink icon_back'
          )) ?>
          <?php if( $this->canPost && !$this->topic->closed): ?>
          <?php echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
          'class' => 'buttonlink icon_event_post_reply'
          )) ?>
          <?php endif; ?>
          <?php if( $this->viewer->getIdentity() ): ?>
          <?php if( !$this->isWatching ): ?>
          <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
          'class' => 'buttonlink icon_event_topic_watch'
          )) ?>
            <?php else: ?>
            <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
          'class' => 'buttonlink icon_event_topic_unwatch'
          )) ?>
          <?php endif; ?>
          <?php endif; ?>
        </div>
        </div>
        <div class="event_discussions_topic_title_wrapper">
          <div class="event_discussions_topic_title">
              <h3><?php echo $this->topic->getTitle() ?></h3>
          </div>
          <div class="event_discussions_topic_options">
          <?php if( $this->canEdit && $this->topic->user_id == $this->viewer()->getIdentity()): ?>
            <?php if( !$this->topic->sticky ): ?>
            <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
            'class' => 'buttonlink icon_event_post_stick'
            )) ?>
            <?php else: ?>
            <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
            'class' => 'buttonlink icon_event_post_unstick'
            )) ?>
            <?php endif; ?>
            <?php if( !$this->topic->closed ): ?>
            <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
            'class' => 'buttonlink icon_event_post_close'
            )) ?>
            <?php else: ?>
            <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
            'class' => 'buttonlink icon_event_post_open'
            )) ?>
            <?php endif; ?>
            <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
            'class' => 'buttonlink smoothbox icon_event_post_rename'
            )) ?>
          <?php elseif( $this->event->isOwner($this->viewer()) == false): ?>
            <?php if( $this->topic->closed ): ?>
            <div class="event_discussions_thread_options_closed">
              <?php echo $this->translate('This topic has been closed.');?>
            </div>
            <?php endif; ?>
          <?php endif; ?>
          <?php if( $this->canDelete && $this->topic->user_id == $this->viewer()->getIdentity()): ?>
            <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
            'class' => 'buttonlink smoothbox icon_event_post_delete'
            )) ?>
          <?php endif; ?>
        </div>
        <?php if( $this->topic->closed ): ?>
        <div class="event_discussions_thread_options_closed"> <?php echo $this->translate('This topic has been closed.')?> </div>
        <?php endif; ?>
        <?php echo $this->placeholder('eventtopicnavi') ?> <?php echo $this->paginationControl(null, null, null, array(
        'params' => array(
        'post_id' => null // Remove post id
        )
        )) ?> 
        <script type="text/javascript">
          var quotePost = function(user, href, body) {
              if( $type(body) == 'element' ) {
                  body = $(body).getParent('li').getElement('.event_discussions_thread_body_raw').get('html').trim();
              }
              var value = '<blockquote>' + '[b][url=' + href + ']' + user + '[/url] <?php echo $this->translate('said');?>: [/b]\n' + htmlspecialchars_decode(body) + '</blockquote>\n\n';
          <?php if ( $this->form && ($this->form->body->getType() === 'Engine_Form_Element_TinyMce') ): ?>
              tinymce.activeEditor.execCommand('mceInsertContent', false, value);
              tinyMCE.activeEditor.focus();
          <?php else: ?>
              document.getElementById('body').value = value;
              $("body").focus();
          <?php endif; ?>
              scriptJquery('html, body').animate({ scrollTop: scriptJquery(document).height() }, 'fast');
          }
        </script>
        </div>
        <ul class='event_discussions_topic_posts'>
          <?php foreach( $this->paginator as $post ):
          $user = $this->item('user', $post->user_id);
          $isOwner = false;
          $isMember = false;
          $liClass = 'event_discussions_thread_author_none';
          if( $this->event->isOwner($user) ) {
          $isOwner = true;
          $isMember = true;
          $liClass = 'event_discussions_thread_author_isowner';
          } else if( $this->event->membership()->isMember($user) ) {
          $isMember = true;
          $liClass = 'event_discussions_thread_author_ismember';
          }
          ?>
          <li class="<?php echo $liClass ?>">
            <div class="event_discussions_topic_posts_author">
              <div class="event_discussions_topic_posts_author_photo"> <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.normal')) ?> </div>
              <div class="event_discussions_topic_posts_author_name"> <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?> </div>
              <ul class="event_discussions_topic_posts_author_info">
                <li class="event_discussions_topic_posts_author_info_title">
                  <?php
          if( $isOwner ) {
            echo $this->translate('Host');
                } else if( $isMember ) {
                echo $this->translate('Member');
                }
                ?>
                </li>
              </ul>
            </div>
            <div class="event_discussions_topic_posts_info">
              <div class="event_discussions_topic_posts_info_top">
                <div class="event_discussions_topic_posts_info_top_date">
                  <?php echo $this->locale()->toDateTime(strtotime($post->creation_date)) ?>
                </div>
                <div class="event_discussions_thread_details">
                  <div class="event_discussions_topic_posts_info_top_options">
                    <?php if( $this->form ): ?>
                      <?php if( $this->canPostCreate ): ?>
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Quote'), array(
                    'class' => 'buttonlink icon_event_post_quote',
                    'onclick' => 'quotePost("'.$this->escape($user->getTitle()).'", "'.$this->escape($user->getHref()).'", this);',
                    )) ?>
                      <?php endif; ?>
                    <?php endif; ?>
                    
                    
                    <?php if( $post->user_id == $this->viewer()->getIdentity() ||
                    $this->event->getOwner()->getIdentity() == $this->viewer()->getIdentity() ||
                    $this->canAdminEdit ): ?>
                    
                    <?php //if( (($post->user_id == $this->viewer()->getIdentity() || $this->event->getOwner()->getIdentity() == $this->viewer()->getIdentity()) || $this->viewer()->isSuperAdmin()) && $this->canPostEdit): ?>
                    <?php if($this->canPostEdit) { ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
                    'class' => 'buttonlink smoothbox icon_event_post_edit'
                    )) ?>
                    <?php } ?>
                    <?php //endif; ?>
                    <?php //if( (($post->user_id == $this->viewer()->getIdentity() || $this->event->getOwner()->getIdentity() == $this->viewer()->getIdentity()) || $this->viewer()->isSuperAdmin()) && $this->canPostDelete): ?>
                    <?php if($this->canPostDelete) { ?>
                    <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
                    'class' => 'buttonlink smoothbox icon_event_post_delete'
                    )) ?>
                    <?php } ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="rich_content_body event_discussions_topic_posts_info_body">
                <?php 
                  $body = $post->body;
                  if( strip_tags($body) == $body ) {
                    $body = nl2br($body);
                  }
                  if( !$this->decode_html && $this->decode_bbcode ) {
                    $body = $this->BBCode($body, array('link_no_preparse' => true));
                  }
                ?>
                <?php echo $body ?> </div>
              <span class="event_discussions_thread_body_raw" style="display: none;"> <?php echo $post->body; ?> </span> </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php if($this->paginator->getCurrentItemCount() > 4): ?>
        <?php echo $this->paginationControl(null, null, null, array(
        'params' => array(
        'post_id' => null // Remove post id
        )
        )) ?> <br />
        <?php echo $this->placeholder('eventtopicnavi') ?>
        <?php endif; ?>
        <?php if( $this->form ): ?>
        <a name="reply"></a> <?php echo $this->form->setAttrib('id', 'event_topic_reply')->render($this) ?>
        <?php endif; ?>
        <script type="text/javascript">
            scriptJquery('.core_main_event').parent().addClass('active');
						// Option Pulldown
		scriptJquery(document).on('click','.event_discussions_pulldown_toggle',function(){
			if(scriptJquery(this).hasClass('showpulldown')){
				scriptJquery(this).removeClass('showpulldown');
			}else{
				scriptJquery('.event_discussions_pulldown_toggle').removeClass('showpulldown');
				scriptJquery(this).addClass('showpulldown');
			}
				return false;
		});
		scriptJquery(document).click(function(){
			scriptJquery('.event_discussions_pulldown_toggle').removeClass('showpulldown');
		});
        </script> 
      </div>
    </div>
  </div>
