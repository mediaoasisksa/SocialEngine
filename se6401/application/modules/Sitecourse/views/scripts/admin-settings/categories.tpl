<h2>
	<?php echo $this->translate('Course Builder / Learning Management Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs clr'>
		<?php
    // Render the menu
    //->setUlClass()
		echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
<?php endif; ?>
<?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
<iframe id='ajaxframe' name='ajaxframe' style='display: none;' src='javascript:false;'></iframe>

<div class='settings clr'>
	<h3><?php echo $this->translate("Categories") ?></h3>
	<p class="description">
		<?php echo $this->translate("Manage_Category_Description") ?>
	</p>
	
</div>
<div class="clr mtop10">
	<div class="sitecourse_categories_left fleft">   
		<div class="sitecourse_cat_add"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array(
			'class' => 'smoothbox buttonlink',
			'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
		</div>
		<?php if(count($this->categories)>0):?>

			<div id='categories' class="sitecourse_cat_list_wrapper clr">
				<?php foreach ($this->categories as $category): ?>

					<div id='cat-<?php echo $category->category_id;?>' class="sitecourse_cat_list">
						<input type="hidden" value="0" id='hide-cat-<?php echo $category->category_id;?>'>
						
						<div class="sitecourse_cat">								
							<a class="sitecourse_cat_showhide"><img onclick='toggleSubCategory(<?=$category->category_id;?>,this)' src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/plus.png' border='0' /></a>
							<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/folder_open_yellow.gif' border='0' class='sitecourse_cat_handle' />
							<div class="sitecourse_cat_det <?php if ($this->category_id == $category['category_id']): ?> sitecourse_cat_selected <?php endif; ?>">

								<span class="sitecourse_cat_det_options">
									<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
										'class' => 'smoothbox',
									)) ?> 									
								</span>
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'edit-category', 'id' =>$category->category_id), $category->category_name, array(
									'class' => 'smoothbox',
								)) ?>
							</div>
							<div class='sitecourse_cat_new'>
								<?php echo $this->translate("Sub Categories"); ?>
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'add-subcategory', 'id' =>$category->category_id), $this->translate('[Add New]'), array(
									'class' => 'smoothbox',
									)) ?></div>
								</div>

								<div id='sub-cat-<?php echo $category->category_id;?>' class="sitecourse_sub_cat_wrapper">
									<?php if(count($this->subCategories)>0): ?>
										<?php foreach ($this->subCategories as $subCategory): ?>
											<?php if($subCategory->cat_dependency == $category->category_id): ?>
												<div class="sitecourse_cat">
													<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/folder_open_green.gif' border='0' class='sitecourse_cat_handle' />
													<div class="sitecourse_cat_det <?php if ($this->category_id == $subcategory['category_id']): ?> sitecourse_cat <?php endif; ?>">
														<span class="sitecourse_cat_det_options">										
															<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'delete-subcategory', 'id' =>$subCategory->category_id), $this->translate('delete'), array(
																'class' => 'smoothbox',
															)) ?>
														</span>
														<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitecourse', 'controller' => 'settings', 'action' => 'edit-subcategory', 'id' =>$subCategory->category_id), $subCategory->category_name, array(
															'class' => 'smoothbox',
														)) ?>
													</div>
													<br/>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
									<?php else: ?>
									<?php endif; ?>

								</div>
							</div>
						<?php endforeach; ?>
					</div>

				<?php else:?>
					<br/>
					<div class="tip">
						<span><?php echo $this->translate("There are currently no categories.") ?></span>
					</div>
				<?php endif;?>
				<br/>


			</div>
		</form>
	</div>
</div>
</div>




<script type="text/javascript">
  // show or id the sub category
  function toggleSubCategory(id,elem){
  	const hideStatusElem = document.getElementById(`hide-cat-${id}`);
  	const hideStatus = +(hideStatusElem.value);
  	const subCatElem = document.getElementById(`sub-cat-${id}`);
    // hide the subcategory
    if(hideStatus){
    	hideStatusElem.value = 0;
    	subCatElem.style.display = 'none';
    	elem.src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/plus.png';
    }
    //show the subcategory
    else{
    	hideStatusElem.value = 1;
    	subCatElem.style.display = 'block';
    	elem.src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitecourse/externals/images/minus.png';
    }
  }
</script>



<script type="text/javascript">
	function createSortable(divId, handleClass)
	{
	// 	new Sortables($(divId), {handle: handleClass, onComplete: function() {
	// 		changeorder(this.serialize(), divId);
	// 	}
	// });
	const ele = scriptJquery('#' + divId);
	ele.sortable({
		handle: handleClass,
		update: function(event, ui) {
			var serial = [];
			ele.children().each(function() {
				serial.push(this.id);
			});
			changeorder(serial, divId);
		}	
	});
}

window.addEventListener('DOMContentLoaded', function() {
	createSortable('categories', 'img.sitecourse_cat_handle');
});

    //THIS FUNCTION CHANGES THE ORDER OF ELEMENTS
    function changeorder(sitecourseorder, divId)
    {
    	document.getElementById('ajaxframe').src = '<?php echo $this->url(array('module' => 'sitecourse', 'controller' => 'settings', 'action' => 'categories'), 'admin_default', true) ?>?task=changeorder&sitecourseorder=' + sitecourseorder+ '&divId=' + divId;
    }
  </script>
