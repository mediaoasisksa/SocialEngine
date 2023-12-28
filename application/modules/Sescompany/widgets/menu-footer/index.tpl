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
<?php 
$aboutsusheading = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.footabtheading', 'About Us');
$aboutusdescription = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.footeraboutusdes', 'Lorem Ipsum Is Simply Dummy Text Of The Printing And Typesetting Industry.');

?>
<div class="sescompany_footer_main sesbasic_bxs">
  <div class="sescompany_footer_about_us sesbasic_clearfix sescompany_footer_blogs">
    <?php if($aboutsusheading) { ?>
    <div class="sescompany_footer_about_tittle sesbasic_footer_tittle">
      <h3><?php echo $this->translate($aboutsusheading); ?></h3>
    </div>
    <?php } ?>
    <?php if($aboutusdescription) { ?>
      <div class="sescompany_footer_about_desc">
        <p><?php echo $this->translate($aboutusdescription); ?></p>
      </div>
    <?php } ?>
  </div>
  
  <div class="sescompany_footer_links sesbasic_clearfix">
    <h3><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.resourceheading', 'RESOURCES')); ?></h3>
    <?php foreach( $this->navigation as $item ): 
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href'
      )));
      ?>
      <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
    <?php endforeach; ?>
  </div>
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.quicklinksenable', '1')) { ?>
    <div class="sescompany_footer_links sesbasic_clearfix">
    <h3><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.quicklinkheading', 'Quick Links')); ?></h3>
      <?php foreach( $this->extramenus as $link ): ?>
        <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
          <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
          <span><?php echo $this->translate($link->getlabel()) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php } ?>

  <div class="sescompany_footer_social_blog sescompany_footer_blogs">
  
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showsocialmedia', 1) && count($this->socialnavigation) > 0) { ?>
      <div class="sescompany_footer_social_tittle">
        <h3><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.socialmediaheading', 'Social')); ?></h3>
      </div>
      <div class="sescompany_footer_social_links sesbasic_clearfix">
        <?php foreach( $this->socialnavigation as $link ): ?>
          <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
            <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
            <i class="fa <?php echo $link->get('icon') ? $link->get('icon') : 'fa-star' ?>"></i>
          </a>
        <?php endforeach; ?>
      </div>
      <br />
    <?php } ?>
    
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showlanguage', 1) && 1 !== count($this->languageNameList) ): ?>
      <div class="sescompany_footer_lang sesbasic_clearfix">
        <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
          <?php $selectedLanguage = $this->translate()->getLocale() ?>
          <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
          <?php echo $this->formHidden('return', $this->url()) ?>
        </form>
      </div>
    <?php endif; ?>
  </div>
   
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.showcopyrights', 1)) { ?>
    <div class="sescompany_footer_bottom">
     <div class="sescompany_footer_copy sesbasic_bxs">
        <?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
      </div>
    </div>
  <?php } ?>
</div>

<a href="javascript:;" id="SESscrollToElement" onclick="SESscrollTopAnimated(1000)" class="scroll-to-top"><i class="fa fa-angle-double-up"></i></a>
<script>
	window.addEventListener("scroll", function(event) {
    var top = this.scrollY;
		if (top > 100) {
			$('SESscrollToElement').fade('in');
		} else {
			$('SESscrollToElement').fade('out');
    }
	}, false);
	var stepTime = 20;
	var docBody = document.body;
	var focElem = document.documentElement;
	
	var scrollAnimationStep = function (initPos, stepAmount) {
			var newPos = initPos - stepAmount > 0 ? initPos - stepAmount : 0;
	
			docBody.scrollTop = focElem.scrollTop = newPos;
	
			newPos && setTimeout(function () {
					scrollAnimationStep(newPos, stepAmount);
			}, stepTime);
	}
	var SESscrollTopAnimated = function (speed) {
			var topOffset = docBody.scrollTop || focElem.scrollTop;
			var stepAmount = topOffset;
	
			speed && (stepAmount = (topOffset * stepTime)/speed);
	
			scrollAnimationStep(topOffset, stepAmount);
	};
</script>
<style>
    .layout_page_footer {
        background-color: #cfd2d8;
    }
    
    .sescompany_footer_main h3 {
        color: #1e3869;
    }
    
    .sescompany_footer_links a {
        color: #fff;
    }
</style>
