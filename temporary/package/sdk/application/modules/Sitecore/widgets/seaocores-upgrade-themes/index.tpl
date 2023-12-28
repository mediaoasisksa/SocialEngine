<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Sociealengineaddon
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/admin/core.js');?>
<script type="text/javascript">
    function upgradePlugin(url) {
        Smoothbox.open(url);
    }
</script>
<h3>
    <?php echo $this->translate('Latest versions of SocialEngineAddOns themes for your site') ?>
</h3>
<p>
    <?php echo $this->translate('Here, you can upgrade the latest version of these themes by using ‘Upgrade’ button available in front of all the desired themes that needs to be upgraded.<br />The latest versions of these themes are also available to you in your SocialEngineAddOns Client Area. Login into your SocialEngineAddOns Client Area here: <a href="http://www.socialengineaddons.com/user/login" target="_blank">http://www.socialengineaddons.com/user/login</a>.'); ?>
</p><br />


<div class='sociealengineaddons_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>

<?php
if (count($this->channel)):
    $isPlugin = false;
    $tempFlag = 0;
    ?>
    <table class='admin_table'>
        <?php foreach ($this->channel as $item): ?>
            <?php
            if ($item['ptype'] === 'sitereviewlistingtype') {
                $product_version = $this->sitereviewListingTypeVersion;
            } else {
                $product_version = $item['product_version'];
            }
            $running_version = $item['running_version'];
//				$versionInfo = 0;
            $status = $this->translate('No');
            $shouldUpgrade = FALSE;
            if (!empty($running_version) && !empty($product_version)) {
                if (empty($tempFlag)):
                    ?>
                    <thead>
                        <tr>
                            <th align="left">
                                <input type="checkbox" class="seaocore_multi-upgrade_checkall" name="multi-upgrade" onclick="checkUncheckSeaoAll(this)">
                            </th>
                            <th align="left">
                                <?php echo $this->translate("Plugin Title"); ?>
                            </th>
                            <th align="left">
                                <?php echo $this->translate("Latest version on SocialEngineAddOns.com"); ?>
                            </th>
                            <th align="left">
                                <?php echo $this->translate("Version on your website"); ?>
                            </th>
                            <th align="left">
                                <?php echo $this->translate("Should you Upgrade?"); ?>
                            </th>
                            <th align="left">
                                <?php echo $this->translate("Upgrade?"); ?>
                            </th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                    endif;
                    $tempFlag ++;
                    $isPlugin = true;
                    $verBool = Engine_Api::_()->seaocore()->checkVersion($product_version, $running_version);
                    if ($verBool == 1) {
                        $shouldUpgrade = TRUE;
                        $status = $this->translate('Yes');
                    }
                    ?>
                    <tr>
                        <?php
                            $url = $this->url(array('module' => 'sitecore', 'controller' => 'settings', 'action' => 'upgrade-plugin', 'name' => @base64_encode($item['name']), 'version' => $product_version, 'ptype' => $item['ptype'], 'key' => $item['key'], 'title' => str_replace("/", "_", @base64_encode($item['title'])), 'calling' => 'seaocore'), 'admin_default', true);
                        ?>
                        <td>
                            <?php if(!empty($shouldUpgrade)) : ?>
                                <input type="checkbox" class="seaocore_multi-upgrade" name="multi-upgrade" data-url='<?php echo $url; ?>'>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['title']; ?></td>
                        <td><?php echo $product_version; ?></td>
                        <td><?php echo $running_version; ?></td>
                        <td><?php echo $status; ?></td>
                        <td>

                            <?php 
                            $title = $this->translate("Upgrade '%s' to latest version %s", $item['title'], $product_version);
                            if (empty($shouldUpgrade)):
                                echo '-';
                            else:
                                ?>
                                <button title="<?php echo $title; ?>" onclick="upgradePlugin('<?php echo $url; ?>')">Upgrade</button>
                            <?php endif; ?>
                        </td> 
                    </tr>
                <?php } ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(!empty($isPlugin)): ?>
        <button title="Upgrade Selected" class="seaocore_upgrade_btn" onclick="upgradeSelectedSeaoPlugins()">Upgrade Selected</button>
    <?php endif; ?>
    <br />

    <?php
endif;
if (empty($isPlugin)):
    ?>
    <?php echo '<div class="tip"><span>You have not purchased any SocialEngineAddOns Themes. Please click <a href="http://www.socialengineaddons.com/catalog/themes" target="_blank">here</a> to view and purchase engaging & attractive themes from SocialEngineAddOns.</span></div>'; ?>  
<?php endif; ?>