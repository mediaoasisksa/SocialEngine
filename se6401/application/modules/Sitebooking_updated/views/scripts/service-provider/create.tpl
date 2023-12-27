<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<?php if($this->count < $this->quota || $this->quota == 0)
{ 
    echo $this->form->render($this);
}
else
{?>
    <div class="tip">
        <span>
          <?php echo $this->translate('You have already created the maximum number of service providers allowed.');?>
          <?php echo $this->translate('If you would like to create a new service provider, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'sitebooking_provider_general'));?>
        </span>
    </div>
<?php }

?>

<script type="text/javascript">
var locationEl = document.getElementById('location');
var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
<?php  include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl';?>

scriptJquery('#provider_slug').on('focus',function(){
  if(this.value == ''){
    this.value = scriptJquery('#provider_title').val();
  }
  (scriptJquery.ajax({
    'dataType': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'create'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
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
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'create'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
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
</script>



    
 
    
  
