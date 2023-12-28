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

if($this->viewer()->getIdentity()) {
    $href= $this->viewer()->getHref();
} else {
  $href= $this->url(array('return_url' => null), 'user_login');
}


?>
<?php if($this->headerview != 3) { ?>
  <div class="mobile_nav_searhbox"><a  href="javascript:void(0);" class="top_header-search_box" id="mobile_header_searchbox_toggle"><i class="fa fa-search"></i></a></div>
  <a class="company_mobile_nav_toggle" id="company_mobile_nav_toggle" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
  <div class="sescompany_mobile_nav sescompany_main_menu" id="company_mobile_nav">
    <ul class="navigation">
      <?php foreach( $this->navigation as $navigationMenu ): 
            $class = explode(' ', $navigationMenu->class); 
      ?>
      
        <?php 
          $mainMenuIcon = Engine_Api::_()->getApi('menus', 'sescompany')->getIconsMenu(end($class)); 
          $mainMenuID = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuId(end($class));
          $menus = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuObject($mainMenuID);
        ?>
        
         <li class="<?php echo $navigationMenu->get('active') ? 'active' : '' ?>">
          <a href='<?php echo $navigationMenu->getHref() ?>' class="<?php echo $navigationMenu->getClass() ? ' ' . $navigationMenu->getClass() : ''  ?>" <?php if( $navigationMenu->get('target') ): ?> target='<?php echo $navigationMenu->get('target') ?>' <?php endif; ?> >
            <?php if(!empty($mainMenuIcon) && empty($menus->icon_type)):?>
              <i style="background-image:url(<?php echo Engine_Api::_()->storage()->get($mainMenuIcon, '')->getPhotoUrl(); ?>);"></i>
            <?php elseif($menus->icon_type && !empty($menus->font_icon)): ?>
              <i class="fa <?php echo $menus->font_icon ?>"></i>
            <?php endif;?>
            <span><?php echo $this->translate($navigationMenu->label); ?></span>
          </a>
        </li>
       
      <?php endforeach; ?>
    </ul>
  </div>
  <script>
    $('company_mobile_nav_toggle').addEvent('click', function(event){
      event.stop();
      if($('company_mobile_nav').hasClass('show-nav'))
        $('company_mobile_nav').removeClass('show-nav');
      else
        $('company_mobile_nav').addClass('show-nav');
      return false;
    });
  </script>
<?php } ?>

