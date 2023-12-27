<?php

class Sitebooking_Form_Widgets_ServiceSidelisting extends Engine_Form
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
            new Engine_Validate_AtLeast(5),
      ),
    ));

    $listing = array();
    $listing["featured"] = "Featured";
    $listing["sponsored"] = "Sponsored";
    $listing["newlabel"] = "New";
    $listing["hot"] = "Trending";
    $listing["like_count"] = "Most Liked";
    $listing["comment_count"] = "Most Commented";
    $listing["review_count"] = "Most Reviewed";
    $listing["rating"] = "Most Rated";

    $this->addElement('Select', 'list_id', array(
      'label' => 'List',
      'multiOptions' => $listing,
      'required' => true,
    ));
    
  }

}
