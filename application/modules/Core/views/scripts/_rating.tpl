<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Core
* @copyright  Copyright 2006-2020 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: _rating.tpl 9785 2012-09-25 08:34:18Z $
*/

?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting($this->module.'.enable.rating', 1)) { ?>
  <?php $ratingIcon = Engine_Api::_()->getApi('settings', 'core')->getSetting($this->module.'.ratingicon', 'fas fa-star'); ?>
  <?php $param = $this->param; ?>
  <?php if($param == 'create') { ?>
    <?php 
      $viewer_id = $this->viewer()->getIdentity();
      $rated = $this->rated;
    ?>
    <div class="rating rating_star_big" onmouseout="rating_out();">
      <span id="rate_1" class="rating_star_big_generic <?php echo $ratingIcon; ?>" <?php if (!$rated && $viewer_id):?> onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
      <span id="rate_2" class="rating_star_big_generic <?php echo $ratingIcon; ?>" <?php if (!$rated && $viewer_id):?> onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
      <span id="rate_3" class="rating_star_big_generic <?php echo $ratingIcon; ?>" <?php if (!$rated && $viewer_id):?> onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
      <span id="rate_4" class="rating_star_big_generic <?php echo $ratingIcon; ?>" <?php if (!$rated && $viewer_id):?> onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
      <span id="rate_5" class="rating_star_big_generic <?php echo $ratingIcon; ?>" <?php if (!$rated && $viewer_id):?> onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
      <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
    </div>
  <?php } else if($param == 'show') { ?>
    <?php $item = $this->item ;?>
    <span class="star_rating_wrapper rating_star_show">
      <?php for( $x=1; $x <= $item->rating; $x++ ): ?>
        <span class="rating_star_generic rating_star <?php echo $ratingIcon; ?>"></span>
      <?php endfor; ?>
      <?php if( (round($item->rating) - $item->rating) > 0): ?>
        <span class="rating_star_generic rating_star_half <?php echo $ratingIcon; ?>"></span>
      <?php endif; ?>
      <?php for( $x=5; $x > round($item->rating); $x-- ): ?>
        <span class="rating_star_generic rating_star_empty <?php echo $ratingIcon; ?>"></span>
      <?php endfor; ?>
    </span>
  <?php } ?>
<?php } ?>
