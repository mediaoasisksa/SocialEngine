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
$settings = Engine_Api::_()->getApi('settings', 'core'); 
?>

<?php if($this->headerview == '1'): ?>

<div class="header_right clearfix">
	<div class="header_right_top clearfix">
 
    <?php if($this->show_socialshare): ?>
      <div class="header_social_icons">
        <ul class="navigation">
          <?php foreach( $this->social_navigation as $link ): ?>
            <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
              <a href='<?php echo $link->getHref() ?>' target="_blank" class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
                <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
                <i class="fa <?php echo $link->get('icon') ? $link->get('icon') : 'fa-star' ?>"></i>
                <span><?php echo $this->translate($link->getlabel()) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if($this->show_mini):?>
   	<div class="header_minimenu">
    	<?php echo $this->content()->renderWidget("sescompany.menu-mini"); ?>
    </div>
    <?php endif; ?>

  </div>
  <div class="header_middle">
  <?php if($this->show_logo):?>
    <div class="header_logo">
      <?php echo $this->content()->renderWidget('sescompany.menu-logo'); ?>
    </div>
  <?php endif; ?>
      <?php if($this->show_search):?>
      <div class="minimenu_search_box" id="minimenu_search_box"><?php echo $this->content()->renderWidget("sescompany.search"); ?></div>
    <?php endif; ?>
</div>
  <?php if($this->show_menu):?>
    <div class="main_menu_bar">
      <?php echo $this->content()->renderWidget("sescompany.menu-main"); ?>
    </div>
	<?php endif; ?>
</div>

<script type="text/javascript">
	( function( window ) {

'use strict';

// class helper functions from bonzo https://github.com/ded/bonzo

function classReg( className ) {
  return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
}

// classList support for class management
// altho to be fair, the api sucks because it won't accept multiple classes at once
var hasClass, addClass, removeClass;

if ( 'classList' in document.documentElement ) {
  hasClass = function( elem, c ) {
    return elem.classList.contains( c );
  };
  addClass = function( elem, c ) {
    elem.classList.add( c );
  };
  removeClass = function( elem, c ) {
    elem.classList.remove( c );
  };
}
else {
  hasClass = function( elem, c ) {
    return classReg( c ).test( elem.className );
  };
  addClass = function( elem, c ) {
    if ( !hasClass( elem, c ) ) {
      elem.className = elem.className + ' ' + c;
    }
  };
  removeClass = function( elem, c ) {
    elem.className = elem.className.replace( classReg( c ), ' ' );
  };
}

function toggleClass( elem, c ) {
  var fn = hasClass( elem, c ) ? removeClass : addClass;
  fn( elem, c );
}

var classie = {
  // full names
  hasClass: hasClass,
  addClass: addClass,
  removeClass: removeClass,
  toggleClass: toggleClass,
  // short names
  has: hasClass,
  add: addClass,
  remove: removeClass,
  toggle: toggleClass
};

// transport
if ( typeof define === 'function' && define.amd ) {
  // AMD
  define( classie );
} else {
  // browser global
  window.classie = classie;
}

})( window );
</script>
<script>
    function init() {
        window.addEventListener('scroll', function(e){
            var distanceY = window.pageYOffset || document.documentElement.scrollTop,
                shrinkOn = 300,
                header = document.querySelector(".layout_page_header");
            if (distanceY > shrinkOn) {
                classie.add(header,"smaller");
            } else {
                if (classie.has(header,"smaller")) {
                    classie.remove(header,"smaller");
                }
            }
        });
    }
    window.onload = init();
</script>

<?php elseif($this->headerview == '2'): ?>
<?php if($this->show_menu):?>
<div class="top-header">
      <div class="header_left">
  <?php if($this->show_logo):?>
    <div class="header_logo">
      <?php echo $this->content()->renderWidget('sescompany.menu-logo'); ?>
    </div>
  <?php endif; ?>
</div>

