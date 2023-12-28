<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _shareButtons.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->subject->getHref(array('showEventType' => 'upcoming')));
$object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->subject->getHref();
echo '<div class="siteevent_grid_footer seaocore_button" id="sitecontentcoverphoto_share_buttons"><a href="javascript:void(0);" class="siteevent_share_links_toggle" ><span class="seaocore_icon_share" id="sitecontentcoverphoto_share_toggle" onclick="showCoverPhotoShareLinks();">' . $this->translate('Share') . '</span></a>'
 . '<ul class="siteevent_share_links dropdown-menu" style="display:none;" id="sitecontentcoverphoto_share_links" >'
 . '<li><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '"><span class="seao_icon_facebook"></span>' . $this->translate('Share on Facebook') . '</a></li>'
 . '<li><a target="_blank" href="http://twitter.com/share?text=' . $this->subject->getTitle() . '&url=' . $urlencode . '"><span class="seao_icon_twitter"></span>' . $this->translate('Share on Twitter') . '</a></li>'
 . '<li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $object_link . '"><span class="seao_icon_linkedin"></span>' . $this->translate('Share on LinkedIn') . '</a></li>'
 . '<li><a target="_blank" href="https://plus.google.com/share?url=' . $urlencode . '&t=' . $this->subject->getTitle() . '"><span class="seao_icon_google_plus"></span>' . $this->translate('Share on Google+') . '</a></li>'
 . '<li><a onclick="shareOnWebsite();return false;" href="javascript:void(0);"><span class="smoothbox seao_icon_sharelink"></span>' . $this->translate('Share on %s', Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'))) . '</a></li>'
 ;
 if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('primemessenger') && Engine_Api::_()->primemessenger()->isPrimeMessengerActive()) { 
 echo '<li><a id="pm_share_message_btn" href="javascript:void(0);" onclick="window.parent.pm.share(\'' . urldecode($urlencode) . '\');"><span class="seao_icon_messenger"></span>' . $this->translate('Share on Messenger')
 . '</a></li>' ; }
 echo '</ul></div>' ;
?>

<script type="text/javascript">
    function shareOnWebsite() {
        var urlShare = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $this->subject->getType(), 'id' => $this->subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true); ?>';

        Smoothbox.open(urlShare);
    }

</script>
