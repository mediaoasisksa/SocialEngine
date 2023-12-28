<?php $this->tinyMCESEAO()->addJS();?>

<?php
  echo $this->partial('_jsSwitch.tpl', 'fields', array( 
  ))
?>
<div class="provider_short_info">
  <div class="_left">
    <span class="_thumb"><?php echo $this->itemBackgroundPhoto($this->service, 'thumb.main') ?></span>
    <h3 class="_name"><?php echo $this->service->getTitle(); ?></h3>
  </div>
  <div class="_right">
    <?php echo $this->htmlLink($this->service->getHref(), 'View profile'); ?>
  </div>
</div>

<?php echo $this->form->render($this); ?>

<script type="text/javascript">  

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitebooking')->getMapping('profile_type')); ?>;

    for (i = 0; i < mapping.length; i++) {
    if (mapping[i].category_id == category_id){
      return mapping[i].profile_type;
    }
    }
  
    return 0;
    
  }

  en4.core.runonce.add(function() {

    if($('longDescription-wrapper')) {
      <?php echo $this->tinyMCESEAO()->render(array('element_id' => '"longDescription"',
      'language' => '',
      'directionality' => '',
      'upload_url' => '')); ?>
    }


  $$('.sitebooking_main_provider_manage').getParent().addClass('active');
  var defaultProfileId = '<?php echo '0_0_1' ?>' + '-wrapper';
    if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
    $(defaultProfileId).setStyle('display', 'none');
    }
  }); 

$('service_slug').addEvent('focus',function(){
  if(this.value == ''){
  this.value = $('service_title').value;
  }
  (new Request.JSON({
  'format': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'edit'), 'default', true) ?>',
  'data' : {
    'format' : 'json',
    'slug' : this.value,
    'isAjax' : '1',
    'pro_id' : <?php echo $this->pro_id ?>,
    'ser_id' : <?php echo $this->ser_id ?>,
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

$('service_slug').addEvent('keyup',function(){
  
  (new Request.JSON({
  'format': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'edit'), 'default', true) ?>',
  'data' : {
    'format' : 'json',
    'slug' : this.value,
    'isAjax' : '1',
    'pro_id' : <?php echo $this->pro_id ?>,
    'ser_id' : <?php echo $this->ser_id ?>,
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


<script type="text/javascript">

  var servicData = <?php echo $jsondata = json_encode(Engine_Api::_()->getItem('sitebooking_ser', $this->ser_id)->toArray());?>;
  window.addEvent('domready',function() {
    if(servicData['first_level_category_id'] != 0 && servicData['first_level_category_id'] != null) {
    sitebooking_addOptions($('category_id-wrapper1').value); 
    $("first_level_category_id").value = servicData['first_level_category_id'];    
    }
    if(servicData['second_level_category_id'] != 0 && servicData['second_level_category_id'] != null) {
    sitebooking_addSubOptions($('first_level_category_id').value); 
    $("second_level_category_id").value = servicData['second_level_category_id'];    
    }
    var profile_type = getProfileType($('category_id-wrapper1').value);
    if(profile_type == 0) profile_type = '';
    $('0_0_1').value = profile_type;
    changeFields($('0_0_1'));
  });

</script>
