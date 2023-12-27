<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

?>
<?php if( $this->menuFromTheme ): ?>
  <ul class="navigation">
    <?php foreach( $this->navigation as $link ): ?>
      <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
        <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
          <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
          <i class="<?php echo $link->get('icon') ? $link->get('icon') : 'fa fa-star' ?>"></i>
          <span><?php echo $this->translate($link->getlabel()) ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <?php $countMenu = 0; ?>
  <nav class="navbar navbar-expand-lg">
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon">
				 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 1792 1792" style="enable-background:new 0 0 1792 1792;" xml:space="preserve">
<title>fiction</title>
<path d="M1673.9,1363.2L1673.9,1363.2c0,52.3-42.4,94.3-94.3,94.3H212.7c-52.3,0-94.3-42.4-94.3-94.3l0,0  c0-52.3,42.4-94.3,94.3-94.3h1366.8C1631.5,1268.5,1673.9,1310.9,1673.9,1363.2z"/>
<path d="M1673.9,895.6L1673.9,895.6c0,52.3-42.4,94.3-94.3,94.3H213c-52.3,0-94.3-42.4-94.3-94.3l0,0c0-52.3,42.4-94.3,94.3-94.3  h1366.6C1631.5,800.8,1673.9,843.2,1673.9,895.6z"/>
<path d="M1673.9,427.9L1673.9,427.9c0,52.3-42.4,94.3-94.3,94.3H212.7c-52.3,0-94.3-42.4-94.3-94.3l0,0c0-52.3,42.4-94.3,94.3-94.3  h1366.8C1631.5,333.2,1673.9,375.6,1673.9,427.9z"/>
         </svg>
       </span>
    </button>
   <div class="main_menu_navigation offcanvas offcanvas-end" id="offcanvasNavbar">
     <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa fa-times"></i></button>
     <ul class="navigation">
      <?php foreach( $this->navigation as $link ): ?>
        <?php if( $countMenu < $this->menuCount ): ?>
          <?php 
            $explodedString = explode(' ', $link->class);
            $menuName = end($explodedString); 
            $moduleName = str_replace('core_main_', '', $menuName);
            if(strpos($moduleName, 'custom_') !== 0){
              $moduleName = $moduleName.'_main';
            }
          ?>
         <?php $subMenus = Engine_Api::_()->getApi('menus', 'core')->getNavigation($moduleName); 
            $menuSubArray = $subMenus->toArray();
         ?>
          <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
            <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
              <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
              <i class="<?php echo $link->get('icon') ? $link->get('icon') : 'fa fa-star' ?>"></i>
              <span><?php echo $this->translate($link->getlabel()) ?></span>
               <?php if(engine_count($menuSubArray) > 0 && $this->submenu): ?>
                  <i class="fa fa-angle-down open_submenu"></i>
               <?php endif; ?>
            </a>
          <?php if(engine_count($menuSubArray) > 0 && $this->submenu): ?>
            <ul class="main_menu_submenu">
              <?php 
              $counter = 0; 
              foreach( $subMenus as $subMenu): 
             	$active = isset($menuSubArray[$counter]['active']) ? $menuSubArray[$counter]['active'] : 0;
              ?>
                <li class="sesbasic_clearfix <?php echo ($active) ? 'selected_sub_main_menu' : '' ?>">
                  <a href="<?php echo $subMenu->getHref(); ?>" <?php if( $subMenu->get('target') ): ?> target='<?php echo $subMenu->get('target') ?>' <?php endif; ?> class="<?php echo $subMenu->getClass(); ?>">
                    <i class="<?php echo $subMenu->get('icon') ? $subMenu->get('icon') : 'fa fa-star' ?>"></i><span><?php echo $this->translate($subMenu->getLabel()); ?></span>
                  </a>
                </li>
              <?php 
              $counter++;
              endforeach; ?>
            </ul>
          <?php endif; ?>
          </li>
        <?php else:?>
          <?php break;?>
        <?php endif;?>
        <?php $countMenu++;?>
      <?php endforeach; ?>
      <?php if (engine_count($this->navigation) > $this->menuCount):?>
        <?php $countMenu = 0; ?>
        <li class="more_tab">
          <a href="javascript:void(0);">
            <span><?php echo $this->translate("More") ?></span>
            <i class="fa fa-angle-down open_submenu"></i>
          </a>
          <ul class="navigation_submenu">
            <?php foreach( $this->navigation as  $link ): ?>
              <?php if ($countMenu >= $this->menuCount): ?>

                <?php 
                  $explodedString = explode(' ', $link->class);
                  $menuName = end($explodedString); 
                  $moduleName = str_replace('core_main_', '', $menuName);
                  if(strpos($moduleName, 'custom_') !== 0){
                    $moduleName = $moduleName.'_main';
                  }
                ?>
                <?php 
                  $subMenus = Engine_Api::_()->getApi('menus', 'core')->getNavigation($moduleName);
                  $menuSubArray = $subMenus->toArray();
                ?>

                <li class="<?php echo $link->get('active') ? 'active' : '' ?>">
                  <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>"
                    <?php if( $link->get('target') ): ?> target='<?php echo $link->get('target') ?>' <?php endif; ?> >
                    <i class="<?php echo $link->get('icon') ? $link->get('icon') : 'fa fa-star' ?>"></i>
                    <span><?php echo $this->translate($link->getlabel()) ?>
                      <?php if(engine_count($menuSubArray) > 0 && $this->submenu): ?>
                          <i class="fa fa-angle-down open_submenu"></i>
                       <?php endif; ?>
                    </span>
                  </a>

                  <?php if(engine_count($menuSubArray) > 0 && $this->submenu): ?>
                    <ul class="main_menu_submenu">
                      <?php 
                      $counter = 0; 
                      foreach( $subMenus as $subMenu): 
                      $active = isset($menuSubArray[$counter]['active']) ? $menuSubArray[$counter]['active'] : 0;
                      ?>
                        <li class="sesbasic_clearfix <?php echo ($active) ? 'selected_sub_main_menu' : '' ?>">
                            <a href="<?php echo $subMenu->getHref(); ?>" <?php if( $subMenu->get('target') ): ?> target='<?php echo $subMenu->get('target') ?>' <?php endif; ?>  class="<?php echo $subMenu->getClass(); ?>">
                            <i class="<?php echo $subMenu->get('icon') ? $subMenu->get('icon') : 'fa fa-star' ?>"></i><span><?php echo $this->translate($subMenu->getLabel()); ?></span>
                          </a>
                        </li>
                      <?php 
                      $counter++;
                      endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </li>
              <?php endif;?>
              <?php $countMenu++;?>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endif;?>
    </ul>
  </div>
  </nav>
  <script type="text/javascript">

    scriptJquery(document).on('click','.open_submenu',function(e){
      if(scriptJquery(this).parent().parent().find('ul').children().length == 0)
        return true;
      e.preventDefault();
      if(scriptJquery(this).parent().hasClass('has_submenu')){
        scriptJquery('.has_submenu').parent().find('ul').slideToggle('slow');
        scriptJquery(this).parent().removeClass('has_submenu');
      } else {
        scriptJquery('.has_submenu').parent().find('ul').slideToggle('slow');
        scriptJquery(this).parent().parent().find('ul').slideToggle('slow');
        scriptJquery('.has_submenu').removeClass('has_submenu');
        scriptJquery(this).parent().addClass('has_submenu');
      }
      return false;
    });

    scriptJquery(document).ready(function(){
      var selectedMenu = scriptJquery('.main_menu_navigation').find(".selected_sub_main_menu");
      if(selectedMenu.length){
        var parentMenu = selectedMenu.closest(".main_menu_submenu").closest("li");
        if(parentMenu.length && !parentMenu.hasClass("active")){
          parentMenu.addClass("active");
        }
      }
      selectedMenu = scriptJquery('.main_menu_navigation').find(".more_tab > ul > li.active");
      if(selectedMenu.length){
        selectedMenu.closest(".more_tab").addClass("active");
      }
    });
    if(typeof en4 != "undefined"){
      en4.core.layout.setLeftPannelMenu('<?php echo $this->menuType; ?>');
    }
  </script>
<?php endif; ?>
