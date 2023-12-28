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
<ul class="navigation">
  <?php foreach( $this->navigation as $link ): ?>
    <?php
      $explodedString = explode(' ', $link->class);
      $menuName = end($explodedString); 
      $subMenus = Engine_Api::_()->getApi('menus', 'core')->getNavigation($menuName); 
      $menuSubArray = $subMenus->toArray();
    ?>
    <li class="<?php echo $link->get('active') ? 'active' : '' ?>" <?php if($link->getlabel() == "Plugins" && count($menuSubArray) == 0): ?> style="display:none;" <?php endif; ?>>
      <a href='<?php echo $link->getHref() ?>' class="<?php echo $link->getClass() ? ' ' . $link->getClass() : ''  ?>">
        <span><?php echo $this->translate($link->getlabel()) ?></span>
      </a>
      <?php if(engine_count($menuSubArray) > 0): ?>
        <ul class="main_menu_submenu">
          <?php foreach( $subMenus as $subMenu): ?>
            <?php
              $subMenuString = explode(' ', $subMenu->class);
              $subMenName = end($subMenuString);
            ?>
            <li>
              <a href="<?php echo $subMenu->getHref(); ?>" class="<?php if ($subMenu->getHref() == $_SERVER['REQUEST_URI']): ?> active <?php endif; ?> <?php echo $subMenu->getClass(); ?>"><span><?php echo $this->translate($subMenu->getLabel()); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
