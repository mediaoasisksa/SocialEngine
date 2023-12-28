<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    help-create-api.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('SocialEngine REST API Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
<div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
</div>
<?php endif; ?>

<div style="margin-top:15px;">
    <h3><?php echo $this->translate("API Documentations") ?></h3>
    <p><?php echo $this->translate("Below you can choose the content modules for which you want to see the REST API Documentation, Like if you have chosen Advanced Events then the you will be able to see the REST API Documentation of this module only.") ?></p><br />
    <?php $flag = 0; ?>
    <?php
    $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
    $path = $getHost . DIRECTORY_SEPARATOR . 'public/apidocumentation.html';

    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    $baseUrl = @trim($baseUrl, "/");
    if (!empty($baseUrl))
        $path = $getHost . DIRECTORY_SEPARATOR . $baseUrl . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'apidocumentation.html';
    ?>
    <div class="admin_seaocore_guidelines_wrapper">
        <ul class="admin_seaocore_guidelines" id="siteapi-config">
            <li>
                <div class="steps">
                    <a href="public/api-documentation/default/documentation.html" target="_blank">Basic SocialEngine REST Api</a>
                </div>
            </li>

            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/advancedactivity/documentation.html" target="_blank">Advanced Activity Feeds / Wall Plugin REST Api</a>
                </div>
            </li>
            <?php endif; ?>

            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/siteevent/documentation.html" target="_blank">Advanced Events Plugin REST Api</a>
                </div>
            </li>
            <?php endif; ?>

            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitepage/documentation.html" target="_blank">Directory / Pages Plugin REST Api</a>
                </div>
            </li>
            <?php endif; ?>

            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitereview/documentation.html" target="_blank">Multiple Listing Types Plugin Core (Reviews & Ratings Plugin) REST Api</a>
                </div>
            </li>
            <?php endif; ?>

            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitegroup/documentation.html" target="_blank">Groups / Communities Plugin REST Api</a>
                </div>
            </li>

            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitetagcheckin/documentation.html" target="_blank">Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin REST Api</a>
                </div>
            </li>
            <?php endif; ?>
            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/suggestion/documentation.html" target="_blank">Suggestions / Recommendations Plugin </a>
                </div>
            </li>
            <?php endif; ?>
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitereaction/documentation.html" target="_blank">Reactions & Stickers Plugin </a>
                </div>
            </li>
            <?php endif; ?>
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitecontentcoverphoto/documentation.html" target="_blank">Content Profiles - Cover Photo, Banner & Site Branding Plugin </a>
                </div>
            </li>
            <?php endif; ?>
                  <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitestore/documentation.html" target="_blank">Stores / Marketplace - Ecommerce </a>
                </div>
            </li>
            <?php endif; ?>
                  <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitevideo/documentation.html" target="_blank">Advanced Videos / Channels / Playlists Plugin </a>
                </div>
            </li>
            <?php endif; ?>
             <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember')): ?>
            <li>
                <div class="steps">
                    <a href="public/api-documentation/sitemember/documentation.html" target="_blank">Advanced Members Plugin - Better Browse & Search, User Reviews, Ratings & Location Plugin. </a>
                </div>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>