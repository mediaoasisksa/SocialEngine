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

$('provider_slug').addEvent('focus',function(){
  if(this.value == ''){
    this.value = $('provider_title').value;
  }
  (new Request.JSON({
    'format': 'json',
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'create'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
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
    'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service-provider', 'action' => 'create'), 'default', true) ?>',
    'data' : {
      'format' : 'json',
      'slug' : this.value,
      'isAjax' : '1',
    },  
    onSuccess: function(responseJSON, responseText) {
      if(responseJSON.flag == false){
        var p = document.createElement("P");
        p.id = "slug_error_msg";
        if( !$('slug_error_msg') ){
          $('slug-element').appendChild(p);
        }
        $('slug_error_msg').innerHTML = "This URL is already taken."

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



    
 
    
  
