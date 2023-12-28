<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var CommentLikesTooltips;
  var pageComment = '<?php echo $this->page;?>';
  en4.core.runonce.add(function() {
    // Scroll to comment
    if( window.location.hash != '' ) {
      var hel = document.getElementById(window.location.hash);
      if( hel ) {
        window.scrollTo(hel);
      }
    }
    // Add hover event to get likes
    scriptJquery('.comments_comment_likes').on('mouseover', function(event) {
      var el = scriptJquery(event.target);
      if( !el.data('tip-loaded', false) ) {
        el.data('tip-loaded', true);
        el.data('tip:title', '<?php echo $this->translate('Loading...') ?>');
        el.data('tip:text', '');
        var id = el.attr('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' =>
'get-likes'), 'default', true) ?>';
        var req = scriptJquery.ajax({
          url : url,
          data : {
            format : 'json',
            type : 'core_comment',
            id : id
            //type : '<?php //echo $this->subject->getType() ?>',
            //id : '<?php //echo $this->subject->getIdentity() ?>',
            //comment_id : id
          },
          success : function(responseJSON) {
            el.data('tip:title', responseJSON.body);
            el.data('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips(scriptJquery('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 16,
        'y' : 16
      }
    });
     // Enable links
    scriptJquery('.comments_body').enableLinks();
  });
</script>

<?php $this->headTranslate(array('Are you sure you want to delete this?',)); ?>

<?php if( !$this->page ): ?>
<div class='comments' id="comments_<?php echo $this->subject->getType()?>_<?php echo
$this->subject->getIdentity() ?>">
<?php endif; ?>
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments',
$this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount()))
?></span>

    <?php if( isset($this->form) ): ?>
      - <a href='javascript:void(0);' onclick="if(scriptJquery('#comment-form-open-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>')){ scriptJquery('#comment-form-open-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').css('display', 'none'); } scriptJquery('#comment-form_<?php echo $this->subject->getType()?>_<?php
echo $this->subject->getIdentity() ?>').css('display', '');if(document.getElementById('comment-form_<?php echo
$this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').body) {document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').find('#body').focus();}"><?php echo $this->translate('Post Comment') ?></a>

    <?php endif; ?>
<?php if($this->subject->getType() != 'sitecontest_enty' && $this->subject->getType() != 'sitecontest_cont'): ?>
    <?php if( $this->viewer()->getIdentity() && $this->canComment ): ?>
      <?php if( $this->subject->likes()->isLike($this->viewer()) ): ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.comments.unlike('<?php echo
$this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $this->show_bottom_post ?>')"><?php echo
$this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="en4.seaocore.comments.like('<?php echo
$this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>','<?php echo $this->show_bottom_post ?>')"><?php echo
$this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  </div>
  <ul>

    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this',
$this->likes->getTotalItemCount()), $this->fluentList($this->subject->likes()->getAllLikesUsers())) ?>
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);',
                          $this->translate(array('%s person likes this', '%s people like this',
$this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('onclick' =>
'en4.seaocore.comments.showLikes("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'","'. $this->show_bottom_post.'");')
                      ); ?>
          </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'),
array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->page - 1).'","'.$this->show_bottom_post.'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'","'.$this->show_bottom_post .'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if( $this->page ):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for( ; $i != $e; $i += $d ):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);

    
     
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()));
        ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php echo $this->htmlLink($poster->getHref(),
              $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
            ) ?>
          </div>
          <div class="comments_info">
            <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle());
?></span>
             <span class="comments_body">
                <?php echo $this->smileyToEmoticons($this->viewMore($comment->body)); ?>
             </span> 
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $canDelete ): ?>
                -
                <a href="javascript:void(0);" onclick="en4.seaocore.comments.deleteComment('<?php
