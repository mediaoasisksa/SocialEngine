<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    status.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo (isset($this->table->status) && !empty($this->table->status)) ? $this->translate('Disable Consumer?') : $this->translate('Enable Consumer?'); ?></h3>
        <p>

            <?php
            if (isset($this->table->status) && !empty($this->table->status)):
                echo $this->translate('Are you sure that you want to disable this OAuth Consumer? By this, all the APPs which are using these combination of keys will stop working.');
            else:
                echo $this->translate('Are you sure that you want to enable this OAuth Consumer? By this, all the APPs which are using these combination of keys will start working.');
            endif;
            ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="id" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo (isset($this->table->status) && !empty($this->table->status)) ? $this->translate('Disable') : $this->translate('Enable'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>