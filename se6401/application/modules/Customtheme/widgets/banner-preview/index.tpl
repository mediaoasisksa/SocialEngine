<?php
/**
* SocialEngine
*
* @category   Application_Core
* @package    Core
* @copyright  Copyright 2006-2010 Webligo Developments
* @license    http://www.socialengine.com/license/
* @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
* @author     Jung
*/
?>
<div class="core_banner" <?php if( $this->banner->getPhotoUrl() ): ?> style="background-image: url(<?php echo $this->banner->getPhotoUrl()?>)" <?php endif;?>>

</div>

<style type="text/css">
.layout_hpbblobk_banner-preview>div {
    min-height: 250px;
    position: relative;
    background: #CC0821;
    background: -webkit-linear-gradient(to left, #CC0821, #eef2f3);
    background: linear-gradient(to left, #CC0821, #eef2f3);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: 0 center;
}
</style>
