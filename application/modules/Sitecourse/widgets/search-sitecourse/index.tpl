
<div class="seaocore_searchform_criteria <?php if ($this->viewType == 'horizontal'): ?>seaocore_searchform_criteria_horizontal<?php endif; ?>">
  <?php if( $this->form ): ?>
    <?php echo $this->form->render($this) ?>
  <?php else: ?>
    <?php echo "not a form"; ?>
  <?php endif ?>
</div>

<script type="text/javascript">

  
 

  function addOption(selectbox,text,value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    if(optn.text != '' && optn.value != '') {
      scriptJquery('#subcategory_id').css('display','block')
      scriptJquery('#subcategory_id-label').css('display','block')
      selectbox.options.add(optn);
    }
    else {
      scriptJquery('#subcategory_id').css('display','none')
      scriptJquery('#subcategory_id-label').css('display','none')
      selectbox.options.add(optn);
    }
  }

  function clear(ddName)
  { 
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 
    } 
  }



  function changeSubCategory(){
    let parentCategory = document.getElementById('category_id').value;
    let url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitecourse_general', true);?>';
    
    let request = en4.core.request.send(scriptJquery.ajax({
      url : url,
      data : {
        format : 'json',
        parent_category : parentCategory
      },
      success : function(responseJSON) {
        const subcats = responseJSON.subcats;
        console.log(subcats);
        let hasSubCat = 0;
        clear('subcategory_id');
        for(let key in subcats){
          if (subcats.hasOwnProperty(key)) {
            ++hasSubCat;
            value = subcats[key];
            addOption(scriptJquery('#subcategory_id')[0],value, key);
          }
        }

        if(!hasSubCat){
          scriptJquery('#subcategory_id').css('display','none')
          scriptJquery('#subcategory_id-label').css('display','none')
        }
      }
    }));
  }


  if(scriptJquery('#subcategory_id'))
    scriptJquery('#subcategory_id').css('display','none')
  if(scriptJquery('#subcategory_id-label'))
    scriptJquery('#subcategory_id-label').css('display','none')
</script>
