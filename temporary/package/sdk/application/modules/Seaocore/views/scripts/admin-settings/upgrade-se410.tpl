<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('SocialEngineAddOns Core Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php if($this->upgraded): ?>
	<?php echo $this->translate("Your Website is already running a 4.10 version."); ?>
	<?php return; ?>
<?php endif; ?>
<div>
	<h3><?php echo $this->translate("Guidelines For Upgrading to SocialEngine 4.10") ?></h3>
	<p><?php echo $this->translate('If ‘Advanced Activity Feeds / Wall Plugin’ and ‘Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments’ plugins are installed on your website then you might face conflicts while upgrading SocialEngine 4.10.0. So, please follow below steps to upgrade to SocialEngine 4.10.0 smoothly. You can skip these steps if you did not get any errors while upgrading to SocialEngine 4.10.') ?></p><br />

	<?php $url = $this->url(array('action' => 'upgrade-se410-before')); ?>
	<div class="admin_seaocore_guidelines_wrapper">
		<ul class="admin_seaocore_guidelines" id="guideline_1">
			<li>
				<div class="steps">
					<a href="javascript:void(0);"><?php echo $this->translate("Step 1"); ?></a>
					<div>
						<p>We need to first check whether any conflicts are there before starting the upgradation process. To do so, please <a href ='<?php echo $url ?>' class='smoothbox'> <button>Click here</button></a><br><br>If you get a successful message here, you can proceed to the next step. And if it fails then please file a support ticket from the support section of your SocialEngineAddOns account.
						</p>
					</div>
				</div>
			</li>
			<li>   
				<div class="steps">
					<a href="javascript:void(0);"><?php echo $this->translate("Step 2"); ?></a>
					<div>
						<p>Now you can proceed with the upgradation process of SocialEngine 4.10. You can refer upgradation process steps from: <a href ='https://support.socialengine.com/php/customer/en/portal/articles/1666869-performing-upgrades' target="_blank"> SocialEngine Documentation</a>.</p>
					</div>
				</div>
			</li>
			<?php $url = $this->url(array('action' => 'upgrade-se410-after')); ?>
			<li>
				<div class="steps">
					<a href="javascript:void(0);"><?php echo $this->translate("Step 3"); ?></a>
					<div>
						<p>Once you are done with the upgradation of the SocialEngine and verified that SocialEngine has been upgraded successfully. You can upgrade SocialEngineAddons Core plugin from <a href="admin/seaocore/settings/upgrade" target="_blank"> Plugin Upgrades </a> section. If you already have the latest version of SocialEngineAddons Core Plugins or you don't want to upgrade to the latest version of SocialEngineAddons Core Plugin you can simply <a href ='<?php echo $url ?>' class='smoothbox'> <button>click here</button></a><br><br>If you get a successful message here, you are done with the SocialEngine upgradation process successfully.</p>
					</div>
				</div>
			</li>
		</ul>
	</div>
</div>