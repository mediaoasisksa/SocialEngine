<?php
  echo $this->partial('_jsSwitch.tpl', 'fields', array( 
  ))
?>
<?php $this->tinyMCESEAO()->addJS();?>

<?php $provider = Engine_Api::_()->getItem('sitebooking_pro', $this->pro_id);?>
<?php if($this->status == 0):?>
  <div class="tip"><span><?php echo $this->translate('This provider has not been published yet.You need to ');?>
    <?php echo $this->htmlLink(array('action' => 'edit','route' => 'sitebooking_provider_specific','pro_id'=>$this->pro_id,'reset' => true,), $this->translate('publish it<a href="%1$s"></a> before you can create its services.'), array('class' => '',)); ?>
  </span></div>
  <?php return;?>
<?php endif;?>
<?php if($this->notApprove == 0) : ?>
  <div class="tip"><span>You can't create any services until Admin approves you. </span></div>
  <?php return;?>
<?php endif;?>

<?php if($provider->enabled == 0) : ?>
  <div class="tip"><span>Enable this Provider [<?php echo $provider->title; ?>] to create its services. </span></div>
  <?php return;?>
<?php endif;?>

<?php 

if($this->count < $this->quota || $this->quota == 0)
{
  echo $this->form->render($this);
}
else
{?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already created the maximum number of services allowed.');?>
      <?php echo $this->translate('If you would like to create a new service, please delete an old one first.');?>
    </span>
  </div>
<?php }

?>

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
      'language' => $this->language,
      'directionality' => $this->directionality,
      'upload_url' => '')); ?>
    }

    scriptJquery('.sitebooking_main_provider_manage').parents().addClass('active');

    var defaultProfileId = '<?php echo '0_0_1' ?>' + '-wrapper';
      if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
      scriptJquery('#'+defaultProfileId).css('display', 'none');
      }
  }); 

scriptJquery('#service_slug').on('focus',function(){
  if(this.value == ''){
  this.value = scriptJquery('#service_title').val();
  }
  (scriptJquery.ajax({
  'dataType': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'create'), 'default', true) ?>',
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

scriptJquery('#service_slug').on('keyup',function(){
  
  (scriptJquery.ajax({
  'dataType': 'json',
  'url' : '<?php echo $this->url(array('module' => 'sitebooking', 'controller' => 'service', 'action' => 'create'), 'default', true) ?>',
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
      scriptJquery('#slug_error_msg').innerHTML = "This URL is already taken."

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

  
</script>






