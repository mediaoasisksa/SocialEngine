<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
 <?php $backgroundUrl = isset($this->values['siteapi_spread_background']) && !empty($this->values['siteapi_spread_background']) ? $this->layout()->staticBaseUrl.$this->values['siteapi_spread_background']:'/application/modules/Siteandroidapp/externals/images/showcase-new.png'; ?>
<style>
#global_page_siteapi-index-app-page #global_content{ width: 100%; float:left;}
.siteapi-cont-body img{	margin: 5px; max-width: 100%; }
.siteapi-cont-body-bg{ width: 100%; float: left; box-sizing: border-box; background-size: cover; max-width: 100% !important;  padding: 50px 30px; background:url(<?php echo $backgroundUrl ?>) no-repeat; background-size: cover;}
.siteapi-cont-body .siteapi-cont-lf{ width: 54%; float: left;}
.siteapi-cont-body .siteapi-cont-lf a{color: #4bf;}
.siteapi-cont-heading{ color: #fff; font-family: 'Lato', sans-serif; font-weight: 300 !important; font-size: 64px !important; line-height: 80px;}
.siteapi-cont-subheading{color: #fff; font-family: 'Lato', sans-serif; font-weight: 100 !important; font-size: 45px !important; line-height: 55px; margin-top:40px;}
.siteapi-cont-body .siteapi-cont-rt{width: 41%; float: right; margin-left: 2%; text-align: right; margin-top: 3.6%;}
.siteapi-icon{margin-top:40px; text-align:center;}
.siteapi-icon-title{text-align: center; margin-top:40px;}
.siteapi-icon-title a{ display: inline-block; outline: 0; margin-left: 15px; margin-right: 15px;}

@media only screen and (min-width: 768px) and (max-width: 1024px){
	.siteapi-cont-heading{font-size: 30px !important; line-height:36px; text-align: center;}
	.siteapi-cont-subheading{font-size: 25px !important; line-height:35px; text-align: center;  margin-top:30px;}
	.siteapi-icon-title{margin-top:30px;}
} 
@media only screen and (max-width: 767px){
	.siteapi-cont-body .siteapi-cont-lf, .siteapi-cont-body .siteapi-cont-rt{width:100%;}
	.siteapi-cont-body .siteapi-cont-lf{margin-bottom: 30px;}
	.siteapi-cont-heading{font-size: 30px !important; line-height:36px; text-align: center;}
	.siteapi-cont-subheading{font-size: 25px !important; line-height:35px; text-align: center;  margin-top:30px;}
	.siteapi-icon-title{margin-top:30px;}
}
</style>
<div class="siteapi-cont-body">
<div class="siteapi-cont-body-bg">
  <div class="siteapi-cont-lf">
      <div class="siteapi-cont-heading"> <?php echo (isset($this->values['siteapi_spread_title_'.$this->defaultLanguage]) && !empty($this->values['siteapi_spread_title_'.$this->defaultLanguage])) ? $this->values['siteapi_spread_title_'.$this->defaultLanguage] : "Your community is now available on mobile!" ?> </div>
    <?php if(!empty($this->hasAndroidApp) && !empty($this->hasIosApp)){ ?>
    <div class="siteapi-cont-subheading"> <?php echo "Check out our " .'<a class="" href="' . $this->iosCallingURL . '">iOS</a>'." & ".'<a class="" href="' . $this->androidCallingURL . '">Android 		</a>'." App."  ?> </div>
    <div class="siteapi-icon"> <img src="<?php echo $this->androidParentDirectoryPath ?>/App_Icons/icon 48 x 48.png" alt="Uploaded App Icon" /> </div>
    <?php }
	else if (!empty($this->hasAndroidApp)){?>
    <div class=""> <?php echo "Check out our " .'<a class="" href="' . $this->androidCallingURL . '">Android </a>'." App."  ?> </div>
    <div class=""> <img src="<?php echo $this->androidParentDirectoryPath ?>/App_Icons/icon 48 x 48.png" alt="Uploaded App Icon" /> </div>
    <?php
	}else if(!empty($this->hasIosApp)) {?>
    <div class=""> <?php echo "Check out our " .'<a class="" href="' . $this->iosCallingURL . '">iOS </a>'." App."  ?> </div>
    <div class=""> <img src="<?php echo $this->iosParentDirectoryPath ?>/App_Icons/icon 48 x 48.png" alt="Uploaded App Icon" /> </div>
    <?php
   }
   else{?>
    <div> <?php echo "Link Expired" ?> </div>
    <?php }	if (isset($this->hasAndroidApp) && !empty($this->hasAndroidApp)){  ?>
    <div class="siteapi-icon-title">
        <?php echo '<a class="" href="' . $this->androidCallingURL . '"><img src="'.$this->layout()->staticBaseUrl.'application/modules/Siteapi/externals/images/Google_play-store.png" alt="'.$this->iosAppBuilderParams['title'].'"/></a>'; ?>
        <?php } if (isset($this->hasIosApp) && !empty($this->hasIosApp)){ ?>
        <?php echo '<a class="" href="' . $this->iosCallingURL . '"><img src="'.$this->layout()->staticBaseUrl.'application/modules/Siteapi/externals/images/Apple_App_Store.png" alt="'.$this->iosAppBuilderParams['title'].'"/></a>'; ?>
    </div>
    <?php } ?>
  </div>
  <div class="siteapi-cont-rt">
      <?php $imageUrl = (isset($this->values['siteapi_spread_image']) && !empty($this->values['siteapi_spread_image'])) ? '/'.$this->values['siteapi_spread_image']:'application/modules/Siteandroidapp/externals/images/mobile-skeleton2.png'; ?>
	<img src='<?php echo $this->layout()->staticBaseUrl.$imageUrl ?>' alt="" data-pagespeed-url-hash="3334356780" onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
</div>
</div>
</div>