<div class="header_right clearfix">
	<div class="header_right_top clearfix">
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.heshowextralinks', 1)) { ?>
      <div class="header_contact_info">
        <ul>  
          <?php if($settings->getSetting('sescompany.heshowextraphoneicon', 'fa-phone') && $settings->getSetting('sescompany.heshowextraphonenumber', '123456789')) { ?> 
            <li><a href="tel:<?php echo $settings->getSetting('sescompany.heshowextraphonenumber', '123456789') ?>"><i class="fa <?php echo $settings->getSetting('sescompany.heshowextraphoneicon', 'fa-phone') ?>"></i><?php echo $settings->getSetting('sescompany.heshowextraphonenumber', '123456789'); ?></a></li>
          <?php } ?>
          <?php if($settings->getSetting('sescompany.heshowextraemailicon', 'fa-envelope-o') && $settings->getSetting('sescompany.heshowextraemailnumber', 'info@business.com')) { ?> 
            <li><a href="mailto:<?php echo $settings->getSetting('sescompany.heshowextraemailnumber', 'info@business.com'); ?>"><i class="fa <?php echo $settings->getSetting('sescompany.heshowextraemailicon', 'fa-envelope-o'); ?>"></i><?php echo $settings->getSetting('sescompany.heshowextraemailnumber', 'info@business.com'); ?></a></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    <?php if($this->show_socialshare): ?>
      <div class="header_social_icons">
        <ul class="navigation">
          <?php foreach( $this->social_navigation as $link ): ?>
            <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
              <a href='<?php echo $link->getHref() ?>' target="_blank" class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
                <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
                <i class="fa <?php echo $link->get('icon') ? $link->get('icon') : 'fa-star' ?>"></i>
                <span><?php echo $this->translate($link->getlabel()) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <?php if($this->show_mini):?>

   	<div class="header_minimenu">
    	<?php echo $this->content()->renderWidget("sescompany.menu-mini"); ?>
    </div>
    <?php endif; ?>
    <?php if($this->show_search):?>
      <div class="minimenu_search_box" id="minimenu_search_box"><?php echo $this->content()->renderWidget("sescompany.search"); ?></div>
    <?php endif; ?>
  </div>
  </div>
    <div class="main_menu_bar">
      <?php echo $this->content()->renderWidget("sescompany.menu-main"); ?>
    </div>
	<?php endif; ?>
</div>

<?php elseif($this->headerview == '3'): ?>

  <div class="header_left">
    <?php if($this->show_logo):?>
      <div class="header_logo">
        <?php echo $this->content()->renderWidget('sescompany.menu-logo'); ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="header_right clearfix">
    <div class="header_right_top clearfix">
      <?php if($this->show_socialshare): ?>
        <div class="header_social_icons">
          <ul class="navigation">
            <?php foreach( $this->social_navigation as $link ): ?>
              <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
                <a href='<?php echo $link->getHref() ?>' target="_blank" class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
                  <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
                  <i class="fa <?php echo $link->get('icon') ? $link->get('icon') : 'fa-star' ?>"></i>
                  <span><?php echo $this->translate($link->getlabel()) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <?php if($this->show_mini):?>
      <div class="header_minimenu">
        <?php echo $this->content()->renderWidget("sescompany.menu-mini"); ?>
      </div>
      <?php endif; ?>
      <?php if($this->show_search):?>
        <div class="minimenu_search_box" id="minimenu_search_box"><?php echo $this->content()->renderWidget("sescompany.search"); ?></div>
      <?php endif; ?>
    </div>
  </div>
  <div class="verical_main_menu_bar clearfix">
    <?php if($this->show_menu):?>
      <div class="main_menu_toggle_panel panel-toggle"></div>
      <div class="main_menu_bar main_menu_navigation mCustomScrollbar" id="main_menu_bar">
        <?php echo $this->content()->renderWidget("sescompany.menu-main"); ?>
      </div>
      
    <?php endif; ?>
  </div>
  <script type="text/javascript">
		function ResizeForScroll(){		
		  var navheight = sesJqueryObject(".verical_main_menu_bar").height();
		 	var toggleheight = sesJqueryObject(".main_menu_toggle_panel").height();
				if($("main_menu_bar")) {
					$("main_menu_bar").setStyle("height",(navheight - toggleheight)+"px");
			}
		};
		window.addEvent('load',function(){
			ResizeForScroll();
		});
		sesJqueryObject(window).resize(function(){
			ResizeForScroll();
		});
	 
	  en4.core.runonce.add(function() {
    
      <?php if($this->headerview == 3): ?>
        $(document).getElement('body').addClass('sescompany_footer_none')
      <?php endif; ?>
      var pannelElement = $(document).getElement('body').addClass('global_left_panel panel-collapsed');
      var button = $(document).getElement('.verical_main_menu_bar .panel-toggle');
      var scrollBar;
      var headerButton = new Element('div', {
        'class': 'main_menu_toggle_panel fa header-panel-toggle'
      }).inject(pannelElement.getElement('.layout_page_header .layout_main .generic_layout_container'), 'top');
      var navigationElement = pannelElement.getElement('.verical_main_menu_bar .main_menu_navigation');

      var setContent = function () {
        pannelElement.addClass('global_left_panel');
        navigationElement.setStyle('height', window.getSize().y - button.getCoordinates().height + 'px');
        navigationElement.removeClass('horizontal_core_main_menu');
      }
      
      pannelElement.getElements('.main_menu_toggle_panel').addEvent('click', function () {
        pannelElement.toggleClass('panel-collapsed').toggleClass('panel-open');
        scrollBar.updateScrollBars();
      });

//      window.addEvent('resize', setContent);
//      setContent();
//      navigationElement.scrollbars({
//        scrollBarSize: 10,
//        fade: true,
//        barOverContent: true
//      });
//      scrollBar = navigationElement.retrieve('scrollbars');
//      scrollBar.element.getElement('.scrollbar-content-wrapper').setStyle('float', 'none');
//      scrollBar.updateScrollBars();

      //Tip Code when Mouse Over on Menu icons
      var menuTipElement = new Element('div', {
        'class': 'menu_core_main_tip'
      }).inject($(document.body));
      var hideMenuTip = function () {
        menuTipElement.setStyle('display', 'none')
      };

      navigationElement.getElements('li').addEvent('mouseover', function () {
        if (!this.getParent('.panel-collapsed') || this.getParent('.horizontal_core_main_menu')) {
          hideMenuTip();
          return;
        }
        if(this.getElement('.menu_core_main')) {
          menuTipElement.set('html', this.getElement('.menu_core_main').get('html')).setStyles({
            'top': this.getCoordinates().top + 8,
            'display': 'block'
          });
        }
      }).addEvent('mouseout', function () {
        hideMenuTip();
      });
      //Tip work

      scrollBar.element.getElement('.scrollbar-content').addEvent('scroll', function () {
        hideMenuTip();
      });
    });
  </script>
<?php endif; ?>

<script type="text/javascript">
  sesJqueryObject(document).on('click','#minimenu_header_searchbox_toggle',function(){
    if(sesJqueryObject (this).hasClass('active')){
     sesJqueryObject (this).removeClass('active');
     sesJqueryObject ('.minimenu_search_box').removeClass('open_search');
    }else{
     sesJqueryObject (this).addClass('active');
     sesJqueryObject ('.minimenu_search_box').addClass('open_search');
    }
 });
   sesJqueryObject(document).on('click','#mobile_header_searchbox_toggle',function(){
    if(sesJqueryObject (this).hasClass('active')){
     sesJqueryObject (this).removeClass('active');
     sesJqueryObject ('.minimenu_search_box').removeClass('open_search');
    }else{
     sesJqueryObject (this).addClass('active');
     sesJqueryObject ('.minimenu_search_box').addClass('open_search');
    }
 });
</script>

