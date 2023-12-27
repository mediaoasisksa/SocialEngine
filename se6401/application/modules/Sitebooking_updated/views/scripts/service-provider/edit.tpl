<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<div class="provider_short_info">
	<div class="_left">
		<span class="_thumb"><?php echo $this->itemBackgroundPhoto($this->provider, 'thumb.main') ?></span>
		<h3 class="_name"><?php echo $this->provider->getTitle(); ?></h3>
		<span class="_location"><i class="fa fa-map-marker"></i><?php echo $this->provider->location; ?></span>
	</div>
	<div class="_right">
		<?php echo $this->htmlLink($this->provider->getHref(), 'View profile'); ?>
	</div>
</div>
    
<?php echo $this->form->render($this); ?>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    scriptJquery('.sitebooking_main_provider_manage').parents().addClass('active');

  });

	var locationEl = document.getElementById('location');
	var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
  <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>


scriptJquery('#provider_slug').on('focus',function(){
  if(this.value == ''){
    this.value = scriptJquery('#provider_title').val();
  }
  (scriptJquery.ajax({
    'dataType': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'edit'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
      'pro_id' : <?php echo $this->pro_id ?>,
    },  
    success: function(responseJSON) {
      
      if(responseJSON.flag == false){
        var p = document.createElement("P");
        p.id = "slug_error_msg";
        if( !scriptJquery('#slug_error_msg') ){
          scriptJquery('#slug-element').appendChild(p);
        }
        scriptJquery('#slug_error_msg').html("This URL is already taken.");
      }
      else{
        if( scriptJquery('#slug_error_msg') ){
          scriptJquery('#slug_error_msg').remove();
        }
      }
    }     
  }));
});

scriptJquery('#provider_slug').on('keyup',function(){
  
  (scriptJquery.ajax({
    'dataType': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'edit'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
      'pro_id' : <?php echo $this->pro_id ?>,
    },  
    success: function(responseJSON, responseText) {
      if(responseJSON.flag == false){
        var p = document.createElement("P");
        p.id = "slug_error_msg";
        if( !scriptJquery('#slug_error_msg') ){
          scriptJquery('#slug-element').appendChild(p);
        }
        scriptJquery('#slug_error_msg').html("This URL is already taken.");
      }
      else{
        if( scriptJquery('#slug_error_msg') ){
          scriptJquery('#slug_error_msg').remove();
        }
      }  
    }     
  }));
});
</script>