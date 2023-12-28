<div class="headline">
	<h2>
		<?php echo $this->translate('Courses');?>
	</h2>
	<?php if($this->navigation && count($this->navigation) > 0 ): ?>
		<div class="tabs">
			<?php
        // Render the menu
			echo $this->navigation()
			->menu()
			->setContainer($this->navigation)
			->render();
			?>
		</div>
	<?php endif; ?>
</div>
</br>




<?php if (!$this->canCreate): ?>
	<div class="tip">
		<span><?php echo $this->translate('You have already created the maximum number of courses allowed.'); ?></span> 
	</div>
	<br/>
<?php else: ?>

	<?php 
	echo $this->form->render($this);
	?>
<?php endif; ?>

<script type="text/javascript">
	scriptJquery('#subcategory_id').css('display','none');
	scriptJquery('#subcategory_id-label').css('display','none');


	function replacetext(element){
		let spanElement= document.getElementById('course_url_address');
		spanElement.textContent=element.value;
	}




	function checkUrl(element){
		let url = '<?php echo $this->url(array('action' => 'validateurl'), 'sitecourse_general', true);?>';	
		let msgElem = document.getElementById('urlMessage');

		
		if(element.value.length>=3){
			
			scriptJquery.ajax({
				type: 'post',
				url : url,
				data : {
					format : 'json',
					url: element.value
				},
				success : function(responseJSON) {
					if(responseJSON.message=='URL is Available'){
						msgElem.style='color:green !important';
						msgElem.innerHTML= `<i class="fas fa-check"></i> ${responseJSON.message}`;
					} else {
						msgElem.style='color:red !important';
						msgElem.innerHTML= `<i class="fa fa-times"></i> ${responseJSON.message}`;
					}
				}
			});
		}else{
			msgElem.textContent="Length should be minimum of 3 characters";
		}
	}

	function insertAfter(referenceNode, newNode) {
		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
	}
	function addOption(selectbox,text,value )
	{
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		if('<?php echo $this->subcategory_id ;?>' == value){
			optn.selected = true;
		}
		if(optn.text != '' && optn.value != '') {
			scriptJquery('#subcategory_id').css('display','block');
			scriptJquery('#subcategory_id-label').css('display','block');
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



	function changeSubCategory(categoryId){
		if(categoryId == 0){
			clear('subcategory_id');
			scriptJquery('#subcategory_id').css('display','none');
			scriptJquery('#subcategory_id-label').css('display','none');
			return;
		}
		let url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitecourse_general', true);?>';
		scriptJquery.ajax({
			url : url,
			data : {
				format : 'json',
				parent_category : categoryId
			},
			success : function(responseJSON) {
				const subcats = responseJSON.subcats;
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
					scriptJquery('#subcategory_id').css('display','none');
					scriptJquery('#subcategory_id-label').css('display','none');
				}
			}
		});
		//request.send();
	}


	const categoryElem = scriptJquery('#category_id');

	if(categoryElem){
		changeSubCategory(categoryElem.val());
	}






</script>
