<?php
/**
 * 
 */
class Sitebooking_View_Helper_Sharelinkshelper extends Zend_View_Helper_Abstract {

  public function sharelinkshelper($subject, $params = array(), $showText = false) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
    $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
    $urlShare = $this->view->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true);
    $baseUrl = $view->baseUrl();
    $resource_id = $subject->getIdentity();
    $resource_type = $subject->getType();
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    ?>
    <?php if($params === "facebook") : ?>
      <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" title="<?php echo $view->translate("Facebook"); ?>"><i class='fa fa-facebook'></i></a>
    <?php endif; ?>
    
    <?php if($params === "twitter") : ?>
      <a href="https://twitter.com/share?text=<?php echo $subject->getTitle(); ?>" target="_blank" title="Twitter"><i class='fa fa-twitter'></i></a>
    <?php endif; ?>

    <?php if($params === "linkedin") : ?>
      <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $object_link; ?>" target="_blank" title="Linkedin"><i class='fa fa-linkedin'></i></a>
    <?php endif; ?>
    <?php if ($params === "pinterest") : ?>
       <a href="http://pinterest.com/pinthis?url=<?php echo $urlencode; ?>&t=<?php echo $subject->getTitle(); ?>" target="_blank" title="Pinterest"><i class="fa fa-pinterest"></i></a> 
    <?php endif; ?>


  <!-- FAVOURITE -->
  <?php if($params === "favourite") : ?>
    <?php if ($viewer_id) : ?>
      <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id); ?>

      <?php $unfavourites = $resource_type . '_unfavourites_' . $resource_id ?>
      <?php $favourites = $resource_type . '_most_favourites_' . $resource_id ?>
      <?php $fav = $resource_type . '_favourite_' . $resource_id; ?>


      <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $unfavourites; ?>" style ='display:<?php echo $hasFavourite ? "inline-block" : "none" ?>' class="seaocore_icon_favourite <?php echo $unfavourites; ?>" title="<?php echo $view->translate("Unfavourite"); ?>">
      <?php if ($showText) : ?>
      <?php echo $view->translate("Unfavourite"); ?>
      <?php endif; ?>
      </a>

      <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $favourites; ?>" style ='display:<?php echo empty($hasFavourite) ? "inline-block" : "none" ?>' class="seaocore_icon_unfavourite <?php echo $favourites; ?>" title="<?php echo $view->translate("Favourite"); ?>"><?php if ($showText) : ?>
      <?php echo $view->translate("Favourite"); ?>
      <?php endif; ?>

      </a>

      <input type ="hidden" id = "<?php echo $fav ?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] : 0; ?>' />
    <?php endif; ?>
  <?php endif; ?>

    <?php
  }
}

?>

