<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    details.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="siteapi_admin_popup"> 
    <div>
        <h3><?php echo $this->translate('API Client Details'); ?></h3>
        <br />
        <table cellpadding="0" cellspacing="0" class="siteapi-view-detail-table">
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" width="350">
                        <tr>

                            <td width="120"><b><?php echo $this->translate('Title:'); ?></b></td>

                            <td>
                                <?php echo $this->apiClientDetails->getTitle(); ?>&nbsp;&nbsp;

                            </td>             

                        <tr>
                            <td><b><?php echo $this->translate('Consumer Key:'); ?></umb></td>
                            <td><?php echo $this->translate($this->apiClientDetails->key); ?> </td>
                        </tr>

                        <tr>
                            <td><b><?php echo $this->translate('Consumer Secret:'); ?></b></td>
                            <td><?php echo $this->translate($this->apiClientDetails->secret); ?> </td>
                        </tr>

                        <tr>
                            <td><b><?php echo $this->translate('Creation Date:'); ?></b></td>
                            <td>
                                <?php echo $this->translate(gmdate('M d,Y g:i A', strtotime($this->apiClientDetails->creation_date))); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><b><?php echo $this->translate('Status:'); ?></b></td>
                            <td>
                                <?php
                                if ($this->apiClientDetails->status)
                                    echo $this->translate('Enabled');
                                else
                                    echo $this->translate("Disabled");
                                ?>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>		
        <br />
        <button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close') ?></button>
    </div>
</div>	

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>