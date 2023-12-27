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

    if(scriptJquery('#longDescription-wrapper')) {
      <?php echo $this->tinyMCESEAO()->render(array('element_id' => '"longDescription"',
      'language' => '',
      'directionality' => '',
      'upload_url' => '')); ?>
    }


  scriptJquery('.sitebooking_main_provider_manage').parents().addClass('active');
  var defaultProfileId = '<?php echo '0_0_1' ?>' + '-wrapper';
    if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
    $(defaultProfileId).css('display', 'none');
    }
  }); 

scriptJquery('#service_slug').on('focus',function(){
  if(this.value == ''){
  this.value = scriptJquery('#service_title').val();
  }
  (scriptJquery.ajax({
  'dataType': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'edit'), 'default', true) ?>',
  'data' : {
    'format' : 'json',
    'slug' : this.value,
    'isAjax' : '1',
    'pro_id' : <?php echo $this->pro_id ?>,
    'ser_id' : <?php echo $this->ser_id ?>,
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

scriptJquery('#service_slug').on('keyup',function(){
  
  (scriptJquery.ajax({
  'dataType': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'edit'), 'default', true) ?>',
  'data' : {
    'format' : 'json',
    'slug' : this.value,
    'isAjax' : '1',
    'pro_id' : <?php echo $this->pro_id ?>,
    'ser_id' : <?php echo $this->ser_id ?>,
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


<script type="text/javascript">

  var servicData = <?php echo $jsondata = json_encode(Engine_Api::_()->getItem('sitebooking_ser', $this->ser_id)->toArray());?>;
  window.addEventListener('DOMContentLoaded', function(){
    if(servicData['first_level_category_id'] != 0 && servicData['first_level_category_id'] != null) {
    sitebooking_addOptions(scriptJquery('#category_id-wrapper1').value); 
    scriptJquery("#first_level_category_id").value = servicData['first_level_category_id'];    
    }
    if(servicData['second_level_category_id'] != 0 && servicData['second_level_category_id'] != null) {
    sitebooking_addSubOptions(scriptJquery('#first_level_category_id').value); 
    $("second_level_category_id").value = servicData['second_level_category_id'];    
    }
    var profile_type = getProfileType(scriptJquery('#category_id-wrapper1').value);
    if(profile_type == 0) profile_type = '';
    scriptJquery('#0_0_1').value = profile_type;
    changeFields(scriptJquery('#0_0_1'));
  });

</script>
