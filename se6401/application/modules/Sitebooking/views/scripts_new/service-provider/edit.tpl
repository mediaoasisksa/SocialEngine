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
    $$('.sitebooking_main_provider_manage').getParent().addClass('active');

  });

	var locationEl = document.getElementById('location');
	var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
  <?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>


$('provider_slug').addEvent('focus',function(){
  if(this.value == ''){
    this.value = $('provider_title').value;
  }
  (new Request.JSON({
    'format': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'edit'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
      'pro_id' : <?php echo $this->pro_id ?>,
    },  
    onSuccess: function(responseJSON, responseText) {
      
      if(responseJSON.flag == false){
        var p = document.createElement("P");
        p.id = "slug_error_msg";
        if( !$('slug_error_msg') ){
          $('slug-element').appendChild(p);
        }
        $('slug_error_msg').innerHTML = "This URL is already taken.";
      }
      else{
        if( $('slug_error_msg') ){
          $('slug_error_msg').remove();
        }
      }
    }     
  })).send();
});

$('provider_slug').addEvent('keyup',function(){
  
  (new Request.JSON({
    'format': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'edit'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
      'pro_id' : <?php echo $this->pro_id ?>,
    },  
    onSuccess: function(responseJSON, responseText) {
      if(responseJSON.flag == false){
        var p = document.createElement("P");
        p.id = "slug_error_msg";
        if( !$('slug_error_msg') ){
          $('slug-element').appendChild(p);
        }
        $('slug_error_msg').innerHTML = "This URL is already taken.";
      }
      else{
        if( $('slug_error_msg') ){
          $('slug_error_msg').remove();
        }
      }  
    }     
  })).send();
});
</script>