<div class="sescompany_main_menu">
  <ul class="navigation">
    <?php $countMenu = 0; ?>
    <?php foreach( $this->navigation as $navigationMenu ): $class = explode(' ', $navigationMenu->class);  ?>
   
      <?php $mainMenuIcon = Engine_Api::_()->getApi('menus', 'sescompany')->getIconsMenu(end($class)); ?>
      
      <?php $mainMenuID = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuId(end($class));
            $menus = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuObject($mainMenuID);
     
      ?>
      
      <?php if( $countMenu < $this->max ): ?>
        <?php if(end($class) == 'custom_356'):?>
        <li class="<?php echo $navigationMenu->get('active') ? 'active' : '' ?>">
          <a href='<?php echo $href ?>' class="<?php echo $navigationMenu->getClass() ? ' ' . $navigationMenu->getClass() : ''  ?>" <?php if( $navigationMenu->get('target') ): ?> target='<?php echo $navigationMenu->get('target') ?>' <?php endif; ?> >
            <?php if(!empty($mainMenuIcon) && empty($menus->icon_type)):?>
              <i style="background-image:url(<?php echo Engine_Api::_()->storage()->get($mainMenuIcon, '')->getPhotoUrl(); ?>);"></i>
            <?php elseif($menus->icon_type && !empty($menus->font_icon)): ?>
              <i class="fa <?php echo $menus->font_icon ?>"></i>
            <?php endif;?>
            <!--<i class="fa <?php //echo $navigationMenu->get('icon') ? $navigationMenu->get('icon') : 'fa-star' ?>"></i>-->
            <span class="fa <?php echo end($class);?>"><?php echo $this->translate($navigationMenu->getlabel()) ?></span>
          </a>
        </li>
        <?php else:?>
        <li class="<?php echo $navigationMenu->get('active') ? 'active' : '' ?>">
          <a href='<?php echo $navigationMenu->getHref() ?>' class="<?php echo $navigationMenu->getClass() ? ' ' . $navigationMenu->getClass() : ''  ?>" <?php if( $navigationMenu->get('target') ): ?> target='<?php echo $navigationMenu->get('target') ?>' <?php endif; ?> >
            <?php if(!empty($mainMenuIcon) && empty($menus->icon_type)):?>
              <i style="background-image:url(<?php echo Engine_Api::_()->storage()->get($mainMenuIcon, '')->getPhotoUrl(); ?>);"></i>
            <?php elseif($menus->icon_type && !empty($menus->font_icon)): ?>
              <i class="fa <?php echo $menus->font_icon ?>"></i>
            <?php endif;?>
            <!--<i class="fa <?php //echo $navigationMenu->get('icon') ? $navigationMenu->get('icon') : 'fa-star' ?>"></i>-->
            <span class="fa <?php echo end($class);?>"><?php echo $this->translate($navigationMenu->getlabel()) ?></span>
          </a>
        </li>
        <?php endif;?>
      <?php else:?>
        <?php break;?>
      <?php endif;?>
      <?php $countMenu++;?>
    <?php endforeach; ?>
    <?php if (count($this->navigation) > $this->max): ?>
      <?php $countMenu = 0; ?>  
      <li class="more_tab">
        <a class="menu_core_main core_menu_more" href="javascript:void(0);"><span><?php echo $this->translate($this->moreText); ?> <i class="fa fa-angle-down"></i></span></a>
        <ul class="main_menu_submenu">
          <?php foreach( $this->navigation as  $navigationMenu ): $class = explode(' ', $navigationMenu->class); ?>
            <?php 
              $mainMenuIcon = Engine_Api::_()->getApi('menus', 'sescompany')->getIconsMenu(end($class)); 
              $mainMenuID = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuId(end($class));
              $menus = Engine_Api::_()->getApi('menus', 'sescompany')->getMenuObject($mainMenuID);
            ?>
            <?php if ($countMenu >= $this->max): ?>
              <li class="<?php echo $navigationMenu->get('active') ? 'active' : '' ?>">
                <a href='<?php echo $navigationMenu->getHref() ?>' class="<?php echo $navigationMenu->getClass() ? ' ' . $navigationMenu->getClass() : ''  ?>" <?php if( $navigationMenu->get('target') ): ?> target='<?php echo $navigationMenu->get('target') ?>' <?php endif; ?> >
                  <?php if(!empty($mainMenuIcon) && empty($menus->icon_type)):?>
                    <i style="background-image:url(<?php echo Engine_Api::_()->storage()->get($mainMenuIcon, '')->getPhotoUrl(); ?>);"></i>
                  <?php elseif($menus->icon_type && !empty($menus->font_icon)): ?>
                    <i class="fa <?php echo $menus->font_icon ?>"></i>
                  <?php endif;?>
                  <!--<i class="fa <?php //echo $navigationMenu->get('icon') ? $navigationMenu->get('icon') : 'fa-star' ?>"></i>-->
                  <span class="test2"><?php echo $this->translate($navigationMenu->label); ?></span>
                </a>
              </li>
            <?php endif;?>
            <?php $countMenu++;?>
          <?php endforeach; ?>
        </ul>
      </li>
    <?php endif;?>
  </ul>
  
  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany_enable_footer', 1) && $this->headerview == 3) { ?>
      <div class="sescompany_main_menu_footer">
    <p class="copyright_right"><?php echo $this->translate('Copyright &copy;%s', date('Y')) ?></p>
    <p class="liks_right">
    <?php foreach( $this->footernavigation as $item ):
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
        'reset_params', 'route', 'module', 'controller', 'action', 'type',
        'visible', 'label', 'href'
      )));
      ?>
      <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
    <?php endforeach; ?></p>

    <?php if( 1 !== count($this->languageNameList) ): ?>
        <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
          <?php $selectedLanguage = $this->translate()->getLocale() ?>
          <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
          <?php echo $this->formHidden('return', $this->url()) ?>
        </form>
    <?php endif; ?>
    </div>
  <?php } ?>
</div>
<?php $header_fixed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sescompany.header.fixed', 1); ?>
<?php if($header_fixed == '1'): ?>
	<script type="text/javascript">
	sesJqueryObject(window).scroll(function() {    
			var scroll = sesJqueryObject(window).scrollTop();
	
			if (scroll >= 100) {
					sesJqueryObject(".layout_page_header").addClass("header_sticky");
			} else {
					sesJqueryObject(".layout_page_header").removeClass("header_sticky");
			}
	});
	</script>
<?php endif; ?>