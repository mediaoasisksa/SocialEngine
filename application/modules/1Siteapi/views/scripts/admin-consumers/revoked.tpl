<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    revoked.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo (isset($this->table->revoked) && !empty($this->table->revoked)) ? $this->translate('Allow Token?') : $this->translate('Revoke Token?'); ?></h3>
        <p>

            <?php
            if (isset($this->table->revoked) && !empty($this->table->revoked)):
                echo $this->translate('Are you sure that you want to allow this OAuth token for this site member? After allowing, the site member will be able to access the server resources / get responses from API.');
            else:
                echo $this->translate('Are you sure that you want to revoke this OAuth Token. If revoked, this site member will not be able to access the server resources / get responses from API.');
            endif;
            ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="id" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo (isset($this->table->revoked) && !empty($this->table->revoked)) ? $this->translate('Allowed') : $this->translate('Revoked'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>