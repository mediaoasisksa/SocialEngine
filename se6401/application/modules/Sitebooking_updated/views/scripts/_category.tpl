<!-- sub_category select element -->
<div id="first_level_category_id-wrapper" class="form-wrapper" style="display:none">
  
  <div id="first_level_category_id-label" class="form-label">
    <label for="first_level_category_id" class="optional">Sub-Category-1</label>
  </div>

  <div id="first_level_category_id-element" class="form-element">
    <select name="first_level_category_id" id="first_level_category_id" onchange='sitebooking_addSubOptions(this.value)'>
    </select>
  </div>

</div>

<div id="second_level_category_id-wrapper" class="form-wrapper" style="display:none">

  <div id="second_level_category_id-label" class="form-label">
    <label for="second_level_category_id" class="optional">Sub-Category-2</label>
  </div>

  <div id="second_level_category_id-element" class="form-element">
    <select name="second_level_category_id" id="second_level_category_id"></select>
  </div>

</div>  

<script type="text/javascript">

  function sitebooking_addOptions(element_value) {

    // Hide sub_sub_category
    var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");
    sub_sub_categories_id.style.display = "none";


    var data = <?php echo $jsondata = json_encode(Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray());?>

    var categories = [{"category_id" : "-1", "category_name" : " "}];

    //removing select options
    scriptJquery("#first_level_category_id").find('option').each(function(el) {
    el.parentNode.removeChild(el);
    });


    //Pushing data into categories array
    for(let i = 0; i < data.length; i++)
    {
      if(data[i]['first_level_category_id'] == element_value && data[i]['second_level_category_id'] == 0){
        categories.push({
              "category_id" : data[i]['category_id'],
              "category_name" : data[i]['category_name']
            }
        );
      }

    }

    var sub_categories_id = document.getElementById("first_level_category_id-wrapper");

    if(categories.length > 1)
    {
      sub_categories_id.style.display = "block";
      for(let i = 0; i < categories.length; i++ )
      {
        var x = document.getElementById("first_level_category_id");
        var option = document.createElement("option");
        option.value= categories[i]['category_id'];
        option.text = categories[i]['category_name'];
        x.add(option);
      }
    }
    else
    {
      sub_categories_id.style.display = "none";
    }   

  }

</script>