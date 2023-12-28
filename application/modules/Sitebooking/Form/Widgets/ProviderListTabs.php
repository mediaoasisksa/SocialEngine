<?php

class Sitebooking_Form_Widgets_ProviderListTabs extends Engine_Form
{
  
  public function init()
  {   
    $this->addElement('Text', 'limit', array(
      'label' => 'Limit',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor()
      ),
      'validators' => array(
            array('Int', true),
            new Engine_Validate_AtLeast(1),
      ),
    ));

    $listing = array();
    $listing["featured"] = "Featured";
    $listing["sponsored"] = "Sponsored";
    $listing["hot"] = "Trending";
    $listing["newlabel"] = "New";
    $listing["verified"] = "Verified";
    $listing["likeCount"] = "Most Liked";
    $listing["commentCount"] = "Most Commented";
    $listing["reviewCount"] = "Most Reviewed";
    $listing["rating"] = "Most Rated";
    $listing["creationDate"] = "Recent";


    $this->addElement('MultiCheckbox', 'list_id', array(
      'label' => 'List',
      'multiOptions' => $listing,
      // 'required' => true,
    ));

    $view = array();
    $view["list"] = "List View";
    $view["grid"] = "Grid View";

    $this->addElement('MultiCheckbox', 'view_id', array(
      'label' => 'View',
      'description' => "Note: If both the view or only grid view is selected then it will show filtered providers in grid view at first. Please note when no 'List' is selected then the view buttons (list or grid view) will not appear on user side.",
      'multiOptions' => $view,
      // 'required' => true,
    ));
    
    $this->view_id->getDecorator("Description")->setOption("placement", "append");


    $table = Engine_Api::_()->getItemTable('sitebooking_category')->fetchAll()->toArray();

    $categories = array();    

    $categories["-1"] = null;
    foreach ($table as $key => $value) { 
      if($value['first_level_category_id'] == 0 && $value['second_level_category_id'] == 0)
        $categories[$value['category_id']] = $value['category_name'];
    }

    $this->addElement('Select', 'category_id', array(
      'label' => 'Categories',
      'multiOptions' => $categories,
      'required' => true,
    ));
    
  }

}