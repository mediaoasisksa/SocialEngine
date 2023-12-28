<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    faq_help.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
<?php if ($this->faq_id): ?>
        window.addEvent('domready', function () {
            faq_show('<?php echo $this->faq_id; ?>');
        });
<?php endif; ?>
</script>

<?php $i = 1; ?>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('How can I start making API calls? Can I see an example API call for my website?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('You should follow the below steps to make calls to the API:'); ?><br />&nbsp;&nbsp;
                    <?php 
                        $host = _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
                        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                        $baseUrl = @trim($baseUrl, "/");                        
                        $baseUrl = (!empty($baseUrl))? $baseUrl . '/': '';
                        
                        $select = Engine_Api::_()->getDbtable('consumers', 'siteapi')->getSelect();
                        $select = $select->limit(1);
                        $getRow = Engine_Api::_()->getDbtable('consumers', 'siteapi')->fetchRow($select);
                    ?>
                    <?php echo $this->translate('- All requests to this REST API should have the base URL like: ' . $host . '/' . $baseUrl . 'api/rest/'); ?> <br />&nbsp;&nbsp;
                    <?php echo $this->translate('- We recommend that all API requests should be made on SSL (HTTPS).'); ?> <br />&nbsp;&nbsp;
                    <?php echo $this->translate('- You should send `oauth_consumer_key` and `oauth_consumer_secret` in HTTP header (or as query string) of the API call to identify if the API request is valid or not (see last step below).'); ?><br />&nbsp;&nbsp;
                    <?php echo $this->translate('- To make API calls for logged-in users, you need to send oauth_token and oauth_secret in HTTP header (or as query string) of the API call (see "/login" call in the API documentation).'); ?><br />&nbsp;&nbsp;
                    <?php echo $this->translate('- Example: API call for your website to get blogs is: <a href="' . $host . '/' . $baseUrl . 'api/rest/blogs?oauth_consumer_key=' . $getRow->key . '&oauth_consumer_secret=' . $getRow->secret . '" target="_blank">' . $host . '/' . $baseUrl . 'api/rest/blogs?oauth_consumer_key=' . $getRow->key . '&oauth_consumer_secret=' . $getRow->secret . '</a>'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What should be done to ensure that API calls for my website are secure?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('We strongly recommend all API requests to be sent on SSL (https://) for security reasons. Plain HTTP requests are insecure because API access credentials (Consumer Key and Consumer Secret) in them are prone to phishing.<br />If you need help in enabling HTTPS for your website, then you may purchase our "<a href=\'http://www.socialengineaddons.com/services/ssl-certification-installation\' target="_blank">SSL Certificate Installation Service</a>".'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Can i use my website\'s APIs concurrently on multiple clients like iOS application, Android application, a stand-alone web application, etc?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Yes. The "API Consumers" section in the Admin Panel of this plugin enables you to create multiple API clients with their own API Consumer Key and API Consumer Secret credentials.'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What are the limitations of this API?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('- The API currently does not support the subscription step in signup. Work on this is planned in the next upgrade.'); ?><br />
                    <?php echo $this->translate('- If there is a 3rd-party plugin installed on your website that alters the default signup process of SocialEngine, then those alterations will not be supported in the signup API.'); ?><br />
                    <?php echo $this->translate('- The API currently does not support payments.'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want to extend these APIs for other modules that are used on my website. Is this possible? If yes, then how?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Yes, this is possible. To extend these APIs for other modules, please follow the "Guidelines for extending APIs to other modules" from "Global Settings" of this plugin. These APIs are easy to extend, and you may contact the developer of that module for this.'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I do not want the API documentation to be publicly visible. What should I do?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('The location of the API documentation is . You may change the file name: "apidocumentation.html" to something that cannot be guessed, or you may save its copy with yourself and delete this file. [Note: Whenever you will upgrade this API plugin, the documentation file will be created on your server again.]'); ?>
                </div>
            </div>
        </li>
        
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want to share the API documentation with my developer so that they can work on new features. How can I do this?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('You may send the "' . APPLICATION_PATH . '/apidocumentation.html" file to your developer, which exists at location on your server.'); ?>
                </div>
            </div>
        </li>

    </ul>
</div>