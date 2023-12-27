<?php echo $this->render('_jsAdmin.tpl') ?>

<h2>
    <?php echo $this->translate('Services Booking & Appointments Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php if (count($this->childNavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->childNavigation)->render() ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Profile Fields for Services'); ?></h3>

<p>
    <?php echo $this->translate('Profile information will enable service owner to add additional information about their service. This non-generic additional information will help others know more specific details about the service. Below, you can create Profile Types for the Services on your site, and then create Profile Information Fields for those profile types. Multiple profile types enable you to have different profile information fields for different type of services. You can also map the Categories for services with Profile Types from the "Category-Service Profile Mapping" section such that if an service belongs to a category, it will automatically have the corresponding profile fields.'); ?>
</p>

<br />

<div class="admin_fields_type">
    <h3><?php echo $this->translate("Editing Profile Information Fields for Service Profile Type:") ?></h3>
    <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion">    <?php echo $this->translate('Add Question'); ?></a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate('Add Heading'); ?></a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate('Rename Profile Type'); ?></a>
    <?php if (count($this->topLevelOptions) > 1): ?>
        <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate('Delete Profile Type'); ?></a>
    <?php endif; ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate('Create New Profile Type'); ?></a>
</div>

<br />

<ul class="admin_fields">
    <?php foreach ($this->secondLevelMaps as $map): ?>
        <?php echo $this->adminFieldMeta($map) ?>
    <?php endforeach; ?>
</ul>

<br />