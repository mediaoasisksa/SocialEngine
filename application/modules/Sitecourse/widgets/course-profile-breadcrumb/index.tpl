
<div class="sitecourse_profile_breadcrumb">
  <?php 
      $temp_general_url = $this->url(array(),'sitecourse_general', false );
    
      if($this->category_name):
        $temp_general_category = $this->url(array(), "sitecourse_general");
        $categoryname = Engine_Api::_()->getItem('sitecourse_category', $this->sitecourse->category_id)->getCategorySlug();
        $category_id = $this->sitecourse->category_id;
        $temp_general_category .= "?categoryname={$categoryname}&category_id={$category_id}";
      endif;
      
      if(!empty($this->subcategory_name)):
        $temp_general_subcategory = $this->url(array(),'sitecourse_general', false );
        $category_id = $this->sitecourse->category_id;
        $categoryname = Engine_Api::_()->getItem('sitecourse_category', $this->sitecourse->category_id)->getCategorySlug();
        $subcategoryname = Engine_Api::_()->getItem('sitecourse_category', $this->sitecourse->subcategory_id)->getCategorySlug();
        
        $temp_general_subcategory .= "?categoryname={$categoryname}&category_id={$category_id}&subcategoryname={$subcategoryname}";
      endif;
      
  ?><h2>
  <a href="<?php echo $temp_general_url;?>">
    <?php echo $this->translate("Browse Courses");?>
  </a>
  <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
  <?php if ($this->category_name): ?>
    <a href="<?php echo $temp_general_category; ?>"><?php echo $this->translate($this->category_name); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if (!empty($this->subcategory_name)): ?>
      <a href="<?php echo $temp_general_subcategory; ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
      <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php echo $this->sitecourse->getTitle(); ?></h2>
</div>

<style type="text/css">

.sitecourse_profile_breadcrumb{
  font-size:11px;
  margin-bottom:10px;
}
.sitecourse_profile_breadcrumb .brd-sep{
  margin:0 3px;
}

</style>