echo $this->subject->getType()?>', '<?php echo $this->subject->getIdentity() ?>', '<?php echo
$comment->comment_id ?>')">
                  <?php echo $this->translate('delete') ?>
                </a>
              <?php endif; ?>
              <?php if( $this->canComment ):
                $isLiked = $comment->likes()->isLike($this->viewer());
                ?>
                -
                <?php if( !$isLiked ): ?>
                  <a href="javascript:void(0)" onclick="en4.seaocore.comments.like(<?php echo
sprintf("'%s', %d, %d, %d", $this->subject->getType(), $this->subject->getIdentity(), $this->show_bottom_post, $comment->getIdentity())
?>)">
                    <?php echo $this->translate('like') ?>
                  </a>
                <?php else: ?>
                  <a href="javascript:void(0)" onclick="en4.seaocore.comments.unlike(<?php echo
sprintf("'%s', %d, %d, %d ", $this->subject->getType(), $this->subject->getIdentity(), $this->show_bottom_post, $comment->getIdentity())
?>)">
                    <?php echo $this->translate('unlike') ?>
                  </a>
                <?php endif ?>
              <?php endif ?>
              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                -
                <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>"
class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                  <?php echo $this->translate(array('%s likes this', '%s like this',
$comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                </a>
              <?php endif ?>
            </div>
          </div>
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'onclick' => 'en4.seaocore.comments.loadComments("'.$this->subject->getType().'",
"'.$this->subject->getIdentity().'", "'.($this->page + 1).'","'. $this->show_bottom_post .'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>
        
      <?php if( isset($this->form) && $this->show_bottom_post ) : ?>
      <li id='comment-form-open-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>' onclick="(this).css('display', 'none'); scriptJquery('#comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').css('display',  '');
        if(document.getElementById(('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').body) {document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').find('#body').focus();}"> 

        <div/></div>
        <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Post a comment...') ?></div>
      </li>
      <li id='comment-form-loading-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>' style="display: none;"> 
        <div class="comments_author_photo">
            <?php echo $this->htmlLink($this->viewer()->getHref(),
              $this->itemPhoto($this->viewer(), 'thumb.icon', $this->viewer()->getTitle())
            ) ?>
          </div>
          <div class="comments_info">
            <span class='comments_author'><?php echo $this->htmlLink($this->viewer()->getHref(), $this->viewer()->getTitle());
?></span>
            <span class="comments_body">
              
            </span> 
            <div class="comments_date"><img src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Seaocore/externals/images/core/loading.gif" /></div>
          </div>
      </li>
      <?php endif ?> 
        
    <?php endif; ?>

  </ul>
  <script type="text/javascript">
    en4.core.runonce.add(function() {
      scriptJquery(document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity();?>').body).each(function () {
        this.setAttribute("style", "height:" + (this.scrollHeight) + "px;overflow-y:hidden;");
      }).on("input", function () {
        this.style.height = 0;
        this.style.height = (this.scrollHeight) + "px";
      });
      en4.seaocore.comments.attachCreateComment(document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>'),'<?php echo
      $this->subject->getType()?>',<?php echo $this->subject->getIdentity() ?> , '<?php echo $this->show_bottom_post ?>');
          <?php if($this->show_bottom_post): ?>
          scriptJquery('#comment-form_<?php echo
      $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').find('#submit').css('display', 'none');
    
    <?php if($this->submit_post):?>
      if(scriptJquery('#comment-form-open-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').length){ document.getElementById('comment-form-open-li_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').style.display = 'none'; } document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php

echo $this->subject->getIdentity() ?>').style.display = '';document.getElementById('comment-form_<?php echo $this->subject->getType()?>_<?php echo $this->subject->getIdentity() ?>').find('#body').focus()

      <?php endif; ?>
    <?php endif; ?>
    });
  </script>
  <?php if( isset($this->form) ) echo $this->form->setAttribs(array('id' =>
'comment-form_'.$this->subject->getType()."_".$this->subject->getIdentity(), 'style' =>
'display:none;', 'class' => 'seaocore_form_comment'))->render() ?>
<?php if( !$this->page ): ?>
</div>
    <?php endif; ?>
