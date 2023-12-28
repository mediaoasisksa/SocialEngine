<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: success.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>
<div class='layout_middle'>
  <div class='generic_layout_container'>
    <div class='global_form'>
      <form method="post" class="global_form">
        <div>
          <div>
          <h3><?php echo $this->translate('Listing Posted');?></h3>
          <p>
            <?php echo $this->translate('Your listing was successfully published.');?>
          </p>
          <p>
          <input type="hidden" name="confirm" value="true"/>
          <a class="employment_listing_button" href='<?php echo $this->url(array('action' => 'manage'), 'employment_general', true) ?>'>
              <?php echo $this->translate('View my listings');?>
          </a>
          </p>
        </div>
        </div>
      </form>
    </div>
  </div>
</div>
