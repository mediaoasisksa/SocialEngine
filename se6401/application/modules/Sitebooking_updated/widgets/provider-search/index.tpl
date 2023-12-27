<?php
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
  $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<?php echo $this->form->render($this) ?>

<script type="text/javascript">
  var locationEl = document.getElementById('location');
  var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
  <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
</script>

