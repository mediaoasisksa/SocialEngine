<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<h2>
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) { echo $this->translate('SocialEngine REST API Plugin'); } else { echo $this->translate('SocialEngine REST API Plugin'); }?>
</h2>

<?php if (count($this->navigation)): ?>
<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
    <div class='settings'>
        <div>
            <h3><?php echo $this->translate("Map 'Profile Type' with respective Mobile / Contact Number profile question.") ?> </h3>
            <p class="form-description">
        <?php echo $this->translate("Below you can map Profile Type with required Mobile / Contact Number profile question. Based on this mapping, contacts in 'People you may know' feature will be synced in your mobile apps.") ?>
            </p>
        </div>
    </div>
</div>

<form id='saveorder_form' method='post' action='<?php echo $this->url(array('action' => 'manage')) ?>' style="overflow:hidden;">
    <input type='hidden'  name='order' id="order" value=''/>
    <div class="seaocore_admin_order_list" style="width:100%;">

        <div class="list_head">     
            <div style="width:10%;">
				<?php echo $this->translate("Profile Type ID") ?>
            </div>
            <div style="width:20%;">
				<?php echo $this->translate("Profile Type Label") ?>
            </div>
            <div style="width:25%;">
				<?php echo $this->translate("Mobile / Contact Number Question") ?>
            </div>
            <div style="width:20%;">
				<?php echo $this->translate("Option") ?>
            </div>      
        </div>

        <div id='order-element'>
            <ul>
				<?php foreach ( $this->profileTypes as $key => $label) :?>
                <li>
                    <input type='hidden'  name='order[]' value='<?php echo $key; ?>'>
                    <div style="width:10%;">
							<?php echo $key ?>
                    </div>
                    <div style="width:20%;">
              <?php echo $this->translate($label) ?>
                    </div>
                    <div style="width:25%;">
              <?php 
              echo isset($this->mappedFieldLabels[$key]) ? $this->mappedFieldLabels[$key]: "-"; ?>
                    </div>

                    <div style="width:10%;">
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'profile-maps-contact', 'action' => 'map', 'option_id' => $key), $this->translate('Edit'),array(
	                            'class' => 'smoothbox',
	                          )) ?> 
                    </div>               
                </li>
				<?php endforeach; ?>
            </ul>
        </div>
    </div>
</form>
<br />

