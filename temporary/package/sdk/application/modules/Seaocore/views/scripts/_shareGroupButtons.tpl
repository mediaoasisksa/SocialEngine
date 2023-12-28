<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _shareGroupButtons.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
 try { 
$urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'].$this->subject->getHref());
$object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->subject->getHref();

$urlShare = $this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->subject->getType(), 'id' => $this->subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true);

echo '<div class="sitegroup_grid_footer"><div><a href="javascript:void(0);"  class="sitegroup_share_links_toggle"><span class="seao_icon_share"></span>' . $this->translate('Share') . '</a>'
. '<div class="sitegroup_share_links" style="display:none;"><ul class="dropdown-menu social-share tall-group-box-menu">'
. '<li class="share-btn"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '"><span class="seao_icon_facebook"></span>' . $this->translate('Share on Facebook') . '</a></li>'
. '<li class="share-btn"><a target="_blank" href="http://twitter.com/share?text=' . $this->subject->getTitle() . '&url=' . $urlencode . '"><span class="seao_icon_twitter"></span>' . $this->translate('Share on Twitter') . '</a></li>'
. '<li class="share-btn"><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $object_link . '"><span class="seao_icon_linkedin"></span>' . $this->translate('Share on LinkedIn') . '</a></li>'
. '<li class="share-btn"><a target="_blank" href="https://plus.google.com/share?url=' . $urlencode . '&t=' . $this->subject->getTitle() . '"><span class="seao_icon_google_plus"></span>' . $this->translate('Share on Google+') . '</a></li>'
. '<li class="share-btn"><a onclick="javascript:Smoothbox.open(\''.$urlShare.'\');" href="javascript:void(0);"><span class="smoothbox seao_icon_sharelink"></span>' . $this->translate('Share on %s', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'))) . '</a></li>' . '</ul></div></div></div>'
;

} catch(Exception $e) {
die("Exception ".$e);
}
?>

<!--<script type="text/javascript">
    function shareOnWebsite(urlShare) {
//        var urlShare = '<?php // echo $this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->subject->getType(), 'id' => $this->subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true); ?>';
        Smoothbox.open(urlShare);
    }
</script>-->
