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

<a href="<?php echo $this->url(array('module' => 'siteapi', 'controller' => 'admin-settings'), 'default', true) ?>" class="buttonlink"
   style="background-image:url(./application/modules/Seaocore/externals/images/back.png);padding-left:23px;" onclick="javascript:history.go(-1)">
    <?php echo $this->translate("Back to Previous Page") ?></a>


<div style="margin-top:15px;">
    <h3><?php echo $this->translate("Extending APIs to other modules") ?></h3>
    <p><?php echo $this->translate("The REST API for SocialEngine by SocialApps.tech directly supports all SocialEngine core features, all SocialEngine official plugins, and selected SocialApps.tech plugins. We're developing APIs for more SocialApps.tech plugins and will be releasing them in subsequent upgrades. A big plus point of this API system is that it can easily be extended to other 3rd-party modules/plugins by following the below steps:") ?></p><br />
    <?php $flag = 0; ?>
    <div class="admin_seaocore_guidelines_wrapper">
        <ul class="admin_seaocore_guidelines" id="siteapi-config">
            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>
                        <p>
                            Suppose that the module's name is: "<i><b>MODULENAME</b></i>". Create a <i><b>siteapi</b></i> directory in <i><b>/application/modules/MODULENAME/controllers/</b></i> and a <i><b>Siteapi</b></i> directory in <i><b>/application/modules/MODULENAME/Api/</b></i> directory of your module / plugin [Screenshots accompanying here are for the APIs of Core module].
                            <br /><br />

                            <img src="./public/siteapi-guidelines/api_documentation_1.png" />
                        </p>					
                    </div>
                </div>
            </li>

            <li>		
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>                        
                        <p>
                            Create a new file: "<i><b>IndexController.php</b></i>" in <i><b>/application/modules/MODULENAME/controllers/siteapi/</b></i> directory, with the class name in it as: "<i><b>MODULENAME_IndexController</b></i>". This class should extend "<i><b>Siteapi_Controller_Action_Standard</b></i>" class.                            
                            <br /><br />
                            <!--Add step2_1 image-->
                            <img src="./public/siteapi-guidelines/api_documentation_2_1.png" />
                            <br /><br />
                            <img src="./public/siteapi-guidelines/api_documentation_2_2.png" />
                        </p>

                    </div>
                </div>
            </li>

            <li>		
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>                        
                        <p>
                            Define the actions for the API requests of this module in this class (example: "browseAction()").

                            <br /><br />
                            <!--Add step3_1 image-->
                            <img src="./public/siteapi-guidelines/api_documentation_3_1.png" />
                        </p>

                    </div>
                </div>
            </li>

            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>                        
                        <p>
                            To access the API actions, create the desired API routes in /application/modules/Siteapi/settings/apiroutes.php file.
                            <br /><br />
                            <!--Add step4_1 image-->
                            <img src="./public/siteapi-guidelines/api_documentation_4_1.png" />
                        </p>

                    </div>
                </div>
            </li>
            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>                        
                        <p>
                             To enable the API of your desired module add module name in /application/modules/Siteapi/ApiBootstrap.php file $getEnabledModulesArray array.
                            <br /><br />
                            <!--Add step4_1 image-->
                            <img src="./public/siteapi-guidelines/api_documentation_5_1.png" />
                        </p>

                    </div>
                </div>
            </li>
            <li>
                <div class="steps">
                    <a href="javascript:void(0);" onClick="guideline_show('siteapistep-<?php echo ++$flag; ?>');"><?php echo $this->translate("Step " . $flag) ?></a>
                    <div id="siteapistep-<?php echo $flag; ?>" style='display: none;'>                        
                        <p>
                            You may now call the API created for the module at their URL like: <i><b>https://example.com/api/rest/CREATED_ROUTE</b></i> (example: https://example.com/api/rest/core/browse).
                        </p>

                    </div>
                </div>
            </li>
        </ul>
    </div>
    <script type="text/javascript">
        function guideline_show(id) {
           if ($(id).style.display == 'block') {
              $(id).style.display = 'none';
           } else {
              $(id).style.display = 'block';
           }
        }
    </script>
</div>