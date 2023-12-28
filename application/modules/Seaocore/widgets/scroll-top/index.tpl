<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    seaocore
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>
<style type="text/css">
   #scrollToTop {
     display: none;
   }
   .scrollToTopBtn:before {
      content: "\f34d";
      font-family: 'Font Awesome 5 Free';
      font-weight: 900;
   }

</style>
  <a id="scrollToTop" href="#" class="seaocore_up_button" title="<?php echo $this->translate("%s", $this->mouseOverText); ?>" onclick="scrollToTop()">
      <span></span>
 </a>

<script type="text/javascript">
   scriptJquery(window).scroll(function () {
  var scroll = scriptJquery(window).scrollTop();
  if (scroll >= 100) {
    scriptJquery("#scrollToTop").fadeIn();
  } else {
    scriptJquery("#scrollToTop").fadeOut();
  }
});

scriptJquery(document).on("click", "#scrollToTop", function () {
  scriptJquery("html, body").animate({ scrollTop: 0 }, 500);
});

</script>