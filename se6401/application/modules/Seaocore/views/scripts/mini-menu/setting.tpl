<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: setting.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<ul>
  <?php foreach($this->settings as $setting) : ?>
    <li onclick="getSettingUrlLink('<?php echo $setting->getHref(); ?>');">
      <a href="<?php echo $setting->getHref() ?>">
        <?php $tempLabel = $setting->getLabel(); ?>
        <?php echo $this->translate($tempLabel); ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
