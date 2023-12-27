<!-- sub_sub_category select element -->
<div id="second_level_category_id-wrapper" class="form-wrapper" style="display:none">

    <div id="second_level_category_id-label" class="form-label">
        <label for="second_level_category_id" class="optional">Sub-Category-2</label>
    </div>

    <div id="second_level_category_id-element" class="form-element">
        <select name="second_level_category_id" id="second_level_category_id"></select>
    </div>

</div>    

<script type="text/javascript">

    function show() {
     	var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");
     	sub_sub_categories_id.style.display = "none";
    }  

    function sitebooking_addSubOptions(element_value) {

    	console.log(element_value);

    	var data = <?php echo $jsondata = json_encode(Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray());?>

        var categories = [];

        //removing select options
        scriptJquery("#second_level_category_id").find('option').each(function(el) {
        el.parentNode.removeChild(el);
        });

        for(let i = 0; i < data.length; i++)
        {
        	if(data[i]['second_level_category_id'] == element_value){
        		categories.push({
	        				"category_id" : data[i]['category_id'],
	        				"category_name" : data[i]['category_name']
						}
        		);
        	}

        }

        var sub_sub_categories_id = document.getElementById("second_level_category_id-wrapper");

        if(categories.length > 0)
        {
            sub_sub_categories_id.style.display = "block";
            for(let i = 0; i < categories.length; i++ )
            {
                var x = document.getElementById("second_level_category_id");
                var option = document.createElement("option");
                option.value= categories[i]['category_id'];
                option.text = categories[i]['category_name'];
                x.add(option);
            }
        }
        else
        {
            sub_sub_categories_id.style.display = "none";
        }   
    }

</script>    