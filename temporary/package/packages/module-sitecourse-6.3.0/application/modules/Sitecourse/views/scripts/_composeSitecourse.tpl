<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')):?>
  <?php  $url = $this->url(array('action' => 'create'), "sitecourse_general", true); ?>
   

<?php   $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecourse/externals/scripts/composer_sitecourse.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
     composeInstance.addPlugin(new Composer.Plugin.Sitecourse({
          title: '<?php echo $this->translate('Create course') ?>',
          lang: {
            'Create course': '<?php echo $this->string()->escapeJavascript($this->translate('Create a course')) ?>'
          },
          loadJSFiles: ['<?php echo $this->tinyMCESEAO()->addJS(true) ?>'],
          packageEnable : '<?php echo Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecourse');?>',
          requestOptions: {
            'url': '<?php echo $url ?>'
          }
        }));
  });
</script>
<?php endif; ?>
