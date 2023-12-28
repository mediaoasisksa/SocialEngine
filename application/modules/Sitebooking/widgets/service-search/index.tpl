<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
    ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>

<?php 
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>

<?php
  echo $this->partial('_jsSwitch.tpl', 'fields', array( 
  ))
?>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>

<script type="text/javascript">

  var profile_type = 0;
  var previous_mapped_level = 0;
  
  function showFields(cat_value, cat_level) {

    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value);
      if (profile_type == 0) {
        profile_type = '';
      } else {
        previous_mapped_level = cat_level;
      }
      document.getElementById('profile_type').value = profile_type;
      changeFields(document.getElementById('profile_type'));
    }
  }

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitebooking')->getMapping('profile_type')); ?>;
    for (i = 0; i < mapping.length; i++) {
      if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }

</script>

<script type="text/javascript">
  var locationEl = document.getElementById('location');
  var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);

  <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
  
</script